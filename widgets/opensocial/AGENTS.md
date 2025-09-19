# Agent Guidance – `widgets/opensocial/`

**Scope precedence:** Use this file for OpenSocial widget integrations before deferring to
`widgets/AGENTS.md`. Coordinate with `OpenSocial/AGENTS.md` and `lib/ning/AGENTS.md` when working
on shared APIs.

## Modernization Priorities
- Audit gadget rendering for compatibility with modern OAuth/OpenID providers and HTTPS-only
  deployments. Document deprecated endpoints that require replacement.
- Consolidate shared signing, authentication, and container logic to avoid duplication across
  widgets.
- Prepare adapters for future REST-based gadget interactions and record outstanding tasks below.

## Structural Guidance
- Keep container bootstrapping separated from gadget-specific renderers. Document dependencies on the
  broader platform in this file.
- Store configuration schemas for third-party integrations centrally so administrators can manage
  credentials securely.

## Testing & Checks
- Add integration coverage with mock OpenSocial providers where feasible. Note manual QA steps when
  live credentials are required.
- Run `php tools/detect_duplicates.php widgets/opensocial` to maintain shared helper clarity.

## Outstanding Work Log
- Track pending authentication upgrades, provider-specific quirks, and documentation updates.

## Audit Summary
- Pending lint audit captured for `widgets/opensocial`. Run `php tools/php_lint_audit.php widgets/opensocial` to log per-file results in `tmp/audit/php_lint_audit.json` and document follow-up fixes.
- Continue cataloguing PHPFox Legacy, Dolphin, and Cheetah feature gaps relevant to this scope during modernization.
