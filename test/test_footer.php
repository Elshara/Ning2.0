<?php
// @todo refactor this into an autorun style like that in the current SimpleTest
//       implementation.  With that, a file is included at the top of the file,
//       then all new classes defined after that which are subclasses of UnitTestCase
//       are executed

// @todo change this name to something less specific [Travis S. 2008-09-19]
if (defined('CMDLINE_TESTING')) {
	return;
}

require_once SIMPLETEST_PATH . 'reporter.php';
require_once SIMPLETEST_PATH . 'xml.php';

class BazelXmlReporter extends XmlReporter
{
    private function convertToCData($text, $element) {
        return preg_replace(
            "/<{$element}>(.*)<\/{$element}>/",
            "<{$element}><![CDATA[\$1]]></{$element}>",
            $text
        );
    }

    function paintPass($message) {
        ob_start();
        parent::paintPass($message);
        $buffer = ob_get_clean();
        echo $this->convertToCData($buffer, 'pass');
    }

    
    function paintFail($message) {
        ob_start();
        parent::paintFail($message);
        $buffer = ob_get_clean();
        echo $this->convertToCData($buffer, 'fail');
    }

    function paintError($message) {
        ob_start();
        parent::paintError($message);
        $buffer = ob_get_clean();
        echo $this->convertToCData($buffer, 'error');
    }

    function paintException($message) {
        ob_start();
        parent::paintFail($message);
        $buffer = ob_get_clean();
        echo $this->convertToCData($buffer, 'exception');
        return $this->convertToCData(parent::paintPass(), 'exception');
    }

    function paintSkip($message) {
        ob_start();
        parent::paintFail($message);
        $buffer = ob_get_clean();
        echo $this->convertToCData($buffer, 'skip');
        return $this->convertToCData(parent::paintPass(), 'skip');
    }
}

preg_match('@(\w+)\.php@', $_SERVER['REQUEST_URI'], $matches);
$testClass = $matches[1];
$test = new $testClass;
$reporter = new HtmlReporter();
if (isset($_GET['xml'])) {
    $too_large = array(
        'Syntax20CmdlineTest' => true,
        'I18N02Test' => true,
    );
    if (isset($too_large[$testClass])) {
        ini_set('memory_limit', -1);
    }
    $reporter = new BazelXmlReporter();
    $test->run($reporter);
} elseif ($_GET['json'] != 'yes') {
    $test->run($reporter); ?>
    <br />
    <a href="?">[Re-run this test]</a>
    <a href="/test/AllUnitTests.php">[Run all tests]</a>
    <a href="/test/index.php">[Test list]</a>
<?php
} else {
    header('Content-Type: text/plain');
    ob_start();
    $test->run($reporter);
    preg_match('@</h1>(.*)</body>@s', $x = ob_get_contents(), $matches);
    ob_end_clean();
    $json = new NF_JSON();
    echo '(' . $json->encode(array('success' => $reporter->getFailCount() + $reporter->getExceptionCount() == 0, 'html' => '<div>' . $matches[1] . '</div>')) . ')';
}
