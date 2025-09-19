# Agent Guidance – `widgets/feed/`

**Scope precedence:** Apply this file for feed aggregation widgets before using `widgets/AGENTS.md`.
Coordinate with `lib/NF/AGENTS.md` for HTTP client helpers and `widgets/activity/AGENTS.md` for
shared stream logic.

## Modernization Priorities
- Replace insecure HTTP requests with modern, TLS-enforced clients supporting timeouts and retries.
- Normalize feed parsing (RSS, Atom, JSON) using well-tested libraries or consolidated helpers.
- Provide caching hooks and record outstanding performance tuning tasks in the Outstanding Work Log.

## Structural Guidance
- Separate external feed adapters from rendering templates. Document each adapter's expected payload
  schema and failure handling.
- Keep configuration forms and persistence logic modular so administrators can manage multiple feed
  sources easily.

## Testing & Checks
- Add tests that mock remote feeds to verify parsing and error handling logic.
- Run `php tools/detect_duplicates.php widgets/feed` to keep adapters consistent.

## Outstanding Work Log
- Track planned integrations with centralized caching, rate limiting, and credential storage systems.

## Audit Summary
- Pending lint audit captured for `widgets/feed`. Run `php tools/php_lint_audit.php widgets/feed` to log per-file results in `tmp/audit/php_lint_audit.json` and document follow-up fixes.
- Continue cataloguing PHPFox Legacy, Dolphin, and Cheetah feature gaps relevant to this scope during modernization.
