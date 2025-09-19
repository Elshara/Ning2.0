# Agent Guidance – `lib/ning/SDK/`

**Scope precedence:** Apply this guidance after reading `lib/ning/AGENTS.md`. It overrides parent
instructions for SDK entities, services, and query builders. Coordinate with `tests/integration/`
when modifying API responses or request generation.

## Modernization Priorities
- Keep SDK classes PSR-4 friendly and ready for future namespace adoption. Record remaining
  classmap constraints in the Outstanding Work Log.
- Add explicit return types, nullable hints, and value object wrappers to eliminate PHP 8.4
  warnings. Maintain backwards-compatible method signatures by introducing new methods when
  necessary.
- Centralise HTTP transport behaviour in `Services/RestService.php`. Remove duplicated cURL or
  stream code and capture pending refactors here.

## Structural Guidance
- Separate DTO-like structures (entities, attributes) from service orchestration. When models contain
  business logic, extract that logic into dedicated helpers and note the follow-up in this file.
- Document expected field sets and validation requirements for each entity so widget and WWF layers
  can rely on consistent payloads.

## Testing & Checks
- Expand `tests/integration/ning_sdk_test.php` (or add new cases) whenever modifying request/response
  handling. Record manual API verification steps when live endpoints are required.
- Run `php tools/detect_duplicates.php lib/ning/SDK` alongside the global lint sweep before
  submitting changes.

## Outstanding Work Log
- Track missing endpoint coverage, unimplemented filters, or serialization bugs uncovered during
  modernization.

## Audit Summary
- Pending lint audit captured for `lib/ning/SDK`. Run `php tools/php_lint_audit.php lib/ning/SDK` to log per-file results in `tmp/audit/php_lint_audit.json` and document follow-up fixes.
- Continue cataloguing PHPFox Legacy, Dolphin, and Cheetah feature gaps relevant to this scope during modernization.
