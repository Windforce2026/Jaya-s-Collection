# Jaya's Collection — website

A seven-page, bilingual (English / Bengali), WhatsApp-first website for **Jaya's Collection**, a gold-plated artificial jewellery boutique in Ballygunge New Market, Kolkata.

Static HTML, CSS and JavaScript. No build step, no framework, no dependencies to install.

---

## Pages

| File | Purpose |
|---|---|
| `index.html` | Home — hero, story, collections, promise, bridal banner, craft, journal, visit |
| `collections.html` | Filterable grid of all pieces (filter state also works via `#category` in the URL) |
| `product.html` | Product detail page, driven by `?p=slug` (e.g. `product.html?p=noor-choker`) |
| `story.html` | Brand story, values, note from the boutique |
| `craft.html` | Gold-plating explainer, care ritual, FAQ |
| `journal.html` | Three full articles: care, bridal, styling (`#care`, `#bridal`, `#styling`) |
| `contact.html` | Address, phone, email, enquiry form → WhatsApp, map, visit FAQ |

Plus `css/styles.css`, `js/data.js`, `js/main.js`, the logo files in `assets/`, and product photography in `assets/img/`.

---

## How to preview

The site works when opened directly (`file://`), but a local server is closer to production and avoids browser restrictions on `history.replaceState`:

```powershell
# from the project folder — any one of these
python -m http.server 8080
npx serve .
```

Then open <http://localhost:8080>.

To publish, upload the whole folder to any static host (Netlify, Vercel, GitHub Pages, cPanel, Hostinger). No server-side code is required.

---

## Editing content

### Products and categories

All products live in `js/data.js`:

```js
{
  slug: "noor-choker",              // used in product.html?p=noor-choker
  category: "necklaces",            // must match a CATEGORIES id
  name: { en: "...", bn: "..." },
  badge: "bestseller",              // "", "new", "bestseller" or "bridal"
  image: "assets/img/necklaces.jpg",
  desc: { en: "...", bn: "..." },
  style: { en: "...", bn: "..." },
  occasion: { en: "...", bn: "..." }
}
```

- Add a product by adding an object to `PRODUCTS`; it appears automatically in the grid, filters and related products.
- Change a category name in `CATEGORIES` and it updates everywhere.
- To remove the badge, set `badge: ""`.
- The phone / WhatsApp number and address are in `WHATSAPP_NUMBER` and `STORE` at the bottom of the same file.

> `product.html` renders entirely from the URL. Always share the full link (`product.html?p=...`), never `product.html` alone.

### Photography

Every image is referenced by path from the HTML or `js/data.js`. To replace a placeholder:

1. Put the new photo in `assets/img/`.
2. Keep the same filename (easiest), or update the path in the HTML/`data.js`.
3. Recommended: portrait 4:5 for products (e.g. 1200 × 1500 px), landscape 3:2 for banners, under 300 KB each where possible.

The product and lifestyle images are **royalty-free Pexels placeholders** — replace them with real photographs of the boutique's stock before launch. See `assets/img/ATTRIBUTION.md` for the source list. One image is already real: `assets/img/boutique.jpg`, the shop photograph supplied by the boutique (shown on the home page and story page).

### Logo

The boutique's own Ganesha–lotus mark is installed across the site.

| File | Purpose |
|---|---|
| `assets/logo.png` | 256 × 256 transparent circular mark used in the header, mobile menu and footer (~92 KB) |
| `assets/favicon-32.png` | browser tab icon — a tighter crop with a little extra brightness so it stays legible at 16–32 px |
| `assets/apple-touch-icon.png` | iOS home-screen icon (opaque navy square) |
| `assets/img/logo-source.jpg` | the artwork as supplied (967 × 936 JPEG, navy background) |

Every page has three identical mark tags — header, mobile menu and footer:

```html
<img class="brand__mark" src="assets/logo.png" alt="" width="46" height="46">
```

The CSS crops the image to a circle (`border-radius: 50%`) and frames it with a hairline gold ring, so export a **square** file with the emblem centred and a little margin. The mark renders at 46 px (40 px below 640 px), and the 256 px file covers high-density screens.

