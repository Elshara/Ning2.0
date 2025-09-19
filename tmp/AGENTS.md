# Agent Guidance – `tmp/`

**Scope precedence:** Apply this minimal guidance before root instructions when managing the `tmp/`
directory.

## Notes
- This directory is reserved for runtime artefacts. Do not commit generated files.
- Document any required runtime structure or cleanup tasks here.
- The lint audit writes `tmp/audit/php_lint_audit.json`; ensure the directory remains ignored and
  purge stale reports when investigating new failures.

## Audit Summary
- Pending lint audit captured for `tmp`. Run `php tools/php_lint_audit.php tmp` to log per-file results in `tmp/audit/php_lint_audit.json` and document follow-up fixes.
- Continue cataloguing PHPFox Legacy, Dolphin, and Cheetah feature gaps relevant to this scope during modernization.
