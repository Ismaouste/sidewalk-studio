# Release Planning

Keep release planning lightweight in v0.

## Source of truth

- `Roadmap.md` defines the milestone order.
- `CHANGELOG.md` records shipped scope.
- The current specs explain what each block was meant to deliver.

## Working rule

- Prefer small, readable delivery commits.
- Keep docs and workflow changes in separate commits from app/runtime changes.
- Do not describe deployment or production automation that does not exist yet.

## Database note

- Keep SQLite for v0.
- Prefer PostgreSQL as the first database to explore later if the product needs it.
- Defer any server database migration until that need is concrete.
