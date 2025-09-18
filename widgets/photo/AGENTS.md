# Agent Guidance – `widgets/photo/`

**Scope precedence:** Use this guidance for photo widget components before referring to
`widgets/AGENTS.md`. Coordinate with `lib/NF/AGENTS.md` for storage helpers and with
`widgets/video/AGENTS.md` when sharing gallery functionality.

## Modernization Priorities
- Update upload pipelines to validate MIME types, enforce size limits, and prepare for pluggable
  storage backends. Document missing validation or cloud integration tasks below.
- Normalize EXIF handling and timezone normalization so galleries display consistent metadata.
- Replace deprecated string offset access and ensure templates emit standards-compliant HTML5.

## Structural Guidance
- Separate upload processing, metadata extraction, and presentation layers into discrete classes.
  Note pending decompositions when files still combine concerns.
- Keep template partials for list vs detail views distinct and document expected variables in header
  comments.

## Testing & Checks
- Add unit tests for metadata parsing and helper functions. Provide integration coverage for upload
  flows once filesystem abstractions are introduced.
- Run `php tools/detect_duplicates.php widgets/photo` to surface reusable gallery logic.

## Outstanding Work Log
- Track pending storage abstraction work, responsive image improvements, and sharing integrations.
