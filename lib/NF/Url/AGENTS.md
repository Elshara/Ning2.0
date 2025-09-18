# Agent Guidance – `lib/NF/Url/`

**Scope precedence:** Apply these notes to helpers within `lib/NF/Url/` before falling back to
`lib/NF/AGENTS.md` and the repository root. Keep this file synchronized with the setup wizard's
`setup/src/Environment/AGENTS.md` guidance when logic is shared.

## Modernization Priorities
- Keep host, port, and authority helpers isolated and side-effect free so they can be reused by both
  runtime code and the installer.
- When moving additional routines out of `UrlHelpers.php`, document the migration here and update the
  outstanding work log so future contributors know what still needs to be decomposed.
- Ensure new helpers include thorough PHPDoc describing accepted inputs (IPv6, forwarded headers,
  etc.) to avoid regressions when invoked by proxy-aware contexts.

## Testing & Verification
- Extend the integration coverage in `tests/integration/` with new cases whenever helper behaviour
  changes. Capture missing scenarios (e.g., exotic proxy headers) in the log below.
- Continue running `php tools/detect_duplicates.php lib/NF/Url` after edits to confirm routines stay
  deduplicated across runtime and setup layers.

## Outstanding Work Log
- Extract base-path normalization helpers into this directory and replace the legacy implementations
  inside `UrlHelpers.php`.
- Backfill PHPUnit coverage for IPv6-with-port and punycode host detection edge cases.
