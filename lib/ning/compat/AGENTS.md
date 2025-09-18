# Agent Guidance – `lib/ning/compat/`

**Scope precedence:** Use this file for compatibility shims before referencing `lib/ning/AGENTS.md`.
Changes here can impact legacy WWF or widget code—coordinate updates across those directories and
note any required follow-up in their respective `AGENTS.md` files.

## Modernization Priorities
- Maintain behavioural parity with the historical Ning PHP SDK while wrapping responses in
  PHP 8.4-safe constructs. Avoid introducing new global state.
- Consolidate duplicated adapter logic. If multiple shims perform similar sanitization or mapping,
  refactor into shared helpers located alongside the consuming subsystem.
- Clearly mark any deprecated behaviour that must remain temporarily for backwards compatibility and
  include timelines for removal in the Outstanding Work Log.

## Structural Guidance
- Keep each adapter or façade in its own file. When a file grows beyond a single responsibility,
  split it and document the migration path here.
- Add inline documentation describing expected inputs/outputs so consuming layers can modernize
  without re-reading legacy Ning documentation.

## Testing & Checks
- Add regression tests in `tests/integration/` whenever adapting behaviour that affects runtime data
  structures. Capture manual QA steps when automated coverage is not possible.
- Run the duplicate scanner for this directory (`php tools/detect_duplicates.php lib/ning/compat`)
  before completing your work.

## Outstanding Work Log
- Record required follow-up cleanups, deprecated pathways to retire, and pending documentation tasks
  uncovered while updating these shims.
