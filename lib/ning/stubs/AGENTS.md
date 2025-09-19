# Agent Guidance – `lib/ning/stubs/`

**Scope precedence:** Read this guidance before modifying any stub inside `lib/ning/stubs/`.
Coordinate with `lib/ning/AGENTS.md` and the consuming subsystem (usually `widgets/` or `WWF/`)
when altering these temporary shims.

## Modernization Priorities
- Treat stubs as placeholders: either replace them with real implementations or document the
  blocking dependency in the Outstanding Work Log with a migration plan.
- Keep method signatures aligned with the legacy interface expectations so existing code continues to
  run until the real implementation is available.
- Remove redundant or unused stubs once downstream code no longer relies on them.

## Structural Guidance
- Limit each stub to the smallest viable surface area. Avoid adding new behaviour here unless it is a
  direct compatibility shim for existing legacy code.
- When a stub must remain, add TODO comments referencing follow-up tickets and include additional
  context in this file.

## Testing & Checks
- Before removing or replacing a stub, ensure corresponding unit/integration tests exist or are added
  to cover the real implementation.
- Run the duplicate scanner (`php tools/detect_duplicates.php lib/ning/stubs`) to confirm temporary
  code has not drifted across directories.

## Outstanding Work Log
- List each remaining stub, its consumer, and the planned path to a production-ready implementation.

## Audit Summary
- Pending lint audit captured for `lib/ning/stubs`. Run `php tools/php_lint_audit.php lib/ning/stubs` to log per-file results in `tmp/audit/php_lint_audit.json` and document follow-up fixes.
- Continue cataloguing PHPFox Legacy, Dolphin, and Cheetah feature gaps relevant to this scope during modernization.
