# Agent Guidance – `widgets/video/`

**Scope precedence:** Apply this file within the video widget before referencing parent widget
instructions. Coordinate with `lib/NF/AGENTS.md` for transcoding/storage helpers and with
`widgets/photo/AGENTS.md` for shared gallery UX patterns.

## Modernization Priorities
- Audit embedded player code for HTML5 compliance and HTTPS-safe embeds. Replace deprecated Flash or
  object embeds with modern iframe/video elements.
- Centralize transcoding and thumbnail generation workflows, recording infrastructure requirements in
  the Outstanding Work Log.
- Ensure playlists, RSS exports, and share links rely on normalized URL helpers compatible with PHP 8.4.

## Structural Guidance
- Separate ingestion, metadata, and playback responsibilities into dedicated services. Document any
  combined files and proposed decomposition steps below.
- Store provider-specific adapters in their own files to simplify maintenance.

## Testing & Checks
- Add regression coverage for playlist serialization and embed URL construction. Capture manual QA
  steps for streaming scenarios not yet automated.
- Run `php tools/detect_duplicates.php widgets/video` to identify reusable media logic.

## Outstanding Work Log
- Track pending provider integrations, DRM considerations, and adaptive bitrate requirements.

## Audit Summary
- Pending lint audit captured for `widgets/video`. Run `php tools/php_lint_audit.php widgets/video` to log per-file results in `tmp/audit/php_lint_audit.json` and document follow-up fixes.
- Continue cataloguing PHPFox Legacy, Dolphin, and Cheetah feature gaps relevant to this scope during modernization.
