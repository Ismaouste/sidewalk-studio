# Analytics Modes

## v0

- `ANALYTICS_DRIVER=none`
- no analytics script is loaded
- the analytics category still exists so the future adapter contract is stable

## Planned later

- Matomo for privacy-first aggregate measurement
- PostHog for explicit opt-in product analytics if needed

Those later adapters should plug into the existing registry instead of coupling themselves directly to the UI modal.
