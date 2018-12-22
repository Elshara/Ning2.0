<?php
/**
 * 	Test for the proper quoting for xg_text/xg_html arguments
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class Syntax28CmdlineTest extends CmdlineTestCase {
	public function testQuoting() {
		return; //!!
		$dirOffset = strlen(NF_APP_BASE)+1;

		#$files = array(NF_APP_BASE . '/lib/XG_TemplateHelpers.php');
		$files = XG_TestHelper::globr(NF_APP_BASE, '*.php');
        foreach($files as $file) {
			if (strpos($file,'XG_AppearanceTemplateHelper.php')) continue;
			if (strpos($file,'/XG_Message')) continue; //
			if (strpos($file,'/fragment_logItem.php')) continue; // skip this garbage

			// Skip all non-templates
			$isTemplate = (bool)strpos($file, '/templates/');
            $contents = self::getFileContent($file);
            $this->tokens = token_get_all($contents);
			$this->pos = 0;
			$this->count = count($this->tokens);

			$badCalls = 0;
			while( list($func,$args) = $this->getNextCall() ) {
				if ($func == 'xg_text') {
					/*if ($isTemplate) {
						if (count($args) != 1) {
							// xg_text is not recommened in templates
							$this->assertTrue(FALSE, "$file: $func($argsStr) - more than 1 arg for xg_text() in the template file");
							continue;
						}
					}*/
					continue; // skip xg_text() calls in non-templates
				}
				$badArgs = 0;
				for($i = 1; $i<count($args);$i++) { // skip first argument - resource name
					$a = $args[$i];
					// Replace all "safe" variables
					$a = preg_replace('/\$\w+Link\b/','', $a);			// special case: replace when fixed
					$a = preg_replace('/\$(\w+->)*num\w+\b/','', $a);	// probably numbers
					$a = preg_replace('/\$(\w+->)*\w+Count\b/','', $a);	// probably counters

					if (false !== strpos($a,'$')) { // If argument contains a variable...
						$badArgs++;
						$args[$i] = '<b style="color:red">' . qh($args[$i]) . '</b>';
					}
				}
				if ($badArgs) {
					// Cannot use assertion here, because all my pretty html is quoted by simpletest :)
					echo substr($file,$dirOffset).": $func(".join(", ",$args).")<br>";
-					$badCalls++;
				}
			}
			$this->assertTrue($badCalls == 0, $file);
        }
    }
    //
    public function getNextCall() { # list<func,args>
		$state = 0;				// DFA state
		$args = array();		// list of xg_html/text arguments
		$arg = '';				// current argument
		$func = '';				// xg_text or xg_html?
		$stack = array();		// braces stack
		$stackPtr = 0;			// braces stack ptr
		$quotePos = 0;			// quoting function most outer position (we ignore nested quoting functions)
		$quotePtr = 0;			// braces level for quoting function

		static $skip = array(
			T_ENCAPSED_AND_WHITESPACE => 1,
			T_WHITESPACE => 1,
			T_COMMENT => 1,
			T_DOC_COMMENT => 1,
			T_ML_COMMENT => 1,
		);
		static $safeFuncs = array(
			'xg_elapsed_time' => 1,
			'count' => 1,
			'intval' => 1,
			'qh' => 1,
			'xnhtmlentities' => 1,
			'htmlentities' => 1,
			'xg_xmlentities' => 1,
			'xg_date' => 1,
			'xg_userlink' => 1,
		);

		for(; $this->pos < $this->count; $this->pos++) {
			$t =& $this->tokens[$this->pos];
			if ( is_array($t) && isset($skip[$t[0]]) ) {
				continue;
			}
			switch($state) {
				case 0: // out
					if (is_array($t) && $t[0] == T_STRING && ($t[1] == 'xg_text' || $t[1] == 'xg_html')) {
						$func = $t[1];
						$state = 1;
					}
					break;
				case 1: // expect first (
					if ($t != '(') {
						return; // ignore
					}
					$state = 2;
					break;
				case 2: // arg
					$append = 1;
					if (is_array($t)) {
						if ($t[0] == T_STRING && !$quotePos && isset($safeFuncs[ $t[1] ])) {
							$quotePos = strlen($arg) + strlen($t[1]);
							$quotePtr = $stackPtr;
						}
					} else {
						switch($t) {
							case ',':
								if (!$stackPtr) {
									$args[] = $arg; $arg = '';
									$quotePos = 0;
									$append = 0;
									// next token
								}
								break;
							case '(':
							case '[':
								$stack[$stackPtr++] = $t;
								break;
							case ')':
								if (!$stackPtr) {
									break 3; // end of call
								}
								if ($stack[--$stackPtr] != '(') throw new Exception("Missing (");
								if ($quotePos && $stackPtr == $quotePtr) {
									$arg = substr($arg, 0, $quotePos) . '(...)';
									$quotePos = 0;
								}
								break;
							case ']':
								if ($stack[--$stackPtr] != '[') throw new Exception("Missing [");
								break;
							default: break;
						}
					}
					if ($append) {
						$arg .= is_array($t) ? $t[1] : $t;
					}
					break;
				default: throw new Exception("Oops!");
			}
		}
		if ($arg) {
			$args[] = $arg;
		}
		return $func ? array($func, $args) : NULL;
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
?>
