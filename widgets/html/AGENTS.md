# Agent Guidance – `widgets/html/`

**Scope precedence:** Follow this file for custom HTML widget work before referencing
`widgets/AGENTS.md`. Coordinate with `widgets/page/AGENTS.md` for shared content-editing helpers.

## Modernization Priorities
- Replace legacy WYSIWYG integrations with secure, configurable editors that enforce content security
  policies. Record migration steps and outstanding tasks below.
- Sanitize user-provided HTML server-side using centralized helpers to prevent XSS and injection
  issues under PHP 8.4.
- Provide localization hooks for content so multi-language deployments can reuse the widget.

## Structural Guidance
- Keep editor configuration, sanitization logic, and rendering templates in dedicated modules.
- Document allowable HTML elements/attributes and expose extension points for administrators to tweak
  policies without editing code.

## Testing & Checks
- Add coverage for sanitizer behaviour and template rendering using representative snippets.
- Run `php tools/detect_duplicates.php widgets/html` after modifications to avoid duplicated
  sanitization code.

## Outstanding Work Log
- Track planned editor upgrades, accessibility improvements, and localization support tasks.

## Audit Summary
- Pending lint audit captured for `widgets/html`. Run `php tools/php_lint_audit.php widgets/html` to log per-file results in `tmp/audit/php_lint_audit.json` and document follow-up fixes.
- Continue cataloguing PHPFox Legacy, Dolphin, and Cheetah feature gaps relevant to this scope during modernization.
