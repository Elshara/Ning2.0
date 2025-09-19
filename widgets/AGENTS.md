# Agent Guidance – `widgets/`

**Scope precedence:** Follow this file before the repository root or other directory instructions
when working inside `widgets/`. Coordinate with `lib/AGENTS.md` for shared helpers.

## Modernization Priorities
- Refactor widget controllers and helpers into smaller, testable units; aim for one controller or
  helper per file.
- Replace duplicated templates or PHP snippets with shared partials. Use `php tools/detect_duplicates.php
  widgets` regularly.
- Eliminate deprecated template constructs (e.g., inline XML declarations, legacy string offsets) and
  record outstanding fixes below.

## Structural Guidance
- Keep presentation assets (templates, CSS, JS) segregated by widget and ensure each subdirectory
  includes a README or notes about outstanding modernization tasks.
- Document permissions, notifications, or extensibility hooks that need to be implemented for parity
  with modern expectations.

## Testing & Checks
- Run PHP linting and duplicate scans after altering widget code.
- When adjusting data flow, add or update coverage in `tests/` and note manual verification steps
  within this file if automated tests are not yet available.

## Outstanding Work Log
- Track unfinished refactors, missing functionality, and code removal tasks discovered during widget
  work.

## Audit Summary
- Pending lint audit captured for `widgets`. Run `php tools/php_lint_audit.php widgets` to log per-file results in `tmp/audit/php_lint_audit.json` and document follow-up fixes.
- Continue cataloguing PHPFox Legacy, Dolphin, and Cheetah feature gaps relevant to this scope during modernization.
