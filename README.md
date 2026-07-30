# Sillage - website

A small marketing site for the Sillage sensory-identity consultancy: **Home, Markets, About,
Contact** and a **Privacy** page. It is **plain HTML and hand-written CSS** with a little vanilla
JavaScript - no framework, no build step, no npm. You edit it by opening a file in any text
editor, and preview it by double-clicking `index.html`.

## Files

| File | What it is |
|------|------------|
| `index.html` | Homepage - hero, "why it matters", 5-step method, research, selected work. |
| `about.html` | The team, how we operate, what we've done, **where we work**, and **contact + enquiry form**. Markets and Contact were merged in here in v4. |
| `markets.html` | Redirect stub only. Sends visitors to `about.html#markets`; kept so old links still work. |
| `contact.html` | Redirect stub only. Sends visitors to `about.html#contact`; kept so old links still work. |
| `privacy.html` | Privacy policy (required - this is a live UK/EU business). |
| `styles.css` | All the styling. The colour palette lives at the very top. |
| `booking.js` | **One line** that sets every "Book a visit" button - see edit #1. |
| `motion.js` | Scroll-reveal. Adds a class when an element scrolls into view; the animating is all in `styles.css`. |
| `favicon.svg` | The browser-tab icon (the blotter mark). |
| `og-image.png` | The picture shown when the site is shared on social media. |
| `assets/img/team/` | Team photos. `group.jpg`, `headshot-curly.jpg` (Kevin), `presenting.jpg` (Prithvish), `headshot-gold-glasses.jpg` (Alexandros) are live. `portrait-1/2/3.jpg` are leftovers from the v3 mockup and are no longer referenced. |

---

## How to preview (no tools needed)
1. Open the `sillage-website` folder and **double-click `index.html`.** It opens in your browser.
2. Edit a file, **save**, then **refresh** the browser to see the change.

---

## Common edits - exact file & line

### 1. Change the booking link (do this before launch)
- **File:** `booking.js`, first line: `var BOOKING_URL = "mailto:hello@sillage.example?subject=Diagnostic%20visit";`
- Replace `hello@sillage.example` with your real **non-personal, brand-domain** address, **or**
  paste a booking-form link in place of the whole `mailto:…` string.
- This is the **single place** - every "Book a visit" / "Get an evaluation" button on every page updates.

### 2. Change a colour
- **File:** `styles.css`, the `:root { … }` block at the top - the **single source of truth** for colour.
- ⚠️ Three rules to keep (v4):
  1. **`--terracotta #EE9560` is SVG stroke/fill only.** It fails AA as text.
  2. **`--terracotta-deep #B85C2B` is allowed on exactly one piece of type**, the wordmark slogan.
     No terracotta buttons, grounds, eyebrows or bullets; that was removed in v4.
  3. **Grounds are forest (hero + footer) or ivory `#FBF8F1`.** Section eyebrows are `--forest`,
     Title Case, never uppercased.

### 3. Edit the words
- Change text **between** the tags (e.g. the homepage headline lives inside `<h1>…</h1>`). Save, refresh.

### 4. Edit the team (About page)
- **File:** `about.html`, the `TEAM CARDS` block. All three cards are filled: Kevin Kegel (Reading),
  Prithvish Patil (Milton Keynes), Alexandros Moschopoulos (Thessaloniki), each with course, society
  role, remit and languages.
- ⚠️ Courses and society roles are **facts about real people**. They came from each founder's own
  LinkedIn plus the owner's corrections (checked 26 July 2026). When a course finishes or a role
  changes, update the card; don't let it go stale.
- Photos go in `assets/img/team/` at 4:5 for portraits and 3:2 for the group. Missing photos show a
  tasteful `[Photo]` tile, never a broken icon.
- To add a person, copy a whole `<article class="card">` and keep the `data-anim-delay` steps 80ms apart.

### 5. Set the contact names
- **File:** `about.html`, the `.cities` list in the Contact section. Each city names its lead
  consultant and must stay in step with the team cards above.

