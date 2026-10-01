# Running the back office on Supabase (Postgres)

The public site reads its pages, publications and settings from the database when there is one, and from the Markdown files when
there is not (`config/site.php`: `content_source` and `SITE_SETTINGS_SOURCE`). Vercel ships no SQLite, so production has been
serving the files. With a Postgres database the back office (`/admin`) can edit and save, and the public site shows the edits.

Nothing below needs a code change. The one code-side option is the pooler flag.

## 1. Variables

In the local `.env` **and** in the Vercel project (Settings, Environment Variables). Never commit them.

| Variable | Value |
|---|---|
| `DB_CONNECTION` | `pgsql` |
| `DB_HOST` | Local: the direct host (`db.<ref>.supabase.co`). **Vercel: the transaction pooler** (`aws-…pooler.supabase.com`), because the direct host is IPv6 only and Vercel functions often cannot reach it. |
| `DB_PORT` | `5432` direct, `6543` for the transaction pooler |
| `DB_DATABASE` | `postgres` |
| `DB_USERNAME` | `postgres` direct; `postgres.<ref>` on the pooler |
| `DB_PASSWORD` | the database password (reset it in Supabase if it was ever pasted anywhere) |
| `DB_SSLMODE` | `require` |
| `DB_PGBOUNCER` | `true` on the transaction pooler (turns on emulated prepared statements, which pgbouncer needs) |
| `SITE_SETTINGS_SOURCE` | `database` (the Vercel entry defaults it to `files`) |
| `ADMIN_SEED_EMAIL`, `ADMIN_SEED_NAME`, `ADMIN_SEED_PASSWORD` | the first operator, read once by the seeder |

## 2. Create the tables and load the content

From a machine that can reach the database (use the **direct** host for this step, migrations dislike the transaction pooler):

```
php artisan migrate --force
php artisan db:seed --force
```

`db:seed` loads the settings, the loader quotes, the admin operator and the content (`ContentFoundationSeeder` reads
`resources/content/`; `tests/Feature/ContentSeedFidelityTest.php` checks that the database renders the same pages as the files).
Re-running it is safe for the content; the admin seeder does not overwrite an existing operator.

## 3. Check

- `https://<site>/admin/login` accepts the operator.
- Edit a page in `/admin`, save, reload the public page: the edit is there.
- Nothing is lost if the database is down: each read falls back to the Markdown files.

## 4. What the back office is not

It is not linked from the public site, `robots.txt` disallows it, and every `/admin` response carries
`X-Robots-Tag: noindex, nofollow, noarchive` (`vercel.json`).
