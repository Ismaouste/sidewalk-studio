# 2026-08-31 — Three defects closed, one thread left open

**Branch:** `feat/declared-content-schema`
**Session:** follow-up to the declared-content-schema delivery, on the two
defects that delivery found and left, plus one reported live during the session.

---

## What shipped

### 1. The page title named the site twice, and spelled it two ways

`App\Support\Seo::page()` composes `"{page} · {suffix}"`, where the suffix comes
from `lang/{locale}/site.php` — so `Ismaël` in French. Both client entrypoints
then declared a second composer:

```ts
title: (title) => (title ? `${title} | ${appName}` : appName);
```

with `appName` read from `VITE_APP_NAME`, frozen into the bundle at build time
from one `.env` line, blind to the locale — `Ismael`, no diaeresis.

`SeoMeta.vue` hands Inertia the already-composed PHP title inside a `<Head>`
slot. Two things in the vendored code make that fatal, and both were read rather
than assumed:

- `@inertiajs/vue3` `renderTagStart` **always** stamps `data-inertia` onto a
  slot node, so `<title>X</title>` is emitted as `<title data-inertia>X</title>`;
- `@inertiajs/core` `createHeadManager().collect()` matches any element starting
  `"<title "` and runs its **inner text** through the title callback.

So a French page settled on `Journal · Ismaël Rodmacq | Ismael Rodmacq`.

**Why 124 green tests never saw it:** the doubling happens when the head manager
commits _in the browser_. Every title assertion in the suite reads the Blade
document, which was correct all along.

**Fix:** delete the second composer. Inertia's `titleCallback` prop already
defaults to `(title) => title`, so removing the option is enough — no
pass-through function needed. PHP is now the only composer: it knows the locale,
and it reads a name the operator can edit from `/admin/language-files` without a
rebuild.

**Consequence handled, not absorbed:** the 13 admin screens pass a _bare_ title
and drew their suffix from that callback. Left alone they would have become
`Edit experience`, ambiguous in a tab strip. They now use
`components/admin/shared/AdminHead.vue`, which composes from the shared,
locale-aware `site.name` prop and the same `·` separator as the public side.

Pinned by `tests/Feature/PageTitleIsComposedOnceTest.php`, which asserts both
halves: PHP appends the name exactly once in every locale, and neither
entrypoint declares a title callback or reads `VITE_APP_NAME`. The second half
greps the source, in the manner of `SiteIsAgnosticTest` — stated in the
docblock, because the suite has no browser and the test should not pretend
otherwise.

Verified in a real browser: `/fr/journal/content-systems-routing-and-metadata`
now titles `… · Ismaël Rodmacq`, and `/admin/settings` titles
`Admin Settings · Ismael Rodmacq`.

### 2. The 4KB session cookie still bit two routes

`SESSION_DRIVER=cookie` (in `.env` and `.env.example`, because the deployed
runtime is serverless) means the session must survive `Set-Cookie`, and a
browser silently drops a cookie over ~4096 bytes. Laravel's handler answers a
`ValidationException` by redirecting with the errors _and the whole request
input_ (`Handler::invalid()`). On an admin page form that input is a page.

`AdminPageController::update` was fixed last session by routing around the
exception. That fixed one action. `AdminPageController::preview` and
`AdminLanguageFileController` both validate a `payload` and still took the
default path.

**Fix at the boundary rather than per controller:** `bootstrap/app.php` now
declares `$exceptions->dontFlash(['payload'])`. Nothing is lost — there is no
`old()` call and no `withInput()` call anywhere in `app/` or
`resources/views/`; the forms are Inertia forms and still hold what was typed.

`tests/Feature/RefusedAdminSavesReachTheOperatorTest.php` asserts the hazard is
real before asserting it is handled: the `experience/fr` payload exceeds the
cookie ceiling on its own, and the session carrying a refusal now stays under
half of it.

The comment in `AdminPageController` was rewritten: it claimed the handler
reflashes the whole input, which this change makes false.

### 3. The sticky breadcrumb did not reach the screen edges

Reported live. Measured at a 375px layout viewport, before:

|            | left              | right          |
| ---------- | ----------------- | -------------- |
| container  | 12px inset        | 12px inset     |
| breadcrumb | **−4px** overhung | **28px short** |

Two independent causes, both confirmed by measurement:

- **The asymmetry.** `.sw-main > .sw-container > *` sets `max-width: 100%`. A
  stretched grid item whose width is bounded stops stretching and falls back to
  `start` alignment, so the negative left margin bled but the negative right
  margin became a 16px indent. That is why it looked wrong on the right
  specifically.
