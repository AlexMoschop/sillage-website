# Sillage - website

A small marketing site for the Sillage sensory-identity consultancy: **Home**, **About**
(which carries the team, the method, the scope statement, the markets and the enquiry form)
and a **Privacy** page, plus a **404** page and three **venue** pages that are built but not
yet published. It is **plain HTML and hand-written CSS** with a little vanilla JavaScript,
and one small PHP file for the contact form. No framework, no build step, no npm.

**v7 "Ambience" rebuilt this site against a written build specification.** If anything in this
README disagrees with `styles.css`, the comment block at the top of `styles.css` wins: it is
the current statement of the colour, type and layout rules.

## Files

| File | What it is |
|------|------------|
| `index.html` | Homepage - hero, the three ambience rows with their evidence expanders, the interplay band, venues, and a testimonials block that is built but switched off. |
| `about.html` | The team, the five-stage method, the three artefacts, the **scope statement**, the markets, and the **enquiry form**. Markets and Contact merged in here in v4; the method moved here in v7. |
| `markets.html` | Redirect stub only. Sends visitors to `about.html#markets`; kept so old links still work. |
| `contact.html` | Redirect stub only. Sends visitors to `about.html#contact`; kept so old links still work. |
| `privacy.html` | Privacy policy (required - this is a live UK/EU business). |
| `styles.css` | All the styling. The colour palette lives at the very top. |
| `ui.js` | The evidence expanders, moving focus to the first form field, and the contact form validation and submit. Replaced `booking.js`, deleted in v7. |
| `contact.php` | The contact form server endpoint. **The only server-side file on the site.** See edit #1. |
| `404.html` | Styled "page not found", links back to the homepage. |
| `venues/` | Three venue pages, **built but not published**. See edit #7. |
| `motion.js` | Scroll-reveal. Adds a class when an element scrolls into view; the animating is all in `styles.css`. |
| `favicon.svg` | The browser-tab icon (the blotter mark). |
| `og-image.png` | The picture shown when the site is shared on social media. |
| `assets/img/team/` | Team photos. `group.jpg`, `headshot-curly.jpg` (Kevin), `presenting.jpg` (Prithvish), `headshot-gold-glasses.jpg` (Alexandros) are live. `portrait-1/2/3.jpg` are leftovers from the v3 mockup and are no longer referenced. |

---

## How to preview

Double-clicking `index.html` mostly works, but **serve it over HTTP instead**. From this folder:

```
python -m http.server 8731
```

then open `http://localhost:8731`. Paths and the form behave differently on `file://`.
`contact.php` will not run under that server, which is fine: you are then testing the
fallback path that the GitHub Pages copy also takes.

---

## Common edits - exact file & line

### 1. The contact form and where it sends
- Every "Get an evaluation" button is now a plain link to `about.html#contact`. No script
  rewrites them any more; `booking.js` was deleted in v7.
- The form posts to **`contact.php`**, which needs a config file **outside the web root**
  before it can send anything. Exact steps are in the comment at the top of `contact.php`.
  In short: create `~/sillage-private/config.php` on the Hostinger account with the
  destination address, a from-address, an encryption key and two directory paths, `chmod 600`.
- **Until that config exists the form cannot deliver.** It fails safely: the visitor keeps
  everything they typed and is shown the email address instead. The same happens on the
  GitHub Pages copy, which cannot run PHP at all.
- The displayed address is `info@seeyazh.com`. To change it, search every file for that
  string: it is in the footer of every page, the form fallback, and the privacy policy.
- The `from` address in the config must be a **real mailbox on the sending domain**, or SPF
  and DMARC will drop the mail silently.
- WARNING: **do not add a second PHP file, reCAPTCHA, or a hosted form service** without
  changing the privacy sentence under the submit button first. It promises no third-party
  sharing and no cookies, and it has to stay true.

### 2. Change a colour
- **File:** `styles.css`, the `:root` block, and read the comment above it first. That
  comment lists every colour pair on the site with its **measured** contrast ratio.
- Rules to keep (v7; these replace the v4 rules that used to be listed here):
  1. **Do not add a colour that is not in `:root`.**
  2. **`--clay-bright #EE9560`, `--sage` and `--plum` are fills.** They fail AA as text on a
     light ground. `--clay-bright` is text in exactly **two** places sitewide, both on forest:
     the footer slogan, and the word "agreement" in the interplay statement.
  3. **`--clay` is `#A85526`.** The specification named `#B4602C`; measured, that is 4.26:1 on
     ivory and fails AA. Never put `--clay` or `--forest-soft` on a mineral panel as text.
  4. **Grounds are forest or ivory.** `--white` is a background for the header and cards only.
     Never pure white or pure black as a type colour.
  5. **Section eyebrows are sans and UPPERCASE** (`--body`, 12.5px, weight 600, `--forest`,
     opacity .75). This reversed the v4 "mono, Title Case, never uppercase" rule.
  6. **Headings are sentence case with a full stop** where they are statements. Never title-case.
