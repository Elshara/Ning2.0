# Agent Guidance – `setup/src/Config/`

**Scope precedence:** Apply this file to every PHP module under `setup/src/Config/` before consulting `setup/src/AGENTS.md`. Coordinate serializer changes with the runtime guidance in `lib/AGENTS.md` when behaviour must stay aligned after installation.

## Modernization Priorities
- Keep configuration writers and serializers pure: accept normalised data structures, avoid touching `$_SERVER`, and surface clear error strings for the wizard UI and CLI flows.
- Replace ad-hoc file access with reusable helpers that validate directories, capture warning details, and avoid using the error-suppression operator.
- Ensure any new classes can be exercised by unit tests; structure methods to allow dependency injection or mocking of filesystem interactions where appropriate.

## Structural Guidance
- Maintain one class per file. If configuration concerns expand (e.g., builders vs. writers), split them into dedicated files within this directory and reference the relationship here.
- Keep helper methods private unless shared usage is required. Promote shared abstractions into a dedicated interface or trait and document the contract in this scope.

## Testing & Checks
- Add or update tests under `tests/` when changing serialization behaviour. At minimum, run
  `php tools/detect_duplicates.php setup/src/Config` after edits to ensure helpers remain distinct
  and keep `php tests/integration/duplicate_audit_test.php` green.
- Validate installer flows manually (CLI and browser) when touching write paths to confirm
  permissions and error handling surface correctly.

## Outstanding Work Log
- Extend automated coverage for the configuration builder and writer, including
  unwritable-directory and invalid-path scenarios, so future refactors remain safe.
- Document any PHPFox Legacy, Dolphin Remake, or Cheetah configuration concepts that should be
  ported, mapping their storage expectations before implementation.

## Audit Summary
- Pending lint audit captured for `setup/src/Config`. Run `php tools/php_lint_audit.php setup/src/Config` to log per-file results in `tmp/audit/php_lint_audit.json` and document follow-up fixes.
- Continue cataloguing PHPFox Legacy, Dolphin, and Cheetah feature gaps relevant to this scope during modernization.
