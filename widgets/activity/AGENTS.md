# Agent Guidance – `widgets/activity/`

**Scope precedence:** Obey this file for all activity widget components before consulting
`widgets/AGENTS.md`. Coordinate with `lib/NF/AGENTS.md` for feed URL helpers and with
`tests/AGENTS.md` when adjusting integration coverage.

## Modernization Priorities
- Ensure RSS/Atom exports emit standards-compliant XML without relying on legacy template headers.
  Normalize encoding and timezone handling across controllers and templates.
- Centralize feed aggregation logic so duplicate queries are avoided. Document remaining duplication
  in the Outstanding Work Log with pointers to shared helpers.
- Add hooks for notification and real-time update systems, noting TODO items until the backend
  services exist.

## Structural Guidance
- Keep controllers thin and delegate heavy lifting to service/helper classes. Record follow-up tasks
  when files still contain mixed responsibilities.
- Separate presentation templates by format (HTML, XML, JSON) and document expected variables in the
  template header comments.

## Testing & Checks
- Add regression coverage for feed serialization under `tests/` when changing output formats.
- Run `php tools/detect_duplicates.php widgets/activity` after modifications to ensure shared code is
  consolidated.

## Outstanding Work Log
- Track pending websocket/real-time integration steps, notification hooks, or pagination issues that
  require additional work.

## Audit Summary
- Pending lint audit captured for `widgets/activity`. Run `php tools/php_lint_audit.php widgets/activity` to log per-file results in `tmp/audit/php_lint_audit.json` and document follow-up fixes.
- Continue cataloguing PHPFox Legacy, Dolphin, and Cheetah feature gaps relevant to this scope during modernization.