- If you change a colour that carries text, **re-measure it**. Do not assume.

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

### 6. The evidence expanders (homepage)
- **File:** `index.html`, the three `.row3` blocks. Each has a `<button class="see">` and a
  panel. Figures and citations are shown **verbatim**. Never paraphrase a statistic, guess a
  date, or add a figure that is not in the build specification.
- **The climate chart has two bars, not three.** An earlier version added a hot condition and
  misrepresented the source. Do not add it back.
- **Do not turn these into an accordion.** More than one may be open at once; that is the point.
- The Music row has **no image on purpose**, and stays that way until a real photograph exists.
  Do not put a stock image there.

### 7. Venue pages (built, not published)
- **Files:** `venues/wellness/`, `venues/personal-care/`, `venues/business-premises/`.
- They are **deliberately empty of research**, because a thin venue page is worse than no venue
  page. Every content block is a labelled "pending" box. **Do not fill them with an invented
  statistic, percentage or citation.**
- The homepage venue cards are therefore **not links yet**. Each card carries a comment saying
  exactly what to change to publish it, once the research lands.

### 8. Fill in the privacy policy
- **File:** `privacy.html` - replace every `[BRACKETED]` field before launch.

---

## Publishing

The real address is **https://www.seeyazh.com**. The site is live on **two** hosts,
which serve identical content, so if one breaks the other is the fallback.

| Host | URL | How it updates |
|------|-----|----------------|
| Hostinger (primary) | https://www.seeyazh.com | One command, see below. |
| GitHub Pages (fallback) | https://alexmoschop.github.io/sillage-website/ | Automatic, about a minute after you push to `master`. |

`seeyazh.com` without the `www` redirects to the `www` address, which is the canonical
one. The old preview address, `sillage.moschopoulos.com`, is retired: it still answers,
but every path 301s to the same path here, so old links keep working.

Because the canonical tags name `www.seeyazh.com`, the GitHub Pages copy now declares
itself a duplicate of the real site. That is deliberate — Pages is the rollback, not a
second published address.

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

- **Design language:** the signature is the blueprint "airflow and diffusion survey" drawing
  (paper sheet, forest walls, clay airflow arrows, dotted sage diffusion rings). It is inline
  SVG, no images and no libraries. In v7 it moved off the homepage hero, which is now a
  photograph, onto the venue template where it is the venue plan. Reuse that language for any
  new illustration, and keep blueprint drawings as inline SVG rather than raster.
- **Fonts:** on Apple hardware the display face resolves natively to *New York* and the body to
  *SF Pro Text*, which Apple licences for use on Apple platforms. Those faces are **deliberately
  not self-hosted**: that licence does not cover redistributing them as webfonts to arbitrary
  browsers, and the build specification was wrong to ask for it. Everywhere else the stack
  currently falls back to Georgia. To close that gap, self-host **Source Serif 4 (SIL OFL)**:
  put the `.woff2` files in `assets/fonts/` and uncomment the `@font-face` block near the top
  of `styles.css`. **Never add Google Fonts**; it would break the no-third-party promise the
  contact form makes.
- **No cookies / no tracking.** If you add analytics, a booking embed, CDN fonts, or a hosted form
  handler, you must add a cookie/consent notice and a data-processing agreement, and update `privacy.html`.
- **Accessibility:** semantic HTML, one `<h1>` per page, heading order, alt text, visible focus,
  keyboard-operable expanders, form errors that are never colour alone and are wired with
  `aria-describedby`, and a skip link. Target is WCAG 2.1 AA. In v7 all 28 shipped colour pairs
  were measured in the browser against their real composited backgrounds; all 28 pass.
- **Responsive:** exactly **one** breakpoint, 900px. Below it the header wraps and all three nav
  items stay visible. **There is no hamburger**; `#nav-toggle` was deleted in v7. Test at
  375, 768, 1024 and 1440.
- **Lighthouse has still never been run.** Run `npx lighthouse http://localhost:8731 --view`
  against a local server, or use Chrome DevTools, rather than assuming a score.

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
