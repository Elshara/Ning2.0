# Agent Guidance – `widgets/index/`

**Scope precedence:** Use this guidance for the front-page/index widget before referring to
`widgets/AGENTS.md`. Coordinate with `lib/AGENTS.md` for caching helpers and `widgets/activity/`
for feed previews.

## Modernization Priorities
- Modularize dashboard components so administrators can rearrange sections without editing PHP.
- Ensure color/theme helpers operate with modern CSS variables and avoid deprecated string offsets.
- Add hooks for personalization and role-based content targeting; log outstanding dependencies in the
  Outstanding Work Log.

## Structural Guidance
- Separate layout configuration, data aggregation, and template rendering into distinct modules.
- Document default widget slots and provide extension points for new modules to register themselves.

## Testing & Checks
- Add coverage for layout serialization and theming helpers. Capture manual QA steps when CSS or
  layout behaviour changes significantly.
- Run `php tools/detect_duplicates.php widgets/index` after edits to centralize shared components.

## Outstanding Work Log
- Track pending personalization features, theme variable migrations, and caching strategies.
