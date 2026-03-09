## Public Static Handoff

This repository now supports a lightweight public mode that can be reproduced on another machine without relying on a local admin workflow or a populated site-settings database row.

### Source of truth for public content

- `resources/content/` contains public pages, journal entries, and case studies.
- `lang/en/site.php` and `lang/fr/site.php` contain the committed public identity, contact details, and consent copy defaults.
- `database/seeders/data/site-settings.json` keeps a portable snapshot of the public-facing site settings.
- `.env.example` is aligned with the current public identity and file-backed settings mode.

### Recommended public-only configuration

Keep these values in `.env`:

- `SITE_SETTINGS_SOURCE=files`
- `APP_URL=http://127.0.0.1:8088` for local work, or the target public URL when exporting

SQLite can stay enabled for Laravel runtime compatibility, but public identity and shell copy no longer depend on a database row.

### Minimal setup on another machine

```bash
cp .env.example .env
composer install
npm ci
mkdir -p database
touch database/database.sqlite
php artisan key:generate
php artisan migrate
```

### Local development

```bash
php artisan serve --host=127.0.0.1 --port=8088
npm run dev
```

### Production-like local build

```bash
npm run build
php artisan serve --host=127.0.0.1 --port=8088
```

### Static preview export

```bash
php artisan site:export-static-preview --locales=fr,en --output=dist/static-preview --base=/sidewalk-studio/
```

The export includes:

- prefetch hints for internal navigation
- a generated `manifest.webmanifest`
- a generated `sw.js` service worker
- partial offline and aggressive asset caching for the static preview shell

### Notes

- GitHub Pages remains a static approximation of the public front-end, not the full Laravel runtime.
- For a more faithful version later, deploy the real Laravel app on a small VPS or home-hosted setup.
