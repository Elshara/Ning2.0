# Agent Guidance – `widgets/forum/`

**Scope precedence:** Use this guidance for forum components before referencing `widgets/AGENTS.md`.
Coordinate with `widgets/groups/AGENTS.md` for membership rules and with `lib/AGENTS.md` for
moderation services.

## Modernization Priorities
- Modernize pagination, search integration, and notification hooks to align with platform-wide
  services. Document remaining gaps in the Outstanding Work Log.
- Sanitize user content consistently and migrate BBCode/legacy markup handlers to modern parsers.
- Replace legacy SQL access patterns with parameterized helpers compatible with PDO/MySQLi.
- Funnel widget controllers through `Forum_RequestHelper` for request sanitization, keeping track of
  unported actions below until the conversion is complete.

## Structural Guidance
- Separate thread, post, and moderation controllers into focused classes. Note pending decompositions
  when controllers remain multi-purpose.
- Keep template partials for list vs detail views distinct and document required variables.

## Testing & Checks
- Add coverage for permission checks, moderation actions, and search indexing triggers.
- Run `php tools/detect_duplicates.php widgets/forum` alongside linting to maintain shared helpers.

## Outstanding Work Log
- Track migration to unified search, moderation queue implementations, and websocket updates.
- Topic, index, and bulk controllers still access `$_GET`/`$_POST` directly; migrate them to
  `Forum_RequestHelper` in follow-up passes once the comment flows stabilise.

## Audit Summary
- Pending lint audit captured for `widgets/forum`. Run `php tools/php_lint_audit.php widgets/forum` to log per-file results in `tmp/audit/php_lint_audit.json` and document follow-up fixes.
- Continue cataloguing PHPFox Legacy, Dolphin, and Cheetah feature gaps relevant to this scope during modernization.
