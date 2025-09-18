# Agent Guidance – `WWF/`

**Scope precedence:** Use this file before consulting the root guidance when editing the WWF
compatibility layer.

## Modernization Priorities
- Gradually replace WWF stubs with modern equivalents while preserving backwards compatibility with
  dependent widgets.
- Identify duplicated behaviours between WWF and `lib/ning/` components. Run `php
  tools/detect_duplicates.php WWF lib/ning` when touching this area.
- Remove deprecated PHP constructs and document any missing features that need modern replacements.

## Structural Guidance
- Keep each class in its own file where possible. When splitting files, update autoloaders and note
  follow-up tasks here.
- Record any removed legacy files and the modules that now provide the functionality.

## Testing & Checks
- Expand or update integration tests under `tests/integration/` to cover WWF interactions.

## Outstanding Work Log
- Track remaining modernization tasks, feature gaps, or duplicate cleanups required for WWF parity.
