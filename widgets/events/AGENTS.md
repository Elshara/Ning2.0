# Agent Guidance – `widgets/events/`

**Scope precedence:** Apply this file to the events widget before consulting `widgets/AGENTS.md`.
Coordinate with `widgets/groups/AGENTS.md` for membership-linked visibility and with
`lib/AGENTS.md` for calendar/timezone helpers.

## Modernization Priorities
- Normalize date/time handling using immutable objects and timezone-aware formatting.
- Consolidate RSVP, invitation, and reminder workflows with the platform-wide notification system.
- Ensure maps/location integrations use modern APIs (HTTPS, up-to-date endpoints). Document missing
  providers or migration tasks in the Outstanding Work Log.

## Structural Guidance
- Separate event creation/editing controllers from listing/browsing controllers. Note pending
  decompositions when logic remains combined.
- Store recurring event logic in dedicated services with clear documentation on expected inputs.

## Testing & Checks
- Add unit tests for recurrence calculation, timezone conversion, and permission enforcement.
- Run `php tools/detect_duplicates.php widgets/events` after edits to keep business logic centralized.

## Outstanding Work Log
- Track outstanding calendar integrations, reminder delivery methods, and accessibility improvements.
- Continue migrating remaining event controller actions to `Events_RequestHelper` for POST sanitisation; GET flows for embed and invitation controllers now rely on the helper but form submissions still need to drop raw superglobals.

## Audit Summary
- Pending lint audit captured for `widgets/events`. Run `php tools/php_lint_audit.php widgets/events` to log per-file results in `tmp/audit/php_lint_audit.json` and document follow-up fixes.
- Continue cataloguing PHPFox Legacy, Dolphin, and Cheetah feature gaps relevant to this scope during modernization.
