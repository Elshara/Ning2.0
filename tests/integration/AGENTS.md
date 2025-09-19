# Agent Guidance – `tests/integration/`

**Scope precedence:** Follow this file after `tests/AGENTS.md` when working on integration tests.
Coordinate with the relevant runtime directory `AGENTS.md` files for behavioural expectations.

## Modernization Priorities
- Port legacy SimpleTest suites to modern PHPUnit or Pest structures while preserving coverage. Note
  outstanding migrations in the Outstanding Work Log.
- Ensure integration tests cover both CLI and HTTP entry points, especially setup wizard flows and
  SDK interactions.
- Keep host/URL helper scenarios in sync with runtime expectations; expand coverage when new
  normalization helpers are introduced.
- Mock external services thoughtfully and document any required credentials or manual steps.

## Structural Guidance
- Organize tests by feature area, mirroring runtime directory structures where practical.
- Store reusable fixtures and builders in dedicated helper classes within this directory.

## Testing & Checks
- Run the relevant PHPUnit/Pest commands (or legacy harness) locally before submitting changes and
  record results in summaries.
- Execute `php tools/detect_duplicates.php tests/integration` periodically to keep helper code DRY.
- Keep `php tests/integration/duplicate_audit_test.php` passing after edits to setup configuration or
  environment helpers; it enforces duplicate-free scaffolding for wizard logic.
- Run `php tests/integration/php_lint_audit_test.php` to persist syntax-audit reports whenever PHP
  files change; the test writes `tmp/audit/php_lint_audit.json` for later review.

## Outstanding Work Log
- Track remaining legacy harness migrations, missing fixtures, and integration gaps uncovered by
  modernization work.
- Note additional public-suffix examples here if the helper coverage requires more fixtures.
- Document upcoming duplicate-audit targets (e.g., runtime URL helpers) once the existing wizard
  directories remain clean across releases.

## Audit Summary
- Pending lint audit captured for `tests/integration`. Run `php tools/php_lint_audit.php tests/integration` to log per-file results in `tmp/audit/php_lint_audit.json` and document follow-up fixes.
- Continue cataloguing PHPFox Legacy, Dolphin, and Cheetah feature gaps relevant to this scope during modernization.
