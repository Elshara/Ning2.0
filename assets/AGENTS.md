# Agent Guidance – `assets/`

**Scope precedence:** Apply this file to everything under `assets/` before following the repository root guidance.

## Structure & Priorities
- Group static assets by type (e.g., CSS, JS) and keep directory depth shallow. Create missing type-specific folders when moving files out of the legacy root.
- Record relocations and outstanding reference updates in the local child `AGENTS.md` files so the asset map stays current.

## Change Log
- 2024-05-13: Created the shared `assets/` container and relocated legacy CSS into `assets/css` for consolidation.

## Open Questions
- Confirm all templates load stylesheets from their new paths; document any remaining root-relative references that need updates.

## Audit Summary
- Pending lint audit captured for `assets`. Run `php tools/php_lint_audit.php assets` to log per-file results in `tmp/audit/php_lint_audit.json` and document follow-up fixes.
- Continue cataloguing PHPFox Legacy, Dolphin, and Cheetah feature gaps relevant to this scope during modernization.
