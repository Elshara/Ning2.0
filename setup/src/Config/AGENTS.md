# Agent Guidance – `setup/src/Config/`

**Scope precedence:** Apply this file to every PHP module under `setup/src/Config/` before consulting `setup/src/AGENTS.md`. Coordinate serializer changes with the runtime guidance in `lib/AGENTS.md` when behaviour must stay aligned after installation.

## Modernization Priorities
- Keep configuration writers and serializers pure: accept normalised data structures, avoid touching `$_SERVER`, and surface clear error strings for the wizard UI and CLI flows.
- Replace ad-hoc file access with reusable helpers that validate directories, capture warning details, and avoid using the error-suppression operator.
- Ensure any new classes can be exercised by unit tests; structure methods to allow dependency injection or mocking of filesystem interactions where appropriate.

## Structural Guidance
- Maintain one class per file. If configuration concerns expand (e.g., builders vs. writers), split them into dedicated files within this directory and reference the relationship here.
- Keep helper methods private unless shared usage is required. Promote shared abstractions into a dedicated interface or trait and document the contract in this scope.

## Testing & Checks
- Add or update tests under `tests/` when changing serialization behaviour. At minimum, run `php tools/detect_duplicates.php setup/src/Config` after edits to ensure helpers remain distinct.
- Validate installer flows manually (CLI and browser) when touching write paths to confirm permissions and error handling surface correctly.

## Outstanding Work Log
- Extract a dedicated configuration builder that assembles the array consumed by the writer so the wizard controller only orchestrates state transitions.
