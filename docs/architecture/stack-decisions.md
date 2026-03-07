# Stack Decisions

## Kept for v0

- Laravel 12 for routing, configuration, content loading, and testable backend behavior
- Inertia + Vue 3 + TypeScript for the public shell because the repo is also meant to demonstrate modern app ergonomics
- SQLite for local development because it removes service friction on Windows
- Tailwind v4 with local font packages to avoid remote font requests
- `.env` for secrets and runtime infrastructure, with a later bounded `site_settings` layer reserved for non-secret runtime configuration

## Explicitly deferred

- Docker and containerized local development
- GitHub Actions and release automation
- production deployment configuration
- real analytics drivers
- required SSR runtime in day-to-day development

## Why Inertia even if it may feel heavy

It is heavier than static Blade pages, but it is the stack decision currently chosen for the project. The repo embraces that decision by making Laravel responsible for structure and SEO, while Vue handles the interactive shell without pretending to be a static site generator.
