<?php
declare(strict_types=1);

require_once dirname(__DIR__, 1) . '/../tools/php_lint_audit.php';

$summary = nf_run_php_lint_audit();

if ($summary['status'] !== 'ok') {
    fwrite(STDERR, "PHP lint audit reported failures.\n");
    foreach ($summary['failures'] as $failure) {
        fwrite(STDERR, sprintf("- %s\n%s\n", $failure['file'], $failure['output']));
    }
    fwrite(STDERR, sprintf("See %s for the JSON report.\n", $summary['log_path']));
    exit(1);
}

fwrite(STDOUT, sprintf(
    "PHP lint audit passed on %d files. Report stored at %s\n",
    $summary['checked_files'],
    $summary['log_path']
));
