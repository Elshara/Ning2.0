<?php	# $Id: $
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class TestComment extends Comment {
	public static function resetCommentFormat() {
		self::$_commentFormat = NULL;
	}
}
class CommentFormatTest extends UnitTestCase {
    //
	public function check($config, $type, $text, $expected) { # void
		W_Cache::getWidget('main')->privateConfig['commentFormat'] = $config;
		TestComment::resetCommentFormat();
		$formatted = Comment::formatCommentText($type, $text);
		//echo $this->_reporter->_test_stack[1],":";var_dump(Comment::$_commentFormat);
		$this->assertEqual($formatted, $expected);
    }
    public function testNoRules() {
		$this->check('', 'User', '<a><b>abc<c>', '<a><b>abc<c>');
	}
    public function testNoSpecific() {
		$this->check('
			* : order=deny : deny=b
		', 'User', '<a><b>abc<i>', '<a>abc<i>');
	}
    public function testNoStar() {
		$this->check('
			user : order=deny : deny=b
		', 'User', '<a><b>abc<i>', '<a>abc<i>');
	}
	public function testSpecificAndStar() {
		$this->check('
			* : order=deny : deny=a;
			user : order=deny : deny=b
		', 'User', '<a><b>abc<i>', 'abc<i>');
	}
	public function test2SpecificAndStar() {
		$this->check('
			* : order=deny : deny=a;
			post : order=deny : deny=i;
			user : order=deny : deny=b
		', 'User', '<a><b>abc<i>', 'abc<i>');
	}
	public function testOrder() {
		$this->check('
			user : deny=b
		', 'User', '<a><b>abc<c>', '<a><b>abc');
		$this->check('
			user : order=deny : deny=b
		', 'User', '<a><b>abc<i>', '<a>abc<i>');
		$this->check('
			user : order=deny,allow : deny=b : allow=c;
		', 'User', '<a><b>abc<c>', '<a>abc<c>');
		$this->check('
			user : order=allow : allow=c : deny = a
		', 'User', '<a><b>abc<c>', '<a><b>abc<c>');
		$this->check('
			user : order=allow,deny : allow=c,d : deny = a,d
		', 'User', '<a><b>abc<d><c>', '<b>abc<d><c>');
	}
	public function testFallback() {
		$this->check('
			* : order=deny : deny=*;
			user : order = allow : allow=c
		', 'User', '<a><b>abc<c>', 'abc<c>');
		$this->check('
			* : order=deny : deny=*;
			user : order = deny,allow : deny=d : allow=c,d
		', 'User', '<a><b>abc<d><c>', 'abc<c>');
	}
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
?>
