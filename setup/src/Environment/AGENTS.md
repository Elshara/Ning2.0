# Agent Guidance – `setup/src/Environment/`

**Scope precedence:** Apply this file after reading `setup/src/AGENTS.md`. It overrides parent
instructions for environment detection helpers and request metadata utilities.

## Modernization Priorities
- Keep detection helpers pure and deterministic. Any reliance on `$_SERVER` should be funnelled
  through dedicated factory methods to simplify testing.
- Normalise proxy headers, schemes, hosts, and paths consistently with the runtime equivalents in
  `lib/NF/`. Document divergences that still need harmonization in the Outstanding Work Log.
- Ensure host sanitation accounts for internationalised domain names (IDN) by delegating to the
  shared punycode helpers before performing validation.
- Use the shared `nf_derive_base_domain`/`nf_derive_slug_from_host` helpers from `lib/NF/Url/`
  instead of duplicating host parsing logic inside the wizard.
- Guard against malformed input (invalid hosts, JSON parsing failures, etc.) with defensive coding
  and descriptive error messages suitable for CLI and HTTP output.

## Structural Guidance
- Store shared constants or enumerations in this directory. If a helper grows beyond a single
  responsibility, split it into multiple files and record the follow-up work below.
- Maintain symmetry between request context objects used by the wizard and those used in runtime
  bootstrapping to avoid configuration drift.

## Testing & Checks
- Extend unit tests that cover header permutations, IPv6 parsing, Cloudflare/Forwarded support, and
  subdirectory detection. Add fixtures representing real-world proxy configurations when issues are
  discovered.
- Run `php tools/detect_duplicates.php setup/src/Environment` and
  `php tests/integration/duplicate_audit_test.php` to ensure detection logic remains centralised and
  audit checks stay green.

## Outstanding Work Log
- Track remaining edge cases (load balancers, unusual proxy headers) and planned sync work with
  `lib/NF/` helpers.
- Mirror future migrations from `lib/NF/UrlHelpers.php` into `lib/NF/Url/` so both the installer and
  runtime rely on the same host and base-path utilities.
- Verify new public-suffix requirements during deployments and update the shared helper list when
  additional regions are needed.
- Capture outstanding work for IDN environments lacking the intl extension so fallback
  strategies stay coordinated with runtime helpers.
- Catalogue candidate environment-detection improvements from PHPFox Legacy, Dolphin Remake, and
  Cheetah so cross-platform proxy support is analysed before implementation.

## Audit Summary
- Pending lint audit captured for `setup/src/Environment`. Run `php tools/php_lint_audit.php setup/src/Environment` to log per-file results in `tmp/audit/php_lint_audit.json` and document follow-up fixes.
- Continue cataloguing PHPFox Legacy, Dolphin, and Cheetah feature gaps relevant to this scope during modernization.
