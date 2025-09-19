# Agent Guidance – `widgets/groups/`

**Scope precedence:** Use this file for the groups widget hierarchy before deferring to
`widgets/AGENTS.md`. Coordinate with `lib/AGENTS.md` for shared membership services and with
`widgets/profiles/AGENTS.md` when group membership affects profile data.

## Modernization Priorities
- Update controllers to enforce role-based permissions and document outstanding checks in the
  Outstanding Work Log.
- Normalize invitation, RSVP, and notification flows with the central permissions system when it is
  introduced. Record dependencies here to keep the roadmap current.
- Eliminate legacy global state from controllers by injecting services for queries and membership
  management.

## Structural Guidance
- Separate admin vs member views into distinct controllers/templates. Note pending decompositions
  below when controllers still multiplex multiple modes.
- Keep SQL or query-building logic inside dedicated helper classes for easier testing.

## Testing & Checks
- Add unit tests for membership state transitions and integration tests for invitations/notifications.
- Run `php tools/detect_duplicates.php widgets/groups` after edits to highlight reusable patterns.

## Outstanding Work Log
- Track remaining permission checks, notification hooks, and migration steps toward modular services.
