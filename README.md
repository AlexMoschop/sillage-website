# Sillage — website

A single-page marketing site for the Sillage sensory-identity consultancy.
It is **plain HTML and hand-written CSS** — no framework, no build step, no npm.
You edit it by opening a file in any text editor, and preview it by
double-clicking `index.html`.

## Files

| File | What it is |
|------|------------|
| `index.html` | The whole homepage (nav, hero, method, evidence, work/team, footer). |
| `styles.css` | All the styling. The colour palette lives at the very top. |
| `privacy.html` | The privacy policy (required — this is a live UK/EU business). |
| `favicon.svg` | The little browser-tab icon (the blotter mark). |
| `og-image.png` | The picture shown when the site is shared on social media. |

---

## How to preview the site (no tools needed)

1. Open the `sillage-website` folder.
2. **Double-click `index.html`.** It opens in your web browser. That's the site.
3. Edit a file, **save it**, then press **refresh** in the browser to see the change.

---

## Common edits

Everything below names the **exact file** and the **exact line or token** to change.
Line numbers are approximate — search for the quoted text if they've shifted.

### 1. Change the booking link (do this before launch)
- **File:** `index.html`
- **Find:** the line `var BOOKING_URL = "mailto:hello@sillage.example?subject=Diagnostic%20visit";` (near the bottom, in the `<script>` block).
- Replace `hello@sillage.example` with your real **non-personal, brand-domain**
  address (e.g. `hello@yourdomain.com`), **or** paste a booking-form link
  (e.g. `https://cal.com/sillage`) in place of the whole `mailto:...` string.
- This is the **single place** to change it — every "Book a visit" button updates at once.
- Do **not** use a personal email address here.

### 2. Change a colour
- **File:** `styles.css`
- **Find:** the `:root {` block at the very top. This is the **single source of truth** for colour.
- Edit the raw hex values under `RAW PALETTE`, e.g. change `--terracotta: #EE9560;`.
  Every component uses these tokens, so one change updates the whole site.
- ⚠️ Keep the contrast rule: **terracotta `#EE9560` is a fill colour only.** For any
  warm **text**, use `--terracotta-deep #B85C2B` (that's what `--color-accent-text` points to).

### 3. Edit the words on the page
- **File:** `index.html`
- Change any text **between** the tags. Example — the big headline is inside
  `<h1>What your space leaves behind.</h1>` in the `HERO` section. Type new words
  between `<h1>` and `</h1>`, save, refresh.

### 4. Add the real research citation
- **File:** `index.html`, `EVIDENCE` section.
- Replace the line beginning `TODO: verify citation` with a **real, correctly-dated
  publication you have actually read** (author, title, journal/publisher, year).
  A strong candidate is noted in the comment right above it — verify it first.
- Do **not** invent or guess a reference.

### 5. Add a real testimonial (or remove the placeholder)
- **File:** `index.html`, `WORK / TEAM` section.
- Replace the `[TESTIMONIAL ...]` and `[Name, role ...]` placeholders with a genuine,
  **consented, attributable** quote — or delete the whole `<figure class="testimonial ...">` block.

### 6. Set the three cities
- **File:** `index.html` — search for `[City&nbsp;One]`. It appears twice (Team section and footer).
  Replace `[City One]` / `[City Two]` with your real cities. Thessaloniki is already set.

### 7. Fill in the privacy policy
- **File:** `privacy.html` — replace every `[BRACKETED]` placeholder (legal entity name,
  address, dates, retention periods) with your real details before launch.

---

## Publishing it (Netlify drag-and-drop — no account skills needed)

Netlify hosts a plain static folder for free, with nothing to install.

1. Go to **https://app.netlify.com/signup** and create a **free** account
   (you can sign up with your email or a GitHub login).
2. Once logged in, open the **"Sites"** page.
3. In your file explorer, open the folder that **contains** `index.html`
   (this `sillage-website` folder).
4. **Drag the whole folder** onto the box on the Netlify page that says
   *"Drag and drop your site output folder here"*. Drop it.
5. Netlify uploads it and gives you a live URL like `random-name-123.netlify.app`.
   That's your site. You can rename it in **Site settings → Change site name**.

### Re-deploying after an edit
1. Make your edit and **save** the file.
2. Go to your site on Netlify → the **"Deploys"** tab.
3. **Drag the folder** onto the deploys page again (onto the
   *"Drag and drop your site folder here"* area). The new version goes live in a few seconds.

> Tip: connect a custom domain later in **Site settings → Domain management**.

---

## Notes for whoever maintains this

- **Fonts:** the display face is specified as *Source Serif 4, upright*. The CSS uses a
  robust cross-platform stack that falls back to Georgia (an upright serif on Windows,
  macOS and Android) so it looks deliberate everywhere. To match the board exactly,
  self-host Source Serif 4 (SIL OFL) and it will be picked up automatically.
- **No cookies / no tracking** are used. If you add analytics, a booking embed, or
  CDN-hosted fonts, you must add a cookie/consent notice and a data-processing
  agreement, and update `privacy.html`.
- **Accessibility:** semantic HTML, correct heading order, alt text, visible focus
  states and keyboard-navigable nav are already in place; target is WCAG AA.
  Lighthouse was **not** run in this build — run it in Chrome DevTools before launch
  and check performance/accessibility yourself rather than assuming a score.
- **Anchors → future pages:** the nav links (`#method`, `#evidence`, `#work`, `#team`)
  are in-page anchors on this one page. A comment in `index.html` marks which would
  become real routes if the site grows to multiple pages.
