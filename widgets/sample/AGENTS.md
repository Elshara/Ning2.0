# Agent Guidance – `widgets/sample/`

**Scope precedence:** Apply this file to the sample widget before consulting `widgets/AGENTS.md`.
Use this directory as a template for new widgets and keep instructions current with platform
conventions.

## Modernization Priorities
- Keep the sample widget minimal, demonstrating PSR-12 code style, dependency injection, and modern
  template practices.
- Update examples whenever platform-wide conventions change (routing, permissions, testing).

## Structural Guidance
- Provide clear documentation inside this directory that explains how to scaffold new widgets using
  the sample as a base. Update code comments and this file when onboarding steps change.

## Testing & Checks
- Ensure the sample widget includes references to unit/integration test scaffolding so new modules
  follow best practices.
- Run `php tools/detect_duplicates.php widgets/sample` when adjusting boilerplate to keep examples
  singular.

## Outstanding Work Log
- Record upcoming updates to the scaffold (e.g., new hooks, configuration patterns) so future agents
  know when to refresh the example.

## Audit Summary
- Pending lint audit captured for `widgets/sample`. Run `php tools/php_lint_audit.php widgets/sample` to log per-file results in `tmp/audit/php_lint_audit.json` and document follow-up fixes.
- Continue cataloguing PHPFox Legacy, Dolphin, and Cheetah feature gaps relevant to this scope during modernization.
