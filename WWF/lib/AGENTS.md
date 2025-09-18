# Agent Guidance – `WWF/lib/`

**Scope precedence:** Follow this file after `WWF/AGENTS.md` when modifying library code for WWF
components. Coordinate with `lib/AGENTS.md` when code is shared between WWF and the main runtime.

## Modernization Priorities
- Identify duplicated logic between WWF libraries and the modernized runtime helpers. Consolidate
  behaviour where possible and record remaining overlaps in the Outstanding Work Log.
- Update exception handling and controller patterns for PHP 8.4 compatibility, avoiding deprecated
  language constructs.
- Prepare these helpers for PSR-4 autoloading as part of the broader modernization effort.

## Structural Guidance
- Keep each controller/helper focused on a single responsibility. When a file mixes concerns, split
  it and document the follow-up tasks below.
- Document integration points with widgets or SDK components to guide future refactors.

## Testing & Checks
- Ensure changes are covered by tests in `test/` or `tests/`. Add new cases where existing coverage is
  insufficient.
- Run `php tools/detect_duplicates.php WWF/lib` alongside linting before submitting changes.

## Outstanding Work Log
- Track remaining modernization tasks, shared helper migrations, and deprecations scheduled for
  removal.
