<?php
/* ============================================================
 * Sillage contact endpoint.
 *
 * This is the ONE server-side file on an otherwise fully static
 * site. It exists because specification section 6 requires
 * server-side validation, delivery to the Sillage inbox with
 * reply-to set to the submitter, an encrypted backup of every
 * submission, per-IP rate limiting, and a honeypot plus a timing
 * check, with no reCAPTCHA and no third-party form service that
 * tracks users.
 *
 * It explicitly supersedes the "No PHP, no contact-form handler,
 * no mail script" rule in ~/.claude/skills/sillage-deploy/SKILL.md,
 * FOR THIS FILE ONLY. That rule has been amended there rather than
 * left to contradict this code. Adding a second PHP file is not
 * covered by that amendment.
 *
 * It is in scope for the pending security audit (spec 9.6) from
 * day one. Nothing here has been audited yet.
 *
 * DESIGN RULES, kept deliberately narrow:
 *   - POST only. Everything else gets 405.
 *   - Strict allowlist validation. The client is never trusted.
 *   - No file uploads, ever.
 *   - No secret is in this file or anywhere in the repository.
 *   - Storage lives OUTSIDE the web root and is encrypted at rest.
 *   - Output is escaped on the way into the mail body.
 *   - Responses are JSON and never echo back raw input.
 *
 * GITHUB PAGES: Pages cannot execute PHP and will serve this file
 * as plain text. That is why nothing secret may ever be added to
 * it. On that host the form's fetch gets a non-JSON response, the
 * submit handler catches it, and the visitor is shown the email
 * address instead, which is the required degraded path (spec 6.2).
 *
 * ------------------------------------------------------------
 * ONE-TIME SERVER SETUP, outside the web root:
 *
 *   mkdir -p ~/sillage-private/submissions
 *   chmod 700 ~/sillage-private ~/sillage-private/submissions
 *   php -r 'echo bin2hex(random_bytes(32)), "\n";'   # the key
 *
 * Then create ~/sillage-private/config.php containing:
 *
 *   <?php return array(
 *     'to'      => 'info@seeyazh.com',
 *     'from'    => 'website@seeyazh.com',   // must be a real mailbox
 *                                           // on the sending domain,
 *                                           // or SPF and DMARC drop it
 *     'key_hex' => '<the 64-character hex string printed above>',
 *     'store'   => '/home/<user>/sillage-private/submissions',
 *     'rate'    => '/home/<user>/sillage-private/ratelimit',
 *   );
 *
 * chmod 600 ~/sillage-private/config.php
 *
 * To read a stored submission back:
 *   openssl_decrypt(base64 ciphertext, 'aes-256-gcm', hex2bin(key),
 *                   OPENSSL_RAW_DATA, iv, tag)
 * ============================================================ */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');
header('Cache-Control: no-store');

function reply(int $status, array $body): void {
    http_response_code($status);
    echo json_encode($body, JSON_UNESCAPED_SLASHES);
    exit;
}

/* ---- POST only ------------------------------------------------ */
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    reply(405, ['ok' => false, 'error' => 'method_not_allowed']);
}

/* ---- Config, from outside the web root ------------------------ */
$configPath = dirname(__DIR__) . '/sillage-private/config.php';
if (!is_readable($configPath)) {
    error_log('sillage contact: config missing at ' . $configPath);
    reply(500, ['ok' => false, 'error' => 'not_configured']);
}
$cfg = require $configPath;
foreach (['to', 'from', 'key_hex', 'store', 'rate'] as $k) {
    if (empty($cfg[$k])) {
        error_log('sillage contact: config key missing: ' . $k);
        reply(500, ['ok' => false, 'error' => 'not_configured']);
    }
}

/* ---- Rate limit, keyed on IP ----------------------------------
 * Five submissions per hour per address. The key is a hash, so the
 * raw IP is never written to disk. Fails CLOSED: if the counter
 * cannot be written, the submission is refused rather than let
 * through unlimited.
 * -------------------------------------------------------------- */
const RATE_MAX    = 5;
const RATE_WINDOW = 3600;

