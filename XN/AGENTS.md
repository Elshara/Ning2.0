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

## Audit Summary
- Pending lint audit captured for `xn`. Run `php tools/php_lint_audit.php xn` to log per-file results in `tmp/audit/php_lint_audit.json` and document follow-up fixes.
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

## Audit Summary
- Pending lint audit captured for `XN`. Run `php tools/php_lint_audit.php XN` to log per-file results in `tmp/audit/php_lint_audit.json` and document follow-up fixes.
- Continue cataloguing PHPFox Legacy, Dolphin, and Cheetah feature gaps relevant to this scope during modernization.
