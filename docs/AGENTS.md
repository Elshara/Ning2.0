# Agent Guidance – `docs/`

**Scope precedence:** Apply this file before the root instructions for documentation within `docs/`.
Coordinate with other directory guides when documenting code they own.

## Documentation Priorities
- Keep documentation synchronized with modernization work, including newly created tooling,
  directory-level `AGENTS.md` files, and removed legacy components.
- Highlight outstanding TODO items, deprecated features, or missing files noted in other scopes.

## Structural Guidance
- Prefer smaller, topic-focused documents. Split large guides into sections and cross-link them.
- Maintain change logs or upgrade notes when behavioural differences are introduced.

## Review & Checks
- Run `php tools/detect_duplicates.php docs` to avoid duplicated prose or conflicting guidance.
- Ensure README updates accompany major refactors elsewhere in the repository.

## Outstanding Work Log
- Record documentation debt, missing diagrams, or areas needing clarification.
