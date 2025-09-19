# Agent Guidance – `widgets/page/`

**Scope precedence:** Apply this guidance before consulting `widgets/AGENTS.md` when working within
page builder components. Coordinate with `widgets/profiles/AGENTS.md` for shared editor widgets and
`lib/AGENTS.md` for persistence helpers.

## Modernization Priorities
- Move toward modular page-building APIs that support custom fields and reusable layout elements.
  Document remaining monolithic controllers in the Outstanding Work Log with proposed split plans.
- Sanitize user-generated HTML using centralized helpers compatible with PHP 8.4 and modern browser
  standards.
- Expose versioning hooks so collaborative editing can be layered on without rewriting controllers.

## Structural Guidance
- Keep widget definitions, controllers, and templates grouped by page type. Introduce subdirectories
  as new page types are added and document them here.
- Store reusable form components in dedicated helpers rather than duplicating markup across templates.

## Testing & Checks
- Add unit tests for slug generation, permission checks, and custom field persistence. Capture manual
  QA steps for WYSIWYG interactions while automated coverage is built.
- Run `php tools/detect_duplicates.php widgets/page` to enforce component reuse.

## Outstanding Work Log
- Track outstanding collaborative editing features, template refactors, and validation improvements.

## Audit Summary
- Pending lint audit captured for `widgets/page`. Run `php tools/php_lint_audit.php widgets/page` to log per-file results in `tmp/audit/php_lint_audit.json` and document follow-up fixes.
- Continue cataloguing PHPFox Legacy, Dolphin, and Cheetah feature gaps relevant to this scope during modernization.
