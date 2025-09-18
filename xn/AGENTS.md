# Agent Guidance – `xn/`

**Scope precedence:** Follow this file before the root guidance when updating the legacy `xn/`
resources.

## Modernization Priorities
- Replace legacy scripts with modular equivalents while maintaining API compatibility for dependent
  code.
- Document removed files and note where modern replacements reside.
- Use `php tools/detect_duplicates.php xn` to detect overlapping utilities with `lib/` or `XN/`.

## Structural Guidance
- Split multi-purpose scripts into smaller functions/classes located in dedicated files.
- Keep any configuration samples up to date with current expectations.

## Outstanding Work Log
- Track pending removals, modernization tasks, or missing functionality needed in this directory.
