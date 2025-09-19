# Agent Guidance – `widgets/notes/`

**Scope precedence:** Apply this file for note/blog widgets before referencing `widgets/AGENTS.md`.
Coordinate with `widgets/page/AGENTS.md` for editor integrations and `lib/AGENTS.md` for tagging
or search helpers.

## Modernization Priorities
- Update the notes editor to use modern sanitization and storage patterns. Record migration plans for
  legacy markup support in the Outstanding Work Log.
- Ensure publishing workflows integrate with notifications, scheduling, and version history once
  those services are available.
- Normalize permalink generation and tagging across notes and other content types.

## Structural Guidance
- Separate authoring, publishing, and presentation logic into distinct modules. Document combined
  controllers and planned decompositions below.
- Store reusable partials for tag lists, author info, and share buttons to avoid duplication.

## Testing & Checks
- Add coverage for draft publishing, permission checks, and permalink routing.
- Run `php tools/detect_duplicates.php widgets/notes` to highlight reusable content components.

## Outstanding Work Log
- Track migration to unified editor experiences, search integration gaps, and analytics hooks.

## Audit Summary
- Pending lint audit captured for `widgets/notes`. Run `php tools/php_lint_audit.php widgets/notes` to log per-file results in `tmp/audit/php_lint_audit.json` and document follow-up fixes.
- Continue cataloguing PHPFox Legacy, Dolphin, and Cheetah feature gaps relevant to this scope during modernization.
