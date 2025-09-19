# Agent Guidance – `lib/NF/`

**Scope precedence:** Follow this guidance for all files inside `lib/NF/` before consulting
`lib/AGENTS.md` or the repository root. Coordinate with `setup/src/Environment/AGENTS.md` when
sharing detection logic with the installer and reference `widgets/` guidance for UI integrations.

## Modernization Priorities
- Keep each helper focused on environment detection, URL normalization, or bootstrap support.
  Consolidate duplicate header parsing or sanitizer logic discovered elsewhere in the runtime.
- Replace legacy globals with pure functions that accept explicit parameters. Document any
  remaining shared state in the Outstanding Work Log.
- Ensure new helpers are safe for CLI execution and PHP 8.4 strict types. Add type hints and
  parameter validation to guard against malformed inputs from proxies or misconfigured servers.

## Structural Guidance
- Group helpers by concern (e.g., request metadata, filesystem checks) and record future
  decompositions below when a file still contains multiple responsibilities.
- When adding new helpers, include usage notes in the PHPDoc block so other subsystems understand
  expectations around sanitized inputs and return formats.

## Testing & Checks
- Extend or create integration tests in `tests/` that exercise helpers under multiple proxy header
  permutations. Capture missing scenarios in the Outstanding Work Log.
- Run `php tools/detect_duplicates.php lib/NF` after edits to confirm shared routines are not being
  reintroduced elsewhere.

## Outstanding Work Log
- Host and authority helpers now live in `lib/NF/Url/HostUtils.php`; continue migrating base-path
  normalization and forwarded-header parsing routines into the dedicated subdirectory.
- Track adoption of the database connection factory (`lib/NF/Database/ConnectionFactory.php`) and
  note remaining legacy MySQL entry points that still bypass PDO.

## Audit Summary
- Pending lint audit captured for `lib/NF`. Run `php tools/php_lint_audit.php lib/NF` to log per-file results in `tmp/audit/php_lint_audit.json` and document follow-up fixes.
- Continue cataloguing PHPFox Legacy, Dolphin, and Cheetah feature gaps relevant to this scope during modernization.
