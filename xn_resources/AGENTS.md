# Agent Guidance – `xn_resources/`

**Scope precedence:** Apply this guidance before the root instructions within `xn_resources/`.

## Modernization Priorities
- Ensure resources (images, styles, assets) align with current UI expectations and remove obsolete
  items.
- Replace duplicated assets with shared references. Use `php tools/detect_duplicates.php
  xn_resources` to locate redundant files or metadata.

## Structural Guidance
- Organise assets by type and document dependencies or build steps in accompanying README files.
- Track missing assets required by modernised widgets or layouts.

## Outstanding Work Log
- Note assets slated for replacement, removal, or addition here.

## Audit Summary
- Pending lint audit captured for `xn_resources`. Run `php tools/php_lint_audit.php xn_resources` to log per-file results in `tmp/audit/php_lint_audit.json` and document follow-up fixes.
- Continue cataloguing PHPFox Legacy, Dolphin, and Cheetah feature gaps relevant to this scope during modernization.
