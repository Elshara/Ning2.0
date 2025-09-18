# Agent Guidance – `XN/`

**Scope precedence:** Apply this file before the root guidance when modifying XN compatibility
wrappers or APIs.

## Modernization Priorities
- Replace legacy Ning/XN shims with modern service abstractions while maintaining API compatibility
  for dependent code.
- Document missing API coverage and plan replacements or removals here.
- Use `php tools/detect_duplicates.php XN` to uncover overlapping functionality with other layers.

## Structural Guidance
- Split large classes into smaller files grouped by domain (e.g., caching, profiles, content).
- Keep autoload metadata updated and note outstanding restructuring work.

## Testing & Checks
- Ensure integration tests cover API changes. Add regression tests for bug fixes.

## Outstanding Work Log
- Record modernization milestones, duplicate removals, and functionality gaps needing attention.