To replace the logo later: export a square PNG of at least 512 px, overwrite `assets/logo.png`, then regenerate the two icon files from the same artwork (a tighter crop works better at tab size).

### Language

- English and Bengali strings are paired in the markup:

```html
<span class="en">Our story</span>
<span class="bn" lang="bn">আমাদের কথা</span>
```

- `html[data-lang]` controls which one is visible. The choice is stored in `localStorage` under `jc-lang`.
- Products, categories and the enquiry form options are translated in `js/data.js` / the `data-en` / `data-bn` attributes in `contact.html`.
- When adding text, always add both versions. If a Bengali translation is not ready, copy the English into the `.bn` span as a placeholder and flag it — do not leave the span empty.

### WhatsApp

All enquiry CTAs use the same number:

```
https://wa.me/919831917368?text=<pre-filled message>
```

To change the number, search for `919831917368` across the HTML files and `js/data.js`, and update `WHATSAPP_NUMBER`, `STORE.phone` and every `tel:` link.

The contact form does **not** send email. It validates name and phone, builds a message and opens WhatsApp in a new tab.

### Reviews

The home page has a reviews band (`index.html#reviews`, linked from every footer). It deliberately contains **no invented testimonials**. The Google listing currently shows no published reviews, so the section invites customers to write the first one instead of quoting anyone.

