# Agent Guidance – `instances/`

**Scope precedence:** Apply this file before the root guidance when working within `instances/`.

## Modernization Priorities
- Keep sample instance definitions up to date with current configuration structures.
- Remove obsolete or redundant example files and replace them with modern equivalents.
- Use `php tools/detect_duplicates.php instances` to avoid diverging configurations.

## Structural Guidance
- Prefer declarative configuration files organised per instance or feature.
- Document missing examples or required scaffolding for new functionality here.

## Outstanding Work Log
- List example updates, missing files, or duplication cleanups that remain outstanding.

## Audit Summary
- Pending lint audit captured for `instances`. Run `php tools/php_lint_audit.php instances` to log per-file results in `tmp/audit/php_lint_audit.json` and document follow-up fixes.
- Continue cataloguing PHPFox Legacy, Dolphin, and Cheetah feature gaps relevant to this scope during modernization.
