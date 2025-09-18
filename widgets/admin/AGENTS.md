# Agent Guidance – `widgets/admin/`

**Scope precedence:** Use this file for administrative widget modules before referring to
`widgets/AGENTS.md`. Coordinate with `lib/AGENTS.md` for permission services and configuration
helpers.

## Modernization Priorities
- Audit management screens for PHP 8.4 compatibility and remove direct superglobal manipulation in
  favour of request objects or dependency injection.
- Centralize reusable admin UI components (tables, filters, flash messages) to maintain consistency
  across widgets.
- Ensure actions validate CSRF tokens and enforce role-based permissions; document any missing checks
  in the Outstanding Work Log.

## Structural Guidance
- Separate controller logic from presentation templates. Introduce view models where templates require
  significant conditional logic.
- Keep configuration persistence helpers in shared services instead of duplicating file/database logic
  in controllers.

## Testing & Checks
- Add tests for permission enforcement and configuration persistence. Record manual QA steps when UI
  workflows change significantly.
- Run `php tools/detect_duplicates.php widgets/admin` alongside linting before completion.

## Outstanding Work Log
- Track remaining modernization tasks, missing permission checks, and UI consolidations.
