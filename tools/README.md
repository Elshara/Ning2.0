# Tooling

This directory houses repository maintenance utilities.

* `detect_duplicates.php` – scans source files for repeated lines and blocks so contributors can
  consolidate legacy logic while modernizing the code base.
* `php_lint_audit.php` – runs `php -l` across repository files, writing a JSON report to `tmp/audit/`
  so each scope can track standalone syntax and autoload issues discovered during modernization.
