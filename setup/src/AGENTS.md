# Agent Guidance – `setup/src/`

**Scope precedence:** Follow this file for all PHP modules beneath `setup/src/` before consulting
`setup/AGENTS.md`. For environment detection utilities, also reference
`setup/src/Environment/AGENTS.md`.

## Modernization Priorities
- Keep each class or trait narrowly scoped (validation, rendering, persistence, etc.). Create new
  subdirectories when responsibilities diverge and update this guidance accordingly.
- Replace implicit superglobals with explicit dependencies injected through constructors or factory
  methods. Document remaining legacy coupling in the Outstanding Work Log.
- Ensure every step of the wizard gracefully handles missing extensions and surfaces actionable
  error messages suitable for CLI and HTTP contexts.

## Structural Guidance
- Colocate templates, view models, and controllers to clarify ownership. When splitting files, note
  the migration plan below so future agents can continue the decomposition.
- Persist shared interfaces in this directory and reuse them across steps to reduce duplication.

## Testing & Checks
- Add unit tests under `tests/` for validation logic and serialization. Use integration tests for
  multi-step flows and note manual QA steps when automation is unavailable.
- Run `php tools/detect_duplicates.php setup/src` after changes to keep modules distinct.

## Outstanding Work Log
- Track unfinished refactors, missing validation rules, or UX improvements that need follow-up.

## Audit Summary
- Pending lint audit captured for `setup/src`. Run `php tools/php_lint_audit.php setup/src` to log per-file results in `tmp/audit/php_lint_audit.json` and document follow-up fixes.
- Continue cataloguing PHPFox Legacy, Dolphin, and Cheetah feature gaps relevant to this scope during modernization.
