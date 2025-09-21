# Agent Guidelines for Ning2.0

These instructions apply to the entire repository unless a more specific `AGENTS.md` is added in a
subdirectory. Every substantial directory **must** contain a companion `AGENTS.md` that tailors
these expectations to the local context. When creating a new directory, add the directory-specific
`AGENTS.md` and cross-reference it from neighbouring scopes so the guidance network stays current.

## Development Principles
- Target **PHP 8.2+** runtime behaviour (the project is exercised against PHP 8.4). Avoid
  deprecated or end-of-life language patterns and replace them with modern, forward-compatible
  constructs.
- Prefer incremental modernization: split monolithic files into cohesive modules, keep one class
  or function per file when practical, and document follow-up refactors in the local `AGENTS.md`.
- When porting behaviour from PHPFox Legacy, Dolphin Remake, or Cheetah, start with isolated
  helpers or services that can be exercised by existing tests. Document mapping notes between the
  source platform and the Ning module inside the relevant directory guidance before landing code.
- Preserve backward compatibility with the existing bootstrap (`index.php` ➝ `bootstrap.php`
  ➝ `lib/index.php`) unless a local scope specifically authorises removal of legacy entry points.
- Prefer pure helpers and dependency-injected services over new global state. Reuse the shared
  utilities in `lib/NF/` or the setup environment helpers when possible and record missing
  functionality that still needs to be implemented.
- Maintain clear separation between runtime logic and setup wizard behaviour. Shared detection
  or validation code belongs in `lib/NF/` (runtime) or `setup/src/` (wizard).
- Keep accessibility and HTML5 semantics in mind when touching templates. Record gaps that block
  WCAG compliance in the scoped `AGENTS.md` so future passes can address them alongside SEO needs.
- When you discover redundant or superseded files, remove them only after confirming their
  behaviour is replaced elsewhere. Record removals and outstanding gaps in the relevant
  `AGENTS.md`.

## Coding Style
- Follow PSR-12 style for new PHP code (4-space indentation, braces on new lines, strict
  comparisons, no trailing whitespace).
- Type-hint parameters and return values where possible. Use nullable and union types only when
  required by behaviour.
- Avoid suppressing errors with `@`. Instead, detect failure states explicitly and handle them.
- Keep functions focused. Prefer extracting helpers instead of extending large conditional blocks.
- De-duplicate logic aggressively. When identical or near-identical code is encountered, consolidate
  it into shared helpers and document the cleanup in the local `AGENTS.md`.

## Testing & Tooling
Before submitting changes that touch PHP or configuration logic, run:

1. `composer validate`
2. `find . -name '*.php' -print0 | xargs -0 -n1 php -l`
3. `php tools/detect_duplicates.php`
4. `php tests/integration/duplicate_audit_test.php` when touching setup configuration or environment
   helpers to ensure the new file-audit gate stays green.
5. `php tools/php_lint_audit.php` to capture per-file syntax audit results. Investigate any failures
   logged to `tmp/audit/php_lint_audit.json` before completing your work.

If a step is not applicable (e.g., `composer.json` removed), state why in your summary rather than
skipping silently. The duplicate scanner flags repeated lines and blocks that need consolidation;
address or document each reported item before completing your work.

## Planning & Workflow
- Maintain the "Expected Work Plan" list below. Update it whenever milestones are completed or
  new follow-up work is identified so future agents share the same roadmap.
- Keep the worktree merge-ready: remove conflict markers immediately, and ensure each set of
  changes is committed cleanly on top of the latest `master` so downstream merges stay painless.
- Run the PHP lint sweep and any affected unit/integration suites after meaningful changes; record
  the outcomes in your summary so audits remain traceable.
- When code references missing dependencies or files, restore or implement them instead of leaving
  gaps—fresh installs must succeed without manual patching.
- Prefer linear history updates over GitHub's "Update branch" merges. Synchronize workspaces with
  the following cadence before starting a new batch of edits:

  ```bash
  git fetch origin
  git switch <feature-branch>
  git reset --hard origin/<feature-branch>
  git rebase origin/master
  git push --force-with-lease
  ```

  Rebasing locally (or within the task shell) keeps diffs focused and prevents the cascading merge
  conflicts that the UI button introduces.
- Enable Git's rerere cache locally (`git config --global rerere.enabled true`) so repeated conflict
  resolutions are applied automatically across Codex sessions.

### Expected Work Plan
1. Eliminate PHP 8.4 runtime notices and deprecation warnings across the legacy helpers and
   controllers, prioritising functions with outdated signatures or optional/required parameter
   ordering issues.
2. Audit the command-line tooling and test harness for compatibility with modern PHP, updating
   deprecated string interpolation, iterator interfaces, and similar language-level breaks.
3. Expand automated coverage gradually by re-enabling or porting the historic SimpleTest suites to
   modern PHPUnit equivalents, keeping parity with the existing behaviour during the transition.
4. Replace duplicated or conflicting logic with shared helpers. Track outstanding duplicate
   cleanups in the directory-specific `AGENTS.md` files.
5. Continue decomposing monolithic helpers like `lib/NF/UrlHelpers.php` into domain-specific files
   under `lib/NF/Url/`, updating the scoped guidance as new utilities are introduced.
6. Document new conventions and configuration expectations in `README.md` or the relevant widget
   docs whenever behaviour changes so administrators can follow along.
7. Record missing files or functionality uncovered during modernization and either implement them
   or create follow-up tasks in the appropriate `AGENTS.md`.
8. Transition the runtime to PDO-based database access using the shared connection factory and map
   outstanding migrations away from flat-file configuration.
9. Verify templates reference relocated static assets under `assets/` and record any remaining
   legacy paths in the relevant directory guidance.
10. Stage import candidates from PHPFox Legacy, Dolphin Remake, and Cheetah by cataloguing their
    modules, database dependencies, and builder tooling in the matching Ning directories before
    attempting a direct port.

## Documentation
- Update `README.md` or relevant docs when behaviour, requirements, or workflows change.
- Describe new configuration options or environment variables so administrators can adopt them.
- Each directory-level `AGENTS.md` must reference neighbouring scopes and outline the order of
  precedence between them so contributors know which guidance to follow first.

## Assets & Structure
- Keep generated files (e.g., `config/app.php`, `vendor/`) out of version control unless the
  project documentation explicitly requires them.
- When adding new directories, include a short README.md that explains their purpose.
- Prefer adding missing files or scaffolding that unlocks incomplete features over leaving stubs.
  Note any unfinished implementations in the local `AGENTS.md` along with recommended next steps.

Adhering to these guidelines will help maintain consistency while we continue modernizing Ning2.0.

## Audit Summary
- Pending lint audit captured for `repository`. Run `php tools/php_lint_audit.php` to log per-file results in `tmp/audit/php_lint_audit.json` and document follow-up fixes.
- Continue cataloguing PHPFox Legacy, Dolphin, and Cheetah feature gaps relevant to this scope during modernization.
