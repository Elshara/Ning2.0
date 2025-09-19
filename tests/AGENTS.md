# Agent Guidance – `tests/`

**Scope precedence:** Apply this file before the repository root instructions when adding or
modifying files under `tests/`. Coordinate with `test/AGENTS.md` for legacy harness work.

## Modernization Priorities
- Port legacy SimpleTest coverage to PHPUnit-compatible suites while preserving behaviour.
- Ensure each new module or helper introduced elsewhere includes automated tests.
- Keep fixtures and mocks free of duplicate logic by sharing builders across suites. Run
  `php tools/detect_duplicates.php tests` to locate overlap.

## Structural Guidance
- Organise tests by domain (e.g., `integration/`, `unit/`). Add README files in nested directories to
  describe scope and remaining tasks.
- Document missing tests or flaky cases in the Outstanding Work Log so follow-up agents can address
  them.

## Testing & Checks
- Execute the relevant test groups or scripts touched by your change. Record results in summaries.
- Update Composer or PHPUnit configuration when adding new suites.
- Run `php tests/integration/duplicate_audit_test.php` after modifying setup configuration or
  environment helpers; it serves as the file-audit gate for duplicate detection.

## Outstanding Work Log
- Track pending migrations, missing fixtures, or duplicate cleanups uncovered during testing.
