# Agent Guidance – `lib/ning/`

**Scope precedence:** Apply this file to everything beneath `lib/ning/` before deferring to
`lib/AGENTS.md` or the repository root. When editing SDK classes, also consult
`lib/ning/SDK/AGENTS.md`; for compatibility shims, read `lib/ning/compat/AGENTS.md` (create if
needed) and note cross-directory impacts.

## Modernization Priorities
- Preserve compatibility with historical Ning SDK contracts while progressively adding strict typing
  and PHP 8.4-safe patterns. Document signature changes that could affect upstream consumers.
- Replace duplicated REST and serialization logic with shared services. Surface remaining
  duplication in the Outstanding Work Log with actionable next steps.
- Audit external API dependencies. When stubs are discovered, either implement missing behaviour or
  capture the gap here with references to affected modules.

## Structural Guidance
- Keep entity, service, and query classes in distinct subdirectories. If legacy files mix
  responsibilities, plan the decomposition and record it below with owners or follow-up tasks.
- Prefer immutable value objects for request/response payloads to reduce hidden side effects when the
  legacy stack integrates with these classes.

## Testing & Checks
- Add integration coverage under `tests/integration/` when updating REST behaviour to avoid
  regressions. Record manual verification steps when automated coverage is not yet feasible.
- Run `php tools/detect_duplicates.php lib/ning` and targeted linting after each change set.

## Outstanding Work Log
- Use this space to track missing endpoints, serialization formats to revisit, or coordination needed
  with `widgets/` and `WWF/` consumers.

## Audit Summary
- Pending lint audit captured for `lib/ning`. Run `php tools/php_lint_audit.php lib/ning` to log per-file results in `tmp/audit/php_lint_audit.json` and document follow-up fixes.
- Continue cataloguing PHPFox Legacy, Dolphin, and Cheetah feature gaps relevant to this scope during modernization.
