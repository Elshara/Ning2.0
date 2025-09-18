# Agent Guidance – `widgets/profiles/`

**Scope precedence:** Follow this file before consulting `widgets/AGENTS.md` when working on profile
widgets. Coordinate with `lib/AGENTS.md` for account services and with
`widgets/groups/AGENTS.md` where membership affects visibility.

## Modernization Priorities
- Migrate profile editing to modular form builders that support custom fields and validation rules.
  Document remaining monolithic controllers in the Outstanding Work Log.
- Ensure embed/export controllers sanitize data and emit standards-compliant HTML/JSON.
- Integrate with centralized notification and permissions systems as they are modernized, noting
  dependencies here.

## Structural Guidance
- Separate public, private, and admin views into dedicated controllers/templates. Record follow-up
  tasks when responsibilities remain combined.
- Store reusable profile field definitions in shared helpers with clear documentation.

## Testing & Checks
- Add coverage for profile privacy checks, embed rendering, and messaging flows.
- Run `php tools/detect_duplicates.php widgets/profiles` after modifications to detect reusable
  patterns.

## Outstanding Work Log
- Track pending privacy enhancements, profile field builder milestones, and messaging integrations.