- **The magnitude.** The bleed was a hardcoded `--sw-space-xs` (16px), while the
  shell gutter halves twice below 640px — 16px, then **12px under 480px**. Two
  numbers that should have been one.

**Fix:** `.sw-container` now names its gutter once as `--sw-container-inset` and
derives its own `width` from it, so the four breakpoints each set one number
instead of restating a `min()`. The breadcrumb reads that property. The
`max-width` clamp gets one documented exception, stated next to the rule it
excepts.

After: `navLeft: 0`, `navRight: 375` — edge to edge, in both `morning` and
`sunset`, with the glass (`blur(26px) saturate(1.45)`) active in the stuck state
and no horizontal overflow introduced.

**Regression check, run rather than assumed:** with the two CSS files stashed,
the desktop container measured `left 116.5 / width 1152` and the document
overflowed by 19px. With them restored, identical. The refactor is faithful to
the pixel, and the 19px overflow is not mine (see below).

---

## Verification

- `php artisan test` — **128 passed, 1462 assertions** (was 124 / 1444)
- `npm run check` — lint, format, types all green
- `composer run lint:check` — pint passed
- Browser: FR article and `/admin` screens at 390 / 620 / 1440, both themes

---

## The open thread — start here

**`/admin/pages/experience/fr` does not stay put.** The page loads and renders
(the snapshot shows the `Edit experience` title and the editor), and then the
SPA navigates away to `/admin/publications` on its own, within a second or two.
Reproduced three times in a row.

What is established:

- The document loads correctly at the right URL with the right title.
- Two `GET`s follow in the network log: `/admin/pages`, then
  `/admin/publications` — both `200`, in that order.
- `location.pathname` really becomes `/admin/publications`; it is not a
  reporting artefact of the browser tooling.
- **No console errors.** The only warnings are unused font preloads (see below).
- No mount-time navigation exists in the admin code: `Admin/Pages/Edit.vue` has
  `router.post` at lines 240 and 251 only, both behind user actions, and
  `AdminLayout.vue` calls the router nowhere.

What is **not** established, and should be step one:

- **Whether this predates this session's changes.** The null test was never run.
  Stash `resources/js/components/admin/shared/AdminHead.vue` and the 13 admin
  page edits, reload, and see whether the drift survives. It very likely does —
  `AdminHead` only renders `<Head :title>` and starts no navigation — but that
  is reasoning, not evidence, and the whole point of the last two sessions is
  that reasoning missed what measurement caught.

Candidates worth checking after that, roughly in order:

1. Inertia `prefetch` on the admin nav `<Link>`s turning into a visit rather
   than a prefetch. Two `GET`s matching two nav destinations is suggestive.
2. `initializeStaticPreviewNavigation` in `app.ts`, which installs capturing
   `click`, `pointerenter`, `focusin` and `touchstart` handlers. It returns
   early unless `site.runtime.staticPreview` is on — confirm it is off on
   `/admin` rather than assuming it.
3. The `watch(` at `Admin/Pages/Edit.vue:86`.

The interrupted step was an instrumented reload that records `location` every
150ms while wrapping `history.pushState` / `replaceState`, to see what performs
the navigation and when. That is the next command.

**The page editor's own verification is still outstanding** — 125 fields, 17
collapsed items — because the page will not stay on screen long enough to check
it. Both are the same thread.

---

## Found and left

- **19px of horizontal overflow on desktop**, from `.ambient-grid__sun`,
  `__shadow` and `__plane`. Confirmed pre-existing by the stash comparison
  above. `html { overflow-x: hidden }` is gated behind
  `@supports not (overflow: clip)`, so Chromium never applies it.
- **Unused font preloads in dev.** `app.blade.php` emits
  `<link rel=preload href="/build/…">` from the built manifest even when Vite is
  serving, so four woff2 files are preloaded and never used. Dev-only noise.
- **`VITE_APP_NAME` is now read by nothing.** It remains declared in `.env`,
  `.env.example` and `resources/js/types/global.d.ts`. The env files were left
  alone deliberately — they are guarded by the secrets hook, and removing the
  variable is the operator's call.

---

## Bringing the environment back

```
export PATH="/c/Users/ismae/AppData/Local/Microsoft/WinGet/Packages/PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe:$PATH"
php artisan serve --host=127.0.0.1 --port=8088
npm run dev
```

`npm run dev` needs PHP on `PATH` too, and fails with a bare
`php is not recognized` if it is missing: `@laravel/vite-plugin-wayfinder`
shells out to `php artisan wayfinder:generate` from a `buildStart` hook. Export
the path in the same shell before starting Vite.

Admin: `admin@sidewalk-studio.test` / `probe-password-12345`.
