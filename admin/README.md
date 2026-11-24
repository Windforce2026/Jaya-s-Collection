# Jaya's Collection — product admin

A small PHP admin panel for cPanel / Hostinger shared hosting. The client can log in, add or edit products **category by category**, upload photos into `assets/img/`, and publish instantly — no database setup and no build step.

## How it works

1. Products, categories and store details live in `admin/data/store.json`.
2. Every save rewrites `js/data.js` in exactly the format the public site already expects, and refreshes `sitemap.xml` with the current product URLs.
3. The public HTML/JS pages are unchanged — they keep reading the same global `PRODUCTS`, `CATEGORIES`, `WHATSAPP_NUMBER` and `STORE` variables.
4. Uploaded photos go to `assets/img/` and are referenced as `assets/img/filename.jpg`.

## First-time setup

1. Upload the whole site folder to cPanel/Hostinger.
2. Make sure these are writable by PHP (usually `755` for folders, `644` for files):
   - `admin/data/`
   - `js/data.js`
   - `assets/img/`
3. Visit `https://yourdomain.com/admin/setup.php`.
4. Choose a username and a password of at least 8 characters. This creates `admin/data/config.json` with a bcrypt password hash.
5. Log in at `/admin/login.php`. Change the password any time from **Password**.

> Do not commit or upload a `config.json` from your local machine — it contains the password hash. Let the setup page create it on the server. `.gitignore` excludes it from Git.

## Everyday use

| Page | What it does |
|---|---|
| `index.php` | Products grouped by category; add, edit, delete |
| `product.php` | Product form: bilingual names/descriptions, category, badge, slug, photo |
| `categories.php` | Rename categories, edit blurbs/photos, add/delete categories |
| `settings.php` | WhatsApp number, phone, email, address, map and Facebook links |
| `password.php` | Change the admin password |

To publish a product: choose its category, fill the English fields (Bengali is optional — English is used as a fallback), select an existing photo or upload a new one, then **Save**. The site updates immediately.

## Important notes

- **Home-page category cards are hard-coded** in `index.html`. A newly added category appears in the collection filters and product pages, but not as a card on the home page until one is added manually to `index.html`.
- The product slug is the link used as `product.html?p=slug`. If you change an existing slug and it clashes with another product, a number is added automatically.
- Deleting a product does **not** delete its image file, because several products may share a photo. Remove unused photos manually from `assets/img/` if needed.
- A category can only be deleted when no products use it.
- `js/.htaccess` tells browsers not to cache `data.js`, so saves show up on refresh. On servers where `.htaccess` is ignored, ask the host to disable caching for `js/data.js` or hard-refresh after saving.
- `admin/data/.htaccess` blocks direct download of the JSON files. If the host uses nginx instead of Apache/LiteSpeed, move `admin/data/` outside the public web root or ask the host for an equivalent rule.
- Use HTTPS. There is no password-reset email, so keep the login details safe.
- Back up `admin/data/store.json` and `assets/img/` regularly.

## Troubleshooting

| Symptom | Fix |
|---|---|
| Setup page says it cannot save `config.json` | Grant PHP write access to `admin/data/` (755/775) |
| Save says it cannot write `data.js` | Make `js/data.js` writable (644) and `js/` writable if needed |
| Upload says folder is not writable | Set `assets/img/` to 755/775 |
| Old products still visible after saving | Hard-refresh the browser; check the `js/.htaccess` cache rule |
| Login page redirects to setup again | `admin/data/config.json` is missing or unreadable — run setup once |