### 6. The contact form
- By default the form **opens the visitor's own email app** (no third-party service, no cookies).
- To use a hosted form handler instead, set `FORM_ENDPOINT` in the `<script>` at the bottom of
  `about.html` to its URL - and add a consent/DPA note to the privacy policy first.

### 7. Add research (homepage)
- **File:** `index.html`, `SUPPORTING RESEARCH`. Figures and citations are shown **verbatim** from
  sources the owner supplied. Never paraphrase a statistic or guess a date; add the full citation.

### 8. Fill in the privacy policy
- **File:** `privacy.html` - replace every `[BRACKETED]` field before launch.

---

## Publishing

The site is live on **two** hosts, which serve identical content. Either one can carry the
site alone, so if one breaks the other is the fallback.

| Host | URL | How it updates |
|------|-----|----------------|
| GitHub Pages | https://alexmoschop.github.io/sillage-website/ | Automatic, about a minute after you push to `master`. |
| Hostinger | https://sillage.moschopoulos.com | One command, see below. |

### To publish a change
1. Save your edit, then commit and push:
   ```
   git add -A && git commit -m "describe your change" && git push
   ```
   GitHub Pages picks it up on its own.
2. Then update Hostinger:
   ```
   bash ~/.claude/skills/sillage-deploy/deploy.sh --push
   ```
   That builds the upload straight from `master`, sends it, and checks every page and image
   arrived intact. Run it with no arguments first if you want to see what it would send
   without sending anything.

### Two rules that matter
- ⚠️ **Never upload this folder by dragging it onto a host, and never edit files in
  Hostinger's File Manager.** Dragging the folder publishes `context.md` (private working
  notes) and the whole `.git` history to the open internet. Editing on the server makes the
  two copies disagree, and then neither one is the real version. `master` is the only source
  of truth, and publishing always goes one way, from `master` out to the hosts.
- The live Hostinger copy also carries a `robots.txt` and an `.htaccess` that are **not** in
  this repo, on purpose. They keep the site out of search results while the booking address
  and privacy policy are still unfinished. The deploy command adds them for you every time.

*(An older draft sits in a separate `sillage-site` folder. It is not this project. Do not
publish it.)*

---

## Notes for whoever maintains this

- **Design language:** the site's signature is the blueprint "airflow & diffusion survey" drawing
  (paper sheet, forest walls, terracotta airflow arrows, dotted sage diffusion rings). It's inline
  SVG - no images, no libraries. Reuse that language for any new illustration.
- **Fonts:** the display face is *Source Serif 4, upright*. It is **not** shipped as a font file; the
  stack falls back to Georgia (an upright serif on Windows/macOS/Android) so it looks deliberate. To
  match exactly, self-host Source Serif 4 (SIL OFL): put the `.woff2` in `assets/fonts/` and add an
  `@font-face` rule naming `"Source Serif 4"` at the top of `styles.css` - it'll be picked up automatically.
- **No cookies / no tracking.** If you add analytics, a booking embed, CDN fonts, or a hosted form
  handler, you must add a cookie/consent notice and a data-processing agreement, and update `privacy.html`.
- **Accessibility:** semantic HTML, heading order, alt text, visible focus, keyboard-navigable nav and
  a skip link are in place; target is WCAG AA. **Lighthouse was not run** in this build - run it in
  Chrome DevTools before launch rather than assuming a score.

---

## Appendix - optional photography prompt pack
The site is intentionally photo-light (blueprint drawings, not stock). If you ever want real
photography for the "selected work" area, these prompts pair with this style suffix:

> …photorealistic interior photograph, soft natural light, warm neutral palette with deep green and
> pale terracotta accents, calm and uncluttered, no people, no visible brand names or logos, no text,
> shallow depth of field.

1. Boutique wellness studio treatment room, linen and pale wood, morning light
2. Small spa relaxation lounge, stone basin, eucalyptus stems
3. Independent hotel lobby, quiet seating corner, brass and dark green details
4. Hair salon interior, mirrors and warm sconce lighting, closed hour
5. Clinic waiting room, soft furnishings, plants, daylight
6. Boutique retail interior, open shelving, natural materials

*(Not wired into any layout - the current work tiles are blueprint drawings by design.)*
