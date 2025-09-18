# Agent Guidance – `widgets/music/`

**Scope precedence:** Obey this file before `widgets/AGENTS.md` for music widget components.
Coordinate with `widgets/video/AGENTS.md` for shared media presentation patterns and with
`lib/AGENTS.md` for audio processing helpers.

## Modernization Priorities
- Update playlist exports to use PHP-rendered XML and JSON that satisfy modern specs. Record any
  outstanding encoding issues below.
- Validate uploads for audio codecs, bitrates, and metadata. Document infrastructure needs for
  transcoding or streaming services in the Outstanding Work Log.
- Harmonize notification hooks with the platform-wide activity system once available.

## Structural Guidance
- Separate upload handling, playlist management, and playback rendering into distinct modules.
- Keep reusable UI components (player controls, playlist tables) in shared partials for reuse across
  widgets.

## Testing & Checks
- Add tests for playlist serialization and permission checks around uploads and playback.
- Run `php tools/detect_duplicates.php widgets/music` to ensure audio helpers remain centralized.

## Outstanding Work Log
- Track remaining streaming integrations, rights management requirements, and UX improvements.
