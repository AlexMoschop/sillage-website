# Sillage — website

A small marketing site for the Sillage sensory-identity consultancy (a homepage plus a
Markets page and a Privacy page). It is **plain HTML and hand-written CSS** — no framework,
no build step, no npm. You edit it by opening a file in any text editor, and preview it by
double-clicking `index.html`.

## Files

| File | What it is |
|------|------------|
| `index.html` | The homepage (nav, hero, method + equipment, evidence ladder, work gallery, team, footer). |
| `markets.html` | Second page: where we operate (UK · Germany · Greece). |
| `privacy.html` | The privacy policy (required — this is a live UK/EU business). |
| `styles.css` | All the styling. The colour palette lives at the very top. |
| `favicon.svg` | The little browser-tab icon (the blotter mark). |
| `og-image.png` | The picture shown when the site is shared on social media. |
| `assets/img/…` | Where your photos go — `team/`, `spaces/`, `equipment/`, `markets/`. Empty until you add them; the site shows tasteful `[Photo]` placeholders in the meantime. |

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

### 4. Add more research to the evidence "ladder"
- **File:** `index.html`, `EVIDENCE` section — the `<table class="ladder">`.
- The figures already there are from a real, owner-verified meta-analysis (Roschk &
  Hosseinpour, 2020) and are shown **verbatim**. To add another study, copy one `<tr>` row
  and set its `style="--v:NN"` to the percentage, choose the bar class
  (`ladder__bar--pos` right / `ladder__bar--neg` left / add `ladder__bar--peak` for the
  single terracotta highlight), and put the number in the `ladder__num` cell.
- **Rule (in a comment there too):** one row per claim; numbers copied **exactly** from a
  source you have read; add the full citation. **Never paraphrase a statistic or guess a date.**

### 5. Add a real testimonial (or remove the placeholder)
- **File:** `index.html`, `WORK` section.
- Replace the `[TESTIMONIAL ...]` and `[Name, role ...]` placeholders with a genuine,
  **consented, attributable** quote — or delete the whole `<figure class="testimonial ...">` block.

### 6. Fill in the team
- **File:** `index.html`, `TEAM` section.
- Each person is a `<article class="card">`. Replace `[Name]`, `[Title …]` and `[Bio …]`.
  A good bio line = **market they lead + specialism + one human sentence.**
- To add a third person, copy the whole commented "CONSULTANT CARD TEMPLATE" `<article>` and
  point its `<img src>` at a new file (see imagery below). Do **not** invent people.

### 7. Set the cities / markets
- **File:** `index.html` **and** `markets.html` — search for `Reading` and `[German&nbsp;city]`.
  Decide the UK city (**Reading or London?**), fill the **German city**, confirm **Thessaloniki**.
- In `markets.html` also confirm the **`Deutsch [confirm capability]`** line — don't claim
  German-language service until it's true.

### 8. Fill in the privacy policy
- **File:** `privacy.html` — replace every `[BRACKETED]` placeholder (legal entity name,
  address, dates, retention periods) with your real details before launch.

---

## Generating imagery

The site works and looks finished with **no photos** — every image slot shows a branded
`[Photo]` placeholder until you add a file. To add real pictures, drop files into the folders
below using the **exact file names**. Team and gallery photos get an automatic duotone
(grayscale → forest) so mixed phone-camera shots look art-directed; colour returns on hover.

| What | Put the file here | Shape |
|------|-------------------|-------|
| Team group photo | `assets/img/team/group.jpg` | wide (3:2) |
| Team portraits | `assets/img/team/member-1.jpg`, `member-2.jpg`, … | tall (4:5) |
| Spaces gallery (Work) | `assets/img/spaces/space-01.jpg` … `space-04.jpg` | wide (3:2) |
| Equipment strip (Method) | `assets/img/equipment/eq-01.jpg` … `eq-03.jpg` | square (1:1) |
| Market headers | `assets/img/markets/uk.jpg`, `de.jpg`, `gr.jpg` | very wide (21:9) |

**Export settings:** ≤ 1600 px wide, ≤ ~200 KB each (JPEG ~80% quality). Keeping under that
keeps the homepage light and fast.

### Image prompt pack

Paste any prompt below into an image generator. **Append this style line to every prompt:**

> …photorealistic interior photograph, soft natural light, warm neutral palette with deep green
> and pale terracotta accents, calm and uncluttered, no people, no visible brand names or
> logos, no text, shallow depth of field.

1. Boutique wellness studio treatment room, linen and pale wood, morning light
2. Small spa relaxation lounge, stone basin, eucalyptus stems
3. Independent hotel lobby, quiet seating corner, brass and dark green details
4. Hair salon interior, mirrors and warm sconce lighting, closed hour
5. Clinic waiting room, soft furnishings, plants, daylight
6. Boutique retail interior, open shelving, natural materials
7. Matte-black cold-air scent diffuser on a stone shelf, faint visible mist
8. Ceiling HVAC diffuser vent detail in a modern plaster ceiling, gentle light
9. Macro of paper blotter strips fanned on dark green linen
10. Amber-glass fragrance-oil bottles on a wooden tray, window light

<!-- 11. Wall-mounted ambient speaker in a spa corridor — HOLD. Music/sound imagery
     conflicts with the locked "fragrance-only" scope; confirm with the owner before using. -->

---

## What still needs your input (before real launch)

- Real **booking address** (§1) — replaces `hello@sillage.example`.
- **Team**: names, titles, bios, group photo + portraits (§6, imagery).
- **UK city**: Reading or London? (§7)
- **German city**, and whether Germany is an office or just a served market (§7).
- **Deutsch** language capability — confirm before claiming it (`markets.html`).
- A real, consented **testimonial** (§5).
- Whether **music/sound** ever enters scope (affects prompt 11 and any future copy).
- The privacy-policy **`[BRACKETED]`** fields (§8).

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
- **Anchors → future pages:** on the homepage, `Method` / `Evidence` / `Work` / `Team` are
  in-page anchors; `Markets` is a real second page (`markets.html`). A comment in `index.html`
  marks which anchors would become standalone routes if the site grows further.
- **Motion:** the hero has an animated "scent trail" on `<canvas>`. It automatically turns
  itself off for visitors who set *reduce motion* in their OS (a soft static wisp shows
  instead), and pauses when the tab is hidden or you scroll past — nothing to configure.
- **Missing photos** show a branded placeholder, never a broken-image icon. You can add photos
  one at a time; each slot fills in as soon as its file exists.
