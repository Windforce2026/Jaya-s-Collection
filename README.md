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

Plus `css/styles.css`, `js/data.js`, `js/main.js`, `assets/logo.svg` and product photography in `assets/img/`.

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

The current images are **royalty-free Pexels placeholders** — replace them with real photographs of the boutique's stock before launch. See `assets/img/ATTRIBUTION.md` for the source list.

### Logo

`assets/logo.svg` is a temporary monogram (navy circle, gold lotus petals, "JC"). To use the boutique's own Ganesha logo:

1. Save the file as `assets/logo.png` (square, ideally 400 × 400 px or larger, transparent or navy background).
2. In **all seven HTML files**, replace:

```html
<img class="brand__mark" src="assets/logo.svg" alt="" width="46" height="46">
```

with:

```html
<img class="brand__mark" src="assets/logo.png" alt="" width="46" height="46">
```

3. Update the favicon links too:

```html
<link rel="icon" type="image/svg+xml" href="assets/logo.svg">
```

→ `<link rel="icon" type="image/png" href="assets/logo.png">`

There are three logo `<img>` tags per page (header, mobile menu, footer), so a find-and-replace of `assets/logo.svg` → `assets/logo.png` in all HTML files is the quickest route.

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

---

## Deployment checklist

- [ ] Replace the placeholder logo (`assets/logo.svg`) with the boutique's logo.
- [ ] Replace the stock photography with real product photos.
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
- "handmade in Kolkata" or an in-house workshop. Copy says "finished by hand" / "selected in person", which is consistent with a boutique that curates stock.

Before publishing, the client should confirm the points below.

---

## Open questions for the client

1. **Logo file** — supply the original Ganesha / lotus logo (PNG or SVG).
2. **PIN code** — Facebook lists **700078**; Ballygunge New Market is commonly listed as **700019**. The site currently uses **700019**. Confirm which is correct for the shop.
3. **Opening hours** — not published. The site says "Message us for today's timings". Provide days and hours to replace this.
4. **Google Maps place link** — the current link is a search for "Jaya's Collection Ballygunge New Market Kolkata", not a verified place card. Send the exact Google Business Profile share link to embed it properly.
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
│  ├─ logo.svg
│  └─ img/            ← replace with real photography
├─ references/       ← research material (not part of the site)
└─ README.md
```

`references/` contains screenshots and saved pages used during research. It can be excluded from the deployed upload.
