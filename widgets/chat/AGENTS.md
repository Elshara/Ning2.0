# Agent Guidance – `widgets/chat/`

**Scope precedence:** Apply this file within the chat widget before referencing parent widget
instructions. Coordinate with `lib/NF/AGENTS.md` for presence or session helpers and with
`tests/AGENTS.md` for realtime coverage expectations.

## Modernization Priorities
- Replace polling logic with event-driven abstractions where possible. Document remaining legacy
  dependencies and required infrastructure upgrades in the Outstanding Work Log.
- Harden user presence detection and message sanitization for PHP 8.4 and modern browser standards.
- Prepare interfaces for future WebSocket or SSE backends while keeping the current AJAX flow
  functional.

## Structural Guidance
- Keep user/session helpers in `lib/helpers` and rendering templates separated by device context.
  Record decomposition plans when a file mixes responsibilities.
- Document API contracts (payloads, endpoints) for chat interactions so mobile or third-party clients
  can adopt them.

## Testing & Checks
- Add automated coverage for message formatting, rate limiting, and permissions checks. Capture
  manual QA steps for realtime flows that lack automation.
- Run `php tools/detect_duplicates.php widgets/chat` alongside the standard lint passes.

## Outstanding Work Log
- Track migration steps toward websocket/event-stream implementations and moderation tooling gaps.