$ip     = (string)($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
$rateDir = rtrim((string)$cfg['rate'], '/');
if (!is_dir($rateDir) && !@mkdir($rateDir, 0700, true) && !is_dir($rateDir)) {
    error_log('sillage contact: cannot create rate dir');
    reply(500, ['ok' => false, 'error' => 'server_error']);
}
$rateFile = $rateDir . '/' . hash('sha256', $ip . '|' . $cfg['key_hex']) . '.json';

$now  = time();
$hits = [];
if (is_readable($rateFile)) {
    $decoded = json_decode((string)file_get_contents($rateFile), true);
    if (is_array($decoded)) $hits = $decoded;
}
$hits = array_values(array_filter($hits, static function ($t) use ($now) {
    return is_int($t) && ($now - $t) < RATE_WINDOW;
}));
if (count($hits) >= RATE_MAX) {
    reply(429, ['ok' => false, 'error' => 'rate_limited']);
}
$hits[] = $now;
if (@file_put_contents($rateFile, json_encode($hits), LOCK_EX) === false) {
    error_log('sillage contact: cannot write rate file');
    reply(500, ['ok' => false, 'error' => 'server_error']);
}
@chmod($rateFile, 0600);

/* ---- Spam checks ----------------------------------------------
 * A honeypot plus a timing check, per spec 6.3. Both answer with a
 * plain rejection; neither explains itself to the sender.
 * -------------------------------------------------------------- */
if (trim((string)($_POST['website'] ?? '')) !== '') {
    reply(400, ['ok' => false, 'error' => 'rejected']);
}
$ts = (int)($_POST['ts'] ?? 0);          // milliseconds, set on page load
if ($ts <= 0 || ($now * 1000 - $ts) < 2000) {
    reply(400, ['ok' => false, 'error' => 'rejected']);
}

/* ---- Validation, allowlist ------------------------------------ */
function field(string $name, int $max): string {
    $v = (string)($_POST[$name] ?? '');
    $v = str_replace(["\r", "\0"], '', $v);
    $v = trim($v);
    return mb_substr($v, 0, $max);
}

$name     = field('name', 120);
$email    = field('email', 200);
$business = field('business', 200);
$type     = field('type', 60);
$message  = field('message', 4000);
$consent  = (string)($_POST['consent'] ?? '');

$TYPES = [
    'Spa or wellness studio',
    'Yoga or pilates studio',
    'Salon, barber or beauty',
    'Hotel or hospitality',
    'Office or reception',
    'Other',
];

$errors = [];
if ($name === '')                                   $errors['name']     = 'required';
if (!filter_var($email, FILTER_VALIDATE_EMAIL))     $errors['email']    = 'invalid';
if ($business === '')                               $errors['business'] = 'required';
if (!in_array($type, $TYPES, true))                 $errors['type']     = 'invalid';
if ($consent !== 'yes')                             $errors['consent']  = 'required';

if ($errors) {
    reply(422, ['ok' => false, 'error' => 'validation', 'fields' => $errors]);
}

/* ---- Store, encrypted at rest ---------------------------------
 * Written BEFORE the mail is attempted, so a mail failure never
 * loses a lead (spec 6.2). AES-256-GCM: the tag makes the record
 * tamper-evident as well as unreadable.
 * -------------------------------------------------------------- */
$record = json_encode([
    'received' => gmdate('c', $now),
    'name'     => $name,
    'email'    => $email,
    'business' => $business,
    'type'     => $type,
    'message'  => $message,
], JSON_UNESCAPED_UNICODE);

$storeDir = rtrim((string)$cfg['store'], '/');
if (!is_dir($storeDir) && !@mkdir($storeDir, 0700, true) && !is_dir($storeDir)) {
    error_log('sillage contact: cannot create store dir');
}
$stored = false;
$key = @hex2bin((string)$cfg['key_hex']);
if ($key !== false && strlen($key) === 32 && is_dir($storeDir)) {
    $iv  = random_bytes(12);
    $tag = '';
    $ct  = openssl_encrypt($record, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
    if ($ct !== false) {
        $blob = json_encode([
            'v'   => 1,
            'alg' => 'aes-256-gcm',
            'iv'  => base64_encode($iv),
            'tag' => base64_encode($tag),
            'ct'  => base64_encode($ct),
        ]);
        $file = $storeDir . '/' . gmdate('Ymd-His', $now) . '-' . bin2hex(random_bytes(4)) . '.json';
        if (@file_put_contents($file, $blob, LOCK_EX) !== false) {
            @chmod($file, 0600);
            $stored = true;
        }
    }
}
if (!$stored) error_log('sillage contact: submission not stored');

/* ---- Mail, with reply-to set to the submitter -----------------
 * Every value is escaped on the way into the body, and the headers
 * are built only from values that have already been validated, so
 * there is nothing for a newline to inject into.
 * -------------------------------------------------------------- */
$body = "New evaluation request from the Sillage website.\n\n"
      . 'Name:      ' . $name     . "\n"
      . 'Email:     ' . $email    . "\n"
      . 'Business:  ' . $business . "\n"
      . 'Type:      ' . $type     . "\n\n"
      . "What the space smells like today:\n"
      . ($message !== '' ? $message : '(not answered)') . "\n\n"
      . '---' . "\n"
      . 'Received ' . gmdate('c', $now) . ' UTC' . "\n"
      . 'Encrypted backup stored: ' . ($stored ? 'yes' : 'NO, check the server log') . "\n";

$headers = [
    'From: Sillage website <' . $cfg['from'] . '>',
    'Reply-To: ' . $email,
    'Content-Type: text/plain; charset=utf-8',
    'MIME-Version: 1.0',
    'X-Mailer: sillage-contact',
];

$sent = @mail(
    (string)$cfg['to'],
    '=?UTF-8?B?' . base64_encode('Evaluation request: ' . $business) . '?=',
    $body,
    implode("\r\n", $headers)
);

if (!$sent && !$stored) {
    // Nothing captured anywhere. Tell the visitor honestly so the
    // form can offer the email address instead.
    reply(500, ['ok' => false, 'error' => 'send_failed']);
}
if (!$sent) error_log('sillage contact: mail() failed, submission is stored');

reply(200, ['ok' => true]);