The verified listing link (resolved from the client's share link `https://share.google/rotILJjgarCt2A3B3`) is:

```
https://www.google.com/maps/place/Jaya%27s+Collection/@22.5212639,88.3648577,17z/data=!4m6!3m5!1s0x3a02777497674ea1:0xa027e36d886186dc!8m2!3d22.5212639!4d88.3648577!16s%2Fg%2F11mzmw6kbf
```

The place is also identified by CID `11540442629739218652` and feature ID `0x3a02777497674ea1:0xa027e36d886186dc`. It is used by the reviews band, the contact page address, the “Open in Google Maps” link and `STORE.map` in `js/data.js`.

To show quoted reviews later:

1. Ask the boutique for 3–5 real Google or Facebook reviews (screenshots are fine).
2. Add quote cards inside the `#reviews` section with the reviewer's first name and the source, keeping the Google links.
3. Update both the English and Bengali copy. Never write a review that a customer did not leave.

Once the listing has reviews, change the heading and lead back to a “read our reviews” message and add the star rating.

---

## Deployment checklist

- [ ] Replace the stock photography with real product photos.
- [ ] Send 3–5 real Google/Facebook reviews if quoted cards are wanted in the Reviews section.
- [ ] Confirm the price-on-enquiry wording (no prices are shown anywhere).
- [ ] Confirm the PIN code (see open questions below).
- [ ] Add the real domain to `<link rel="canonical">` and `og:url` on every page.
- [ ] Add a Google Analytics / Meta Pixel tag if the client wants tracking.
- [ ] Submit the site to Google Search Console and update the Facebook page website link.
- [ ] Test the WhatsApp links on a real phone (the `wa.me` link opens the app; desktop opens WhatsApp Web).
- [ ] Check every image loads and every internal link resolves.

---

## Content and claims policy

The site deliberately avoids claims that could not be verified. In particular it does **not** state:

- a founding year, "since 19xx", or a family generation;
- the base metal, karat of the plating, or plating thickness ("gold-plated" only);
- a warranty, guarantee, exchange or refund policy (FAQ routes these to WhatsApp);
- opening hours, a Google rating, or a specific number of customers served;
- invented testimonials — the Reviews section links to the boutique's Google listing instead of quoting customers without permission;
- "handmade in Kolkata" or an in-house workshop. Copy says "finished by hand" / "selected in person", which is consistent with a boutique that curates stock.

Before publishing, the client should confirm the points below.

---

## Open questions for the client

1. **Logo file** — the supplied Ganesha / lotus artwork is installed. Confirm it is final and, if possible, send a higher-resolution or vector original (the supplied file was a 967 × 936 JPEG).
2. **PIN code** — three different values appear online: Facebook **700078**, the Google Business listing embedded on the contact page **700029**, and common directory listings **700019**. The site currently uses **700029** (from the Google listing). Confirm the correct PIN for the shop.
3. **Opening hours** — not published. The site says "Message us for today's timings". Provide days and hours to replace this.
4. **Google Maps place link** — resolved from the client's share link; the site now uses the verified place URL (see the Reviews section above). No change needed unless the business moves or the listing is merged. Ask the client to keep the Google Business Profile active so the review link keeps working.
5. **Product range** — the 18 products are illustrative examples built from the boutique's categories. Replace names, descriptions and photos with actual stock, or approve them as-is.
6. **Materials** — confirm the base metal and plating specification, and whether any piece is nickel-free / hypoallergenic. This can then be added to the product spec block.
7. **Exchange / return policy** — confirm the real policy so the FAQ can be specific.
8. **Delivery charges and timelines** — confirm whether delivery is free, chargeable, insured, and typical delivery time. The announcement bar currently says "Delivery across India" (the safer wording). If delivery is genuinely free, change it to "Complimentary delivery across India".
9. **Email address** — confirm `jayalahiri1972@gmail.com` is the address to publish.
10. **Founder credit** — the story page signs off as "Jaya's Collection" rather than a personal name. Add the founder's name and a line of biography if desired.
11. **Domain** — supply the final domain for canonical URLs and social previews.

---

## Accessibility and performance notes

- Skip link, visible focus styles, `aria-expanded` on menu / accordions, labelled form fields, `lang="bn"` on Bengali text.
- Respects `prefers-reduced-motion`.
- Images carry `width` / `height`, below-the-fold images use `loading="lazy"`, the hero uses `fetchpriority="high"`.
- Fonts are loaded from Google Fonts with `preconnect` and `display=swap`.
- The page works without JavaScript for reading, but the collection grid and product pages require JavaScript (a `<noscript>` message points to WhatsApp).

---

## Mobile and responsive behaviour

The layout is fluid between 320 px and wide desktop, with three breakpoints:

| Width | What changes |
| --- | --- |
| `≤ 900px` | Primary nav replaced by the full-screen menu (language toggle moves into it); two-column CTA bands stack and their buttons go full-width; grids collapse to 1–2 columns. |
| `≤ 640px` | Single-column product and journal grids, stacked hero and page headers, 66 px section rhythm, larger tap targets on footer links, language buttons, product-card links and contact links. |
| `≤ 380px` | Header and hero compress (smaller brand mark, icon-less "Enquire" pill, 38 px menu button, tighter hero spacing) so nothing overlaps at 320 px. |

- **Map:** desktop loads Google's business card; at `≤ 640px` `initMapEmbeds()` in `js/main.js` swaps the iframe to a plain coordinate pin (`?q=22.5212639,88.3648577`) because Google crops its place card in a short mobile frame. The visible "Open in Google Maps" links always use the verified place URL.
- **WhatsApp float** stays bottom-right on all pages; at `≤ 380px` the hero's bottom padding is increased so the meta line never sits under it.
- **Bengali** copy reflows cleanly at every width — the toggle is reachable from both the header (≥ 901px) and the mobile menu (≤ 900px).
- Tested with headless Chrome at **320 / 360 / 390 px** in mobile emulation and 1440 px desktop: 42 width/page combinations with zero horizontal overflow, 14 EN/BN mobile page loads with no console errors and no broken images, and all 7 desktop pages clean.

---

## File map

```
jaya/
├─ index.html
├─ collections.html
├─ product.html
├─ story.html
├─ craft.html
├─ journal.html
├─ contact.html
├─ css/
│  └─ styles.css
├─ js/
│  ├─ data.js
│  └─ main.js
├─ assets/
│  ├─ logo.png
│  ├─ favicon-32.png
│  ├─ favicon-32.png
│  ├─ apple-touch-icon.png
│  └─ img/            ← replace with real photography
├─ references/       ← research material (not part of the site)
└─ README.md
```

`references/` contains screenshots and saved pages used during research. It can be excluded from the deployed upload.
