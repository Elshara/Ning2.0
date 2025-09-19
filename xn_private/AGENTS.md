# Agent Guidance – `xn_private/`

**Scope precedence:** Follow this file before root guidance for sensitive or private assets stored in
`xn_private/`.

## Modernization Priorities
- Verify whether each private asset is still required. Remove obsolete items after confirming no
  dependencies remain.
- Document missing private resources that installations expect.

## Structural Guidance
- Keep credentials or secrets out of version control. Provide sample templates instead and document
  their usage here.

## Outstanding Work Log
- Record pending removals, replacements, or security reviews for this directory.

## Audit Summary
- Pending lint audit captured for `xn_private`. Run `php tools/php_lint_audit.php xn_private` to log per-file results in `tmp/audit/php_lint_audit.json` and document follow-up fixes.
- Continue cataloguing PHPFox Legacy, Dolphin, and Cheetah feature gaps relevant to this scope during modernization.
