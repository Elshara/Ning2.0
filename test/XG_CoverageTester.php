<?php

/**
 * Tool for coverage testing: monitoring lines of code or logic branches to
 * confirm that they are executed during testing.
 *
 * Put the following line at the top of index.php:
 *
 *         require_once $_SERVER['DOCUMENT_ROOT'] . '/test/XG_CoverageTester.php';
 *
 * Then put the following "probes" throughout your code - whatever you want to monitor:
 *
 *         XG_CoverageTester::probe();
 *
 * XG_CoverageTester will create a report called /coverage_report.txt showing which probes
 * have executed. On subsequent requests, it will update the report with new probe hits.
 *
 * If you add more probes, or if you simply want to start again,
 * delete or clear coverage_report.txt so that it gets rebuilt.
 */
class XG_CoverageTester {

    /** Whether findProbes() was called */
    private static $searchedForProbes = false;

    /** ID => boolean, where ID is of the form FilePath:LineNumber */
    private static $lines = null;

    /** Whether to write to the logfile */
    public static $logging = false;

    /**
     * Flags the current line of the current file as done.
     */
    public static function probe() {
        $backtrace = debug_backtrace();
        $filePath = $backtrace[0]['file'];
        $lineNumber = $backtrace[0]['line'];
        self::log($filePath, $lineNumber);
        $lines = self::getLines();
        self::$lines[$filePath . ':' . $lineNumber] = true;
    }

    /**
     * Logs the current file path and line number, if logging is turned on.
     *
     * @param $filePath  path to the PHP file containing the current probe
     * @param $lineNumber  1-based line number of the current probe
     */
    public static function log($filePath, $lineNumber) {
        if (! self::$logging) { return; }
        static $currentUrlLogged = false;
        if (! $currentUrlLogged) {
            $currentUrlLogged = true;
            error_log('');
            error_log(XG_HttpHelper::currentUrl());
        }
        error_log(basename($filePath) . ':' . $lineNumber . ' - ' . $filePath);
    }

    /**
     * Returns IDs of all lines being monitored and whether they are done.
     *
     * @return array  ID => boolean, where ID is of the form FilePath:LineNumber
     */
    public static function getLines() {
        if (is_null(self::$lines)) {
            self::$lines = file_exists(self::getReportFilePath()) ? self::readReportFile() : self::findProbes();
        }
        return self::$lines;
    }

    /**
     * Updates the report file with the latest lines that have been done.
     */
    public static function writeReportFile() {
        if (file_exists(self::getReportFilePath())) {
            copy(self::getReportFilePath(), self::getReportFilePath() . '.bak');
        }
        $lines = self::getLines();
        $report =
"XG_CoverageTester Report  -  " . (self::$searchedForProbes ? 'Built' : 'Updated') . " " . date('c', time()) . "
When you add new probes, clear this file so it will get rebuilt, adding the new probes.
=====================================================================\n";
        foreach($lines as $line => $done) {
            $report .= ($done ? 'DONE' : '    ') . ' - ' . $line . "\n";
        }
        file_put_contents(self::getReportFilePath(), $report);
    }

    /**
     * Parses the report file, or if it looks empty, calls findProbes().
     *
     * @return array  ID => boolean, where ID is of the form FilePath:LineNumber
     */
    public static function readReportFile() {
        $contents = file_get_contents(self::getReportFilePath());
        if (strlen($contents) < 5) { return self::findProbes(); }
        $lines = array();
        $dividerFound = false;
        foreach (explode("\n", $contents) as $x) {
            if (strpos($x, '=====') !== false) {
                $dividerFound = true;
                continue;
            }
            if (! $dividerFound) { continue; }
            if (! $x) { continue; } // Ignore empty lines [Jon Aquino 2007-11-15]
            $parts = explode(' - ', $x, 2);
            $lines[$parts[1]] = $parts[0] == 'DONE';
        }
        return $lines;
    }

    /**
     * Locates the XG_CoverageTester::probe() calls in the source code.
     *
     * @return array  ID => false, where ID is of the form FilePath:LineNumber
     */
    public static function findProbes() {
        self::$searchedForProbes = true;
        $lines = array();
        foreach(self::globr($_SERVER['DOCUMENT_ROOT'], '*.php') as $filePath) {
            if (strpos($filePath, 'XG_CoverageTester.php') !== false) { continue; }
            if (strpos($filePath, 'Syntax8Test.php') !== false) { continue; }
            $contents = file_get_contents($filePath);
            if (strpos($contents, 'XG_CoverageTester::probe') === false) { continue; }
            $lineNumber = 1;
            foreach (explode("\n", $contents) as $line) {
                if (strpos($line, 'XG_CoverageTester::probe') !== false) {
                    $lines[$filePath . ':' . $lineNumber] = false;
                }
                $lineNumber++;
            }
        }
        return $lines;
    }

    /**
     * Returns the path to the report file.
     *
     * @return string  an absolute file path
     */
    public static function getReportFilePath() {
        return $reportFilePath = $_SERVER['DOCUMENT_ROOT'] . '/coverage_report.txt';
    }

    /**
     * Recursive version of glob
     *
     * @param $sDir string      Directory to start with.
     * @param $sPattern string  Pattern to glob for.
     * @param $nFlags int      Flags sent to glob.
     * @return array containing all pattern-matched files.
     */
    // From http://ca3.php.net/manual/en/function.glob.php#30238  [Jon Aquino 2007-01-17]
    public static function globr($sDir, $sPattern, $nFlags = NULL) {
        $sDir = escapeshellcmd($sDir);

        // Get the list of all matching files currently in the
        // directory.
        $aFiles = glob("$sDir/$sPattern", $nFlags);

        // Then get a list of all directories in this directory, and
        // run ourselves on the resulting array.  This is the
        // recursion step, which will not execute if there are no
        // directories.
        foreach (glob("$sDir/*", GLOB_ONLYDIR) as $sSubDir) {
            $aSubFiles = self::globr($sSubDir, $sPattern, $nFlags);
            $aFiles = array_merge($aFiles, $aSubFiles);
        }

        // The array we return contains the files we found, and the
        // files all of our children found.
        return $aFiles;
    }

}

register_shutdown_function(array('XG_CoverageTester', 'writeReportFile'));
