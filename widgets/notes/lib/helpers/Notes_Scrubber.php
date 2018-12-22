<?php

class Notes_Scrubber {
    const NS_XHTML = 'http://www.w3.org/1999/xhtml';
    public static $allowedLinkSchemes = array('http' => true, 'https' => true, 'ftp' => true, 'mailto' => true,
                                              'rtsp' => true, 'mms' => true);
    public static $allowedSrcSchemes = array('http' => true, 'https' => true);

    protected static $_baseAttributes = array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'align' => true);

    public static $allowed = array(
      'div' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'align' => true),
      'p' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'align' => true),
      'h1' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'align' => true),
      'h2' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'align' => true),
      'h3' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'align' => true),
      'h4' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'align' => true),
      'h5' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'align' => true),
      'h6' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'align' => true),
      'ul' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'type' => true, 'compact' => true),
      'ol' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'type' => true, 'compact' => true, 'start' => true),
      'li' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'type' => true, 'value' => true),
      'dl' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'compact' => true),
      'dt' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true),
      'dd' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true),
      'address' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true),
      'hr' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'align' => true, 'noshade' => true, 'size' => true, 'width' => true),
      'pre' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'width' => true, 'xml:space' => true),
      'blockquote' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'cite' => true),
      'center' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true),
      'ins' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'cite' => true, 'datetime' => true),
      'del' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'cite' => true, 'datetime' => true),
      'a' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'charset' => true, 'type' => true, 'name' => true, 'href' => true, 'hreflang' => true, 'rel' => true, 'rev' => true, 'shape' => true, 'coords' => true, 'target' => true),
      'span' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true),
      'bdo' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true),
      'br' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'clear' => true),
      'em' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true),
      'strong' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true),
      'dfn' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true),
      'code' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true),
      'samp' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true),
      'kbd' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true),
      'bar' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true),
      'cite' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true),
      'abbr' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true),
      'acronym' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true),
      'q' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'cite' => true),
      'sub' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true),
      'sup' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true),
      'tt' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true),
      'i' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true),
      'b' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true),
      'big' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true),
      'small' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true),
      'u' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true),
      's' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true),
      'strike' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true),
      'basefont' => array('id' => true, 'size' => true, 'color' => true, 'face' => true),
      'font' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'size' => true, 'color' => true, 'face' => true),
      'object' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'declare' => true, 'classid' => true, 'codebase' => true, 'data' => true, 'type' => true, 'codetype' => true, 'archive' => true, 'standby' => true, 'height' => true, 'width' => true, 'usemap' => true, 'name' => true, 'tabindex' => true, 'align' => true, 'border' => true, 'hspace' => true, 'vspace' => true, 'align' => true),
      'param' => array('id' => true, 'name' => true, 'value' => true, 'valuetype' => true, 'type' => true),
      'img' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'src' => true, 'alt' => true, 'name' => true, 'longdesc' => true, 'height' => true, 'width' => true, 'usemap' => true, 'ismap' => true, 'align' => true, 'border' => true, 'hspace' => true, 'vspace' => true),
      'table' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'summary' => true, 'width' => true, 'border' => true, 'frame' => true, 'rules' => true, 'cellspacing' => true, 'align' => true, 'bgcolor' => true),
      'caption' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'align' => true),
      'colgroup' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'span' => true, 'width' => true, 'align' => true, 'char' => true, 'charoff' => true, 'valign' => true),
      'col' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'span' => true, 'width' => true, 'align' => true, 'char' => true, 'charoff' => true, 'valign' => true),
      'thead' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'align' => true, 'char' => true, 'charoff' => true, 'valign' => true),
      'tfoot' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'align' => true, 'char' => true, 'charoff' => true, 'valign' => true),
      'tbody' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'align' => true, 'char' => true, 'charoff' => true, 'valign' => true),
      'tr' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'align' => true, 'char' => true, 'charoff' => true, 'valign' => true, 'bgcolor' => true),
      'th' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'abbr' => true, 'axis' => true, 'headers' => true, 'scope' => true, 'rowspan' => true, 'colspan' => true, 'align' => true, 'char' => true, 'charoff' => true, 'valign' => true, 'nowrap' => true, 'bgcolor' => true, 'width' => true, 'height' => true),
      'td' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'abbr' => true, 'axis' => true, 'headers' => true, 'scope' => true, 'rowspan' => true, 'colspan' => true, 'align' => true, 'char' => true, 'charoff' => true, 'valign' => true, 'nowrap' => true, 'bgcolor' => true, 'width' => true, 'height' => true),
      'embed' => array('id' => true, 'class' => true, 'style' => true, 'title' => true, 'lang' => true, 'xml:lang' => true, 'dir' => true, 'align' => true, 'allowscriptaccess' => true, 'bgcolor' => true, 'src' => true, 'type' => true, 'width' => true, 'height' => true, 'quality' => true, 'flashvars' => true, 'name' => true, 'scale' => true, 'wmode' => true, 'pluginspage' => true)
    );


    public static function scrub($html, $opts = null) {
        if (! function_exists('tidy_repair_string')) {
            throw new Exception('HTML_Scrubber requires the tidy extension');
        }
        $opts = self::parseOpts($opts);
        $html = self::preTidyCleanup($html);
        $xhtml = tidy_repair_string($html, array('output-xhtml' => true,
                                                 'numeric-entities' => true,
                                                 'wrap' => 0,
                                                 'drop-empty-paras' => false
                                                 ), $opts['encoding']);
        $src = new DOMDocument;
        // PHO-487 [ David Sklar 2006-09-28 ]
        // tidy_repair_string() puts junk onto the end of the document if $html is
        // 31 characters long, so we need to remove that junk if it exists
        $xhtml = preg_replace('@</html>.*$@s', '</html>', $xhtml);

        // Suppress errors from loading wonky HTML, e.g. with bad namespaces
        @$src->loadXML($xhtml);
        $x = new DOMXPath($src);
        $x->registerNamespace('xh',self::NS_XHTML);
        $target = new DOMDocument;
        $targetDiv = $target->createElement('div');
        $target->appendChild($targetDiv);
        $body = $x->query('/xh:html/xh:body')->item(0);
        // Don't scrub if there are no child nodes [ David Sklar 2006-09-28 ]
        if ($body->childNodes) {
            self::scrubChildren($body, $targetDiv, $opts['elements'], $opts['additionalElements']);
        }
        self::postScrubCleanup($target);

        $s = '';
        foreach ($targetDiv->childNodes as $node) {
            $s .= $node->ownerDocument->saveXML($node);
        }
        $s = self::textCleanup($s);
        return $s;
    }

    protected static function parseOpts($opts = null) {
         // Set defaults for options
        if (is_null($opts)) { $opts = array(); }
        if (isset($opts['elements'])) {
            if (is_array($opts['elements'])) {
                // Transform array to an associative array for quicker lookup later
                $tmp = array();
                foreach ($opts['elements'] as $element) { $tmp[$element] = true; }
                $opts['elements'] = $tmp;
            } else {
                // Convert a single string element
                $tmp = $opts['elements'];
                $opts['elements'] = array($tmp => true);
            }
        } else {
            // Default (null) means "allow all elements"
            $opts['elements'] = null;
        }
        /** 'additionalElements' allows the caller to allow the basic set of
        * permitted elements plus new ones */
        $additionalElements = array();
        if (isset($opts['additionalElements']) && is_array($opts['additionalElements'])) {
            foreach ($opts['additionalElements'] as $i => $element) {
                /* If it's a numeric array and the value is a string, treat that
                 * as an allowed element with the standard attributes */
                 if (is_integer($i) && is_string($element)) {
                     $additionalElements[$element] = self::$_baseAttributes;
                 }
                /* If it's an associative array and the key is a string and the value
                 * is an array, treat the key as the element name and the value as the
                 * allowed attributes (+ the base attributes) */
                 else if (is_string($i) && is_array($element)) {
                     $elementName = $i;
                     /* Convert attribute names to associative array */
                     $allowedAttributes = self::$_baseAttributes;
                     foreach ($element as $allowedAttribute) {
                         $allowedAttributes[$allowedAttribute] = true;
                     }
                     $additionalElements[$elementName] = $allowedAttributes;
                 }
            }
        }
        $opts['additionalElements'] = $additionalElements;

        if (! isset($opts['encoding'])) {
            $opts['encoding'] = 'utf8';
        }
        return $opts;
    }

    protected static function scrubChildren($src,$dst, $elementSubset = null, $additionalElements = array()) {
        foreach ($src->childNodes as $srcNode) {
            $isTextNode = (($srcNode->nodeType == XML_TEXT_NODE) ||
                           ($srcNode->nodeType == XML_CDATA_SECTION_NODE));
            if (! $isTextNode) {
                // Ignore non-XHTML
                if ($srcNode->namespaceURI != self::NS_XHTML) {
                    continue;
                }

                // Ignore non-allowed elements
                if ((! isset(self::$allowed[$srcNode->nodeName])) && (! isset($additionalElements[$srcNode->nodeName]))) {
                    continue;
                }
            }

            // If an element subset is supplied, ignore elements not in that subset
            if ($elementSubset && (! isset($elementSubset[$srcNode->nodeName]))) {
                continue;
            }

            if ($srcNode->nodeType == XML_TEXT_NODE) {
                $dstNode = $dst->ownerDocument->createTextNode($srcNode->textContent);
                $dst->appendChild($dstNode);
            } else if ($srcNode->nodeType == XML_CDATA_SECTION_NODE) {
                $dstNode = $dst->ownerDocument->createCDATASection($srcNode->textContent);
                $dst->appendChild($dstNode);
            } else {
                $dstNode = $dst->ownerDocument->createElement($srcNode->nodeName);
                $dst->appendChild($dstNode);
                // Copy any allowed attributes over
                if ($srcNode->attributes) {
                    foreach ($srcNode->attributes as $attribute) {
                        $attributeOK = isset(self::$allowed[$srcNode->nodeName][$attribute->nodeName]) ||
                            (isset($additionalElements[$srcNode->nodeName]) && isset($additionalElements[$srcNode->nodeName][$attribute->nodeName]));
                        // Only allow links that don't run inline scripts
                        if (($srcNode->nodeName == 'a') && ($attribute->name == 'href')) {
                            $url = parse_url($attribute->value);
                            $attributeOK = self::schemeIsOK($url, self::$allowedLinkSchemes);
                        }
                        if ($attribute->name == 'src') {
                            $url = parse_url($attribute->value);
                            $attributeOK = self::schemeIsOk($url, self::$allowedSrcSchemes);
                        }
                        // NING-6123: try to weed out bad things from style attributes
                        if ($attribute->name == 'style') {
                            if (preg_match('@expression\(@u', $attribute->value)) {
                                $attributeOK = false;
                            }
                            else if (preg_match('@url\(@u', $attribute->value)) {
                                $attributeOK = false;
                            }
                        }
                        // EXA-2529:
                        if ($attributeOK) {
                            $dstNode->setAttribute($attribute->nodeName, $attribute->value);
                        }
                    }
                }
                // Copy any allowed children over
                self::scrubChildren($srcNode, $dstNode, $elementSubset, $additionalElements);
            }
        }
    }

    protected static function schemeIsOk($url, $allowedSchemes) {
        static $ctrlChars = null;
        if (is_null($ctrlChars)) {
            $ctrlChars = array(chr(0),chr(1),chr(2),chr(3),chr(4),chr(5),chr(6),chr(7),
                               chr(8),chr(9),chr(10),chr(11),chr(12),chr(13),chr(14),
                               chr(15),chr(16),chr(17),chr(18),chr(19),chr(20),chr(21),
                               chr(22),chr(23),chr(24),chr(25),chr(26),chr(27),chr(28),
                               chr(29),chr(30),chr(31),' ',"\t","\r","\n");
        }
        if (isset($url['scheme'])) {
            return isset($allowedSchemes[$url['scheme']]);
        }
        else if (isset($url['path'])) {
            $path = strtolower(str_replace($ctrlChars,array(), rawurldecode($url['path'])));
            if (preg_match('@^(javascript|vbscript|xss):@u', $path)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Before passing the HTML to tidy, perform any necessary cleanups
     *
     */
    protected static function preTidyCleanup($html) {
        /* NING-5975: Prevent tidy from putting <object> in <head> rather than <body> */
        $html = '<body>' . $html . '</body>';
        /* NING-4775: Handle unclosed quotes in attribute values */
		$data = preg_split('/(<\/?[A-Za-z0-9:-]+|<!|[\'">])/',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
		$max = count($data);
		if ($max % 2) {
			$data[] = ''; // add fake delimiter
		}
		$output = '';
		$state = 0; // 0 = out-of-tag; 1 = in-tag; 2 = in-double; 3 = in-single
		for($i = 0; $i<$max; $i+=2) {
			$delim = $data[$i+1];
			switch($delim[0]) {
				case '<':
					if (0 == $state) { $state = 1; }
					break;
				case '>':
					if (1 == $state) { $state = 0; }
					else if (2 == $state) { $delim = '"' . $delim; $state = 1; }
					else if (3 == $state) { $delim = "'" . $delim; $state = 1; }
					break;
				case '"':
					if (1 == $state) { $state = 2; }
					else if (2 == $state) { $state = 1; }
					break;
				case "'":
					if (1 == $state) { $state = 3; }
					else if (3 == $state) { $state = 1; }
					break;
			}
			$output .= $data[$i] . $delim;
		}
		return $output;
    }

    /** After everything has been scrubbed, apply any structural cleanups or
     * transformations that are necessary
     */
    protected static function postScrubCleanup($doc) {
        $x = new DOMXPath($doc);
        /** VID-176: turning off AllowScriptAccess in embed code */
        // First make sure that all 'embed' elements have an 'AllowScriptAccess="never"' attribute
        foreach ($x->query('//embed') as $embed) {
            $embed->setAttribute('allowscriptaccess','never');
        }

        // Make sure each '<object>' has a <param name="allowscriptaccess" value="never" /> child
        foreach ($x->query('//object') as $object) {
            // youtube + IE7 broken xhtml "fix" - BAZ-7255
            // this is the simplest approach (adding a text node after the embed)
            // but we might want to explore cleaner solutions like looking for and
            // reformatting youtube embeds to proper xhtml - [ywh 2008-04-24]
            if ( $object->childNodes ) {
                $numNodes = $object->childNodes->length;
                for ($i = 0; $i < $numNodes; $i++) {
                    if ( $object->childNodes->item($i)->nodeName == 'embed' ) {
                        // not the last child node; use insertBefore on node i+1
                        if ( $i < $numNodes - 1 ) {
                            $object->insertBefore( $doc->createTextNode(' '), $object->childNodes->item($i+1)  );
                        } else {
                            $object->appendChild( $doc->createTextNode(' ') );
                        }
                    }
                }
            }

            // If this <object> has a <param name="allowscriptaccess"/> child set its value to 'never'
            // We must loop through all attributes since XPath can't do case-insensitive matching
            $addParam = true;
            foreach ($x->query('param', $object) as $param) {
                foreach ($param->attributes as $attributeName => $attribute) {
                    if (($attributeName == 'name') && (strtolower($attribute->value) == 'allowscriptaccess')) {
                        $param->setAttribute('value','never');
                        $addParam = false;
                    }
                }
            }
            // Add a <param name="allowscriptaccess" value="never" /> child if necessary
            if ($addParam) {
                $param = $doc->createElement('param');
                $param->setAttribute('name','allowscriptaccess');
                $param->setAttribute('value','never');
                $object->appendChild($param);
            }
        }
    }

    /**
     * Tags that require explicit closing tags, e.g., <div></div>, not <div/>.
     *
     * @see the non-"EMPTY" tags in XHTML Modularization 1.1, http://www.w3.org/TR/2006/WD-xhtml-modularization-20060705/abstract_modules.html
     */
    const TAGS_REQUIRING_CLOSING_TAGS = 'a|abbr|acronym|address|applet|b|bdo|big|blockquote|body|button|caption|center|cite|code|colgroup|dd|del|dfn|dir|div|dl|dt|em|fieldset|font|form|frameset|h1|h2|h3|h4|h5|h6|head|html|i|iframe|ins|kbd|label|legend|li|map|menu|noframes|noscript|object|ol|optgroup|option|p|pre|q|s|samp|script|select|small|span|strike|strong|style|sub|sup|table|tbody|td|textarea|tfoot|th|thead|title|tr|tt|u|ul|var';

    /**
     * After the final HTML string has been built, apply any transformations that are
     * necessary -- these are things that have to happen in the HTML text itself, not
     * in the DOM structure
     */
    protected static function textCleanup($s) {
        /** Give empty <div/>s closing tags */
        $s = preg_replace('@<(' . self::TAGS_REQUIRING_CLOSING_TAGS . ')( [^>]+)?/>@s','<$1$2></$1>', $s);
        return trim($s);
    }
}
