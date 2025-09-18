# Agent Guidance – `OpenSocial/`

**Scope precedence:** Follow this file before consulting root guidance when altering OpenSocial
assets.

## Modernization Priorities
- Identify obsolete specifications and replace or remove them. Document compatibility decisions here.
- Consolidate duplicated API facades with the XN layer. Run `php tools/detect_duplicates.php
  OpenSocial XN` when making changes.
- Ensure OAuth and REST helpers meet contemporary security standards.

## Structural Guidance
- Split large service definitions into self-contained modules per specification area.
- Keep schema or protocol documentation synchronized with implementation changes.

## Testing & Checks
- Add integration tests for OpenSocial endpoints touched by your changes.

## Outstanding Work Log
- Track deprecated features pending removal and modernization follow-ups.
