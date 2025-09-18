# Agent Guidance – `tests/integration/`

**Scope precedence:** Follow this file after `tests/AGENTS.md` when working on integration tests.
Coordinate with the relevant runtime directory `AGENTS.md` files for behavioural expectations.

## Modernization Priorities
- Port legacy SimpleTest suites to modern PHPUnit or Pest structures while preserving coverage. Note
  outstanding migrations in the Outstanding Work Log.
- Ensure integration tests cover both CLI and HTTP entry points, especially setup wizard flows and
  SDK interactions.
- Mock external services thoughtfully and document any required credentials or manual steps.

## Structural Guidance
- Organize tests by feature area, mirroring runtime directory structures where practical.
- Store reusable fixtures and builders in dedicated helper classes within this directory.

## Testing & Checks
- Run the relevant PHPUnit/Pest commands (or legacy harness) locally before submitting changes and
  record results in summaries.
- Execute `php tools/detect_duplicates.php tests/integration` periodically to keep helper code DRY.

## Outstanding Work Log
- Track remaining legacy harness migrations, missing fixtures, and integration gaps uncovered by
  modernization work.
