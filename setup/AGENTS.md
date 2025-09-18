# Agent Guidance – `setup/`

**Scope precedence:** Obey this file before the repository root `AGENTS.md` for everything under
`setup/`. When touching shared environment helpers, coordinate with `lib/AGENTS.md`.

## Modernization Priorities
- Keep the setup wizard modular: one class or helper per file inside `setup/src/`.
- Ensure the wizard auto-detects HTTPS, domains, ports, and database compatibility without manual
  intervention. Document missing cases here.
- Replace deprecated patterns and remove duplicated form handling logic. Run `php
  tools/detect_duplicates.php setup` to surface reuse opportunities.

## Structural Guidance
- Split UI rendering, environment detection, validation, and persistence responsibilities into
  distinct files. Record future refactors below when complete separation is not yet possible.
- Prefer configuration stored in the database over flat files when forward planning modern features;
  track migrations or schema changes that are still required.

## Testing & Checks
- After modifying the wizard, run targeted CLI and browser entry-point checks where possible, and
  document results in commit summaries.
- Keep generated files like `config/app.php` ignored and note any installer automation gaps here.

## Outstanding Work Log
- Use this section to list unfinished steps, missing functionality, or follow-up cleanup tasks.
