# Agent Guidelines for Ning2.0

These instructions apply to the entire repository unless a more specific `AGENTS.md` is added in a
subdirectory.

## Development Principles
- Target **PHP 8.2+** runtime behaviour (the project is exercised against PHP 8.4). Avoid
  deprecated patterns (e.g., dynamic properties, string offset array access) and prefer modern
  language features when safe.
- Preserve backward compatibility with the existing bootstrap (`index.php` ➝ `bootstrap.php`
  ➝ `lib/index.php`). Favor incremental refactors over large rewrites.
- Prefer pure helpers and dependency-injected services over new global state. Reuse the
  shared utilities in `lib/NF/` or the setup environment helpers when possible.
- Maintain clear separation between runtime logic and setup wizard behaviour. Shared detection
  or validation code belongs in `lib/NF/` (runtime) or `setup/src/` (wizard).

## Coding Style
- Follow PSR-12 style for new PHP code (4-space indentation, braces on new lines, strict
  comparisons, no trailing whitespace).
- Type-hint parameters and return values where possible. Use nullable and union types only when
  required by behaviour.
- Avoid suppressing errors with `@`. Instead, detect failure states explicitly and handle them.
- Keep functions focused. Prefer extracting helpers instead of extending large conditional blocks.

## Testing & Tooling
Before submitting changes that touch PHP or configuration logic, run:

1. `composer validate`
2. `find . -name '*.php' -print0 | xargs -0 -n1 php -l`

If a step is not applicable (e.g., `composer.json` removed), state why in your summary rather than
skipping silently.

## Documentation
- Update `README.md` or relevant docs when behaviour, requirements, or workflows change.
- Describe new configuration options or environment variables so administrators can adopt them.

## Assets & Structure
- Keep generated files (e.g., `config/app.php`, `vendor/`) out of version control unless the
  project documentation explicitly requires them.
- When adding new directories, include a short README.md that explains their purpose.

Adhering to these guidelines will help maintain consistency while we continue modernizing Ning2.0.
