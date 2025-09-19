# Agent Guidance – `lib/`

**Scope precedence:** Follow this file first for anything under `lib/`, then fall back to the
repository root `AGENTS.md` for shared expectations. Coordinate with `widgets/AGENTS.md` and
`setup/AGENTS.md` when work crosses boundaries.

## Modernization Priorities
- Replace legacy procedural helpers with focused classes or single-function files where practical.
- Remove or consolidate duplicated logic. Use `php tools/detect_duplicates.php lib` to spot repeated
  sections before submitting changes.
- Prefer dependency-light abstractions that keep the legacy bootstrap operable while enabling future
  modularisation.
- When removing end-of-life code paths, document the replacement or follow-up work inside this file.

## Structural Guidance
- Large files should be decomposed into smaller modules (one class or function per file) and stored
  in subdirectories that match their domain (e.g., `NF/`, `ning/`). Record outstanding split tasks
  below.
- Maintain compatibility with MySQL/MariaDB access helpers. Flag missing coverage for new features
  in this file.

## Testing & Checks
- Run the repository-wide linting and duplicate detection steps after edits.
- Add targeted unit or integration tests in `tests/` when new helpers are introduced or behaviour
  changes.

## Outstanding Work Log
- List discovered duplication, modernization gaps, or missing functionality here with actionable
  next steps.
