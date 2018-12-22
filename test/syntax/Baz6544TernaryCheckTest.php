<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class Baz6544TernaryCheckTest extends UnitTestCase
{
    // TODO: refactor into outside class with its own test cases
    public function containsInvalidTernary($string) {
        return preg_match(
            "/return [^ ]+ *\n\s*\\?/",
            $string
        );
        return false;
    }
        
    public function testEnsureCatchesFailures() {
        $invalidTernary = "return foo\n" .
                          "    ? 'foo'\n" .
                          "    : 'bar';";
        $this->assertTrue(
            $this->containsInvalidTernary($invalidTernary),
            "double check the containsInvalidTernary() method"
        );
    }

    public function testEnsureCatchesWithWhitespaceInTernary() {
        $invalidTernary = "return foo \n" .
                          "    ? 'foo'\n" .
                          "    : 'bar';";
        $this->assertTrue(
            $this->containsInvalidTernary($invalidTernary),
            "check to ensure it ignores whitespace"
        );
    }

    public function testEnsureCatchesPass() {
        $validTernary = "return foo ?\n" .
                        "    'bar' :\n" .
                        "    'foo';";
        $this->assertFalse(
            $this->containsInvalidTernary($validTernary),
            "double check the containsInvalidTernary() method for parsing valid ternaries"
        );
    }

    public function testAssertThatImproperTernaryIsNotUsed() {
        $path = dirname(__FILE__) . '/../../xn_resources/widgets/shared/js/messagecatalogs/*.js';
        $files = glob($path);
        foreach ($files as $file) {
            if (is_dir($file) || substr($file, -3) != '.js') {
                continue;
            }
            $this->assertFalse(
                $this->containsInvalidTernary(file_get_contents($file)),
                'checking file [' . $file . '] for invalid ternary usage'
            );
        }
    }
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';

