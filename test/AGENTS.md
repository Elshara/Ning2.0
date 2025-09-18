# Agent Guidance – `test/`

**Scope precedence:** Use this guidance before `tests/AGENTS.md` or the repository root when touching
legacy SimpleTest assets under `test/`.

## Modernization Priorities
- While the legacy harness remains, keep scripts compatible with PHP 8.4 by removing deprecated
  patterns and global state where feasible.
- Identify duplicate scripts or fixtures and either remove them or migrate their behaviour into the
  modern `tests/` suites. Use `php tools/detect_duplicates.php test` to discover overlap.

## Structural Guidance
- Document which files are slated for retirement and where their replacements live.
- Break up monolithic scripts into smaller helpers to ease eventual migration.

## Testing & Checks
- Execute affected command-line scripts after edits and document outcomes.

## Outstanding Work Log
- Maintain a checklist of remaining migration tasks and duplicate cleanups in this section.
