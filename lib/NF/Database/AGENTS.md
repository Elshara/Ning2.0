# Agent Guidance – `lib/NF/Database/`

**Scope precedence:** Apply these requirements to database helpers before consulting
`lib/NF/AGENTS.md` or the repository root guidance.

## Modernization Priorities
- Centralize PDO connection handling and transition legacy MySQL access points to use the shared
  factory. Record remaining direct `mysql_*` or `mysqli_*` usage here with follow-up tasks.
- Ensure helpers validate configuration arrays defensively so the runtime can surface actionable
  errors instead of emitting PHP notices.
- Keep helpers framework-agnostic; they should return value objects or arrays rather than modifying
  globals directly. Document any unavoidable global state in the outstanding work log.

## Testing & Verification
- Add integration coverage for connection bootstrap logic (valid credentials, invalid credentials,
  missing extensions) inside `tests/integration/` as functionality expands.
- Execute `php tools/detect_duplicates.php lib/NF/Database` after edits to confirm connection logic
  remains consolidated.

## Outstanding Work Log
- Migrate legacy database initialization code in widgets and scripts to use the shared PDO factory.
- Implement connection pooling or read/write splitting as part of the broader database modernization
  roadmap.

## Audit Summary
- Pending lint audit captured for `lib/NF/Database`. Run `php tools/php_lint_audit.php lib/NF/Database` to log per-file results in `tmp/audit/php_lint_audit.json` and document follow-up fixes.
- Continue cataloguing PHPFox Legacy, Dolphin, and Cheetah feature gaps relevant to this scope during modernization.
