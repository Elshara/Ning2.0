# Agent Guidance – `tools/`

**Scope precedence:** Follow this file before the root guidance when updating repository tooling.

## Modernization Priorities
- Keep maintenance scripts self-contained with one responsibility per file.
- Ensure tools are compatible with PHP 8.4 and avoid deprecated functions.
- Document missing automation or checks that future agents should add.
- Maintain the lint audit helper (`php_lint_audit.php`) alongside duplicate detection so syntax
  regressions are caught and logged for each scope.

## Structural Guidance
- Provide inline documentation and usage examples in each tool or accompanying README.
- Add tests under `tests/` when tools grow complex.

## Outstanding Work Log
- Track planned enhancements or refactors to repository tooling here.
- Document new integration hooks (e.g., duplicate audit tests) whenever tooling gains consumers so
  contributors know which scripts are considered gates.

## Audit Summary
- Pending lint audit captured for `tools`. Run `php tools/php_lint_audit.php tools` to log per-file results in `tmp/audit/php_lint_audit.json` and document follow-up fixes.
- Continue cataloguing PHPFox Legacy, Dolphin, and Cheetah feature gaps relevant to this scope during modernization.
