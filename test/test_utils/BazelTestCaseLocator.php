<?php

// @todo test
// @todo break into its own class and create a BazelTestCaseFilterIterator
class BazelTestCaseLocator extends FilterIterator
{
    private $_path = '';
    protected $_pattern = '/.*Test.php$/';

    // @todo allow for different paths and patterns
    public function __construct() {
        $this->_path = realpath(dirname(__FILE__) . '/..');
        parent::__construct(new RecursiveFileIterator($this->_path));
    }

    public function accept() {
        $item = $this->getInnerIterator();
        if ($item->isDir()) {
            return true;
        }
        if (dirname($item->getRealpath()) == $this->_path) {
            return false;
        }
        if ($this->skipThis($item)) {
            return false;
        }
        return preg_match($this->_pattern, $item->getRealPath());
    }

    private function skipThis($item) {
        return strpos($item->getRealPath(), 'js/adapter') !== false;
    }

    public function getArray() {
        // @todo cache this with an ability to flush it
        $return = array();
        foreach ($this as $file) {
            $return[] = $this->filterFileName($file);
        }
        return $return;
    }

    protected function filterFileName($file) {
        return str_replace($_SERVER['DOCUMENT_ROOT'] . '/test/', '', $file->getRealPath());
    }
}

abstract class BazelUnitTestByDirectoryLocator extends BazelTestCaseLocator
{

    /**
     * An array of the name of directories that include unit tests that we want
     * to use.
     *
     * @var array
     */
    protected $_paths = array();

    public function __construct() {
        parent::__construct();
        $this->_pattern = "/(" . implode('|', $this->_paths) . ")\/.*Test.php$/";
    }
}

/**
 * Locates only known unit tests
 */
class BazelUnitTestCaseLocator extends BazelUnitTestByDirectoryLocator
{
    /**
     * @inheritdoc
     */
    protected $_paths = array(
        'activity',
        'admin',
        'events',
        'feed',
        'forum',
        'group',
        'html',
        'main',
        'music',
        'notes',
        'opensocial',
        'page',
        'photo',
        'profiles',
        'video',
        'XG',
    );
}

/**
 * Locates only known "clean up" tests - i18n, dependency, syntax, etc.
 */
class BazelCleanUpTestCaseLocator extends BazelUnitTestByDirectoryLocator
{
    /**
     * @inheritdoc
     */
    protected $_paths = array(
        'i18n',
        'dependency',
        'syntax',
    );
}

/**
 * stolen from Liz Smith's Advent Calendar post
 *
 * todo break into a separate file and test
 */
class RecursiveFileIterator extends RecursiveIteratorIterator
{
    /**
     * Takes a path to a directory, checks it, and then recurses into it.
     * @param $path directory to iterate
     */
    public function __construct($path)
    {
        // Use realpath() and make sure it exists; this is probably overkill, but I'm anal.
        $path = realpath($path);

        if (!file_exists($path)) {
            throw new Exception("Path $path could not be found.");
        } elseif (!is_dir($path)) {
            throw new Exception("Path $path is not a directory.");
        }

        // Use RecursiveDirectoryIterator() to drill down into subdirectories.
        parent::__construct(new RecursiveDirectoryIterator($path));
    }
}

