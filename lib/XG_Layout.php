<?php

/**
 *   class for storing a Bazel page layout
 */
abstract class XG_Layout {

    /** Invalidation key indicating that the layout of the main page has been modified. */
    const MAIN_PAGE_LAYOUT_CHANGED = 'main-page-layout-changed';

    /** The name that, with the type, uniquely identifies a layout instance, e.g., 'index', or the content ID of a User object */
    protected $_name;

    /** Returns the layout type. 'homepage' is the default and is the front page layout. 'profiles' is the layout for a user profile page. */
    protected $_type;

    /** The DOM object for this layout */
    public $_layout;

    /** The initial DOM object for this layout */
    protected $_initialLayout;

    /** Runtime configuration parameters for the layout, e.g., viewAsOther */
    protected $_opts;

    /** Layout cache, with keys being the type and the name */
    private static $nameToLayoutMap = array();

    /**
     * Returns a PHP callback specification for the given method name.
     *
     * @param $type string  the layout type. 'homepage' is the default and is the front page layout. 'profiles' is the layout for a user profile page.
     * @param $method string  the method name: load, setup, save, or isOwner
     * @return array  the callback spec
     */
    protected function buildCallback($type, $method) {
        return array($this,"callback_{$method}");
    }

    /**
     * Returns the maximum <embed> width for the given colgroup or column element,
     * or null if this information is not available.
     *
     * @param DOMElement $element  the element, of type "column" or "colgroup"
     * @return integer  the max width for <embed>s, in pixels, or null
     */
    public function getMaxEmbedWidth(DOMElement $element) {
        static $maxEmbedWidthsForColumns = null;
        if (is_null($maxEmbedWidthsForColumns)) { $maxEmbedWidthsForColumns = $this->getMaxEmbedWidthsForColumns(); }
        // We don't support colgroups yet [Jon Aquino 2008-02-29]
        return self::getMaxEmbedWidthProper($element, $this, $maxEmbedWidthsForColumns);
    }

    /**
     * Returns the maximum <embed> width for each column, ordered depth-first.
     */
    protected abstract function getMaxEmbedWidthsForColumns();

    /**
     * Returns the 0-based index position of a column within a layout, or false if the column element is not present
     *
     * @param DOMElement $element  the column element
     * @param XG_Layout $xgLayout  the layout to search
     *
     * @return integer/boolean  the index position or false if the column element is not present
     */
    public static function getColumnIndexInLayout(DOMElement $element, $xgLayout) {
        static $layoutNameToColumns = array();
        $layoutName = $xgLayout->getName();
        if (is_null($layoutNameToColumns[$layoutName])) {
            $xpath = new DOMXPath($xgLayout->getLayout());
            $layoutNameToColumns[$layoutName] = array();
            foreach ($xpath->query('//column') as $column) { $layoutNameToColumns[$layoutName][] = $column; }
        }
        return array_search($element, $layoutNameToColumns[$layoutName], true);
    }

    /**
     * Returns the maximum <embed> width for the given colgroup or column element,
     * or null if this information is not available.
     *
     * @param DOMElement $element  the element, of type "column" or "colgroup"
     * @param XG_Layout $xgLayout  the XG_Layout
     * @param $maxEmbedWidthsForColumns  the max embed width for each column, ordered depth-first
     * @return integer  the max width for <embed>s, in pixels, or null
     */
    protected static function getMaxEmbedWidthProper(DOMElement $element, $xgLayout, $maxEmbedWidthsForColumns) {
        $i = self::getColumnIndexInLayout($element, $xgLayout);
        return $i === false ? null : $maxEmbedWidthsForColumns[$i];
    }

    /**
     * Removes any intra-element whitespace so that the various previousSibling/nextSibling
     * calculations work properly
     *
     * @param $xml string the XML
     */
    protected static function removeWhitespaceBetweenTags($xml) {
        return preg_replace('@>\s+<@u','><',$xml);
    }

    /**
     * Load a new layout or retrieve it from the map
     *
     * @param $name string A name that, with the type, uniquely identifies a layout instance, e.g., 'index', or the content ID of a User object
     * @param $type string optional layout type. 'homepage' is the default and
     *  is the front page layout. 'profiles' is the layout for a user profile
     *  page.
     * @param $opts array optional array of arguments for the layout to do
     *  layout-specific things with.
     */
    public static function load($name, $type = 'homepage', $opts = array()) {
        // Ensure the XG_Layout is instantiated only once during the request,
        // so that the saving (which happens in the destructor) happens once [Jon Aquino 2006-11-17]
        if (! isset(self::$nameToLayoutMap[$type])) {
            self::$nameToLayoutMap[$type] = array();
        }
        if (! isset(self::$nameToLayoutMap[$type][$name])) {
            $class = 'XG_Layout_' . $type;
            if (! class_exists($class)) {
                throw new Exception("No class exists for layout type '$type'");
            }
            self::$nameToLayoutMap[$type][$name] = new $class($name, $type, $opts);
        }
        return self::$nameToLayoutMap[$type][$name];
    }

    /**
     * Create a new layout object by loading it from the appropriate place
     * or building the default
     *
     * @param $name string A name that, with the type, uniquely identifies a
     *         layout instance, e.g., 'index', or the content ID of a User object
     * @param $type string The layout type 'homepage' is the default and
     *  is the front page layout. 'profiles' is the layout for a user profile
     *  page.
     * @param $opts array optional array of arguments for the layout to do
     *  layout-specific things with.
     *  Options for all layouts:
     *  viewAsOther: if true, force the answer to the question 'is the current
     *                  user the layout owner' to be 'no'
     */
    protected function __construct($name, $type, $opts = array()) {

        $this->_name = $name;
        $this->_type = $type;
        $this->_layout = new DOMDocument();
        $this->_opts = $opts;

        $loadCallback = $this->buildCallback($type, 'load');
        $this->_initialLayout = call_user_func($loadCallback, $name);
        if ($this->_initialLayout) {
            $this->_layout->loadXML($this->_initialLayout);
        } else {
            $setupCallback = $this->buildCallback($type,'setup');
            call_user_func($setupCallback, $name);
        }
    }

    /**
     * Destructor
     */
    public function __destruct() {
        $currentLayout = $this->_layout->saveXML();
        if ($currentLayout != $this->_initialLayout && self::layoutXmlValid($currentLayout) && ! defined('UNIT_TESTING')) {
            $saveCallback = $this->buildCallback($this->_type, 'save');
            call_user_func($saveCallback, $currentLayout);
        }
    }

    /**
     * Sanity-checks the layout XML.
     *
     * @param $xml string  return value from DOMDocument->saveXML()
     * @return boolean  whether the XML looks OK
     * @see BAZ-8244
     */
    protected function layoutXmlValid($xml) {
        return mb_strlen(trim($xml)) > mb_strlen('<?xml version="1.0"?>');
    }

    /**
     * Adds a module to the specified parent node in the current layout.
     *
     * @param $widgetName string  name of the widget
     * @param $action string  name of the action in the widget's EmbedController
     * @param $parentPath string  xpath expression for the parent node
     * @param $attributes array  key-value pairs specifying attributes for the new module node
     * @param $append boolean  whether to put the module node at the start or end of the parent's children
     */
    public function addModule($widgetName, $action, $parentPath, $attributes = NULL,
            $append = FALSE) {
        $xpath = new DOMXPath($this->_layout);
        $results = $xpath->query($parentPath);
        $parent = $results->item(0);
        if ($parent) {
            $moduleElement = $this->_layout->createElement('module');
            if ($parent->hasChildNodes() && !$append) {
                $moduleElement = $parent->insertBefore($moduleElement,
                        $parent->firstChild);
            }
            else {
                $moduleElement = $parent->appendChild($moduleElement);
            }
            $moduleElement->setAttribute('widgetName', $widgetName);
            $moduleElement->setAttribute('action', $action);
            $instanceId = $this->nextEmbedInstanceId();
            $moduleElement->setAttribute('embedInstanceId', $instanceId);
            if (is_array($attributes)) {
                foreach ($attributes as $name => $value) {
                    $moduleElement->setAttribute($name, $value);
                }
            }
            return $instanceId;
        }
        return FALSE;
    }

    /**
     * Adds a module to the specified parent node in the current layout ONLY if
     *   a module with the same widget name and action does not already exist
     *   under that parent.
     *
     * @param $widgetName string  name of the widget
     * @param $action string  name of the action in the widget's EmbedController
     * @param $parentPath string  xpath expression for the parent node
     * @param $attributes array  key-value pairs specifying attributes for the new module node
     * @param $append boolean  whether to put the module node at the start or end of the parent's children
     */
    public function addModuleOnce($widgetName, $action, $parentPath, $attributes = NULL,
            $append = FALSE) {
        //  Query for a module under the specified parent with the specified
        //    widget and action names
        $xpath = new DOMXPath($this->_layout);
        $existingNode = $xpath->query($parentPath . '/module'
                . "[@widgetName='$widgetName'][@action='$action']")->item(0);
        if ($existingNode) {
            return $existingNode->getAttribute('embedInstanceId');
        }
        //  None found - proceed to add
        return $this->addModule($widgetName, $action, $parentPath, $attributes,
                $append);
    }

    /**
     * Adds a module immediately before or after the specified node in the current
     *   layout.
     *
     * @param $widgetName string  name of the widget
     * @param $action string  name of the action in the widget's EmbedController
     * @param $nodePath string  xpath expression for the reference node
     * @param $attributes array  key-value pairs specifying attributes for the new module node
     * @param $after boolean  whether to put the new node before or after the reference
     *   node in the parent's child list
     */
    public function insertModule($widgetName, $action, $referencePath, $attributes = NULL,
            $after = FALSE) {
        $xpath = new DOMXPath($this->_layout);
        $results = $xpath->query($referencePath);
        $refNode = $results->item(0);
        $parent = $refNode->parentNode;
        if ($refNode && $parent) {
            $moduleElement = $this->_layout->createElement('module');
            $moduleElement = $parent->insertBefore($moduleElement,
                    ($after ? $refNode->nextSibling : $refNode));
            $moduleElement->setAttribute('widgetName', $widgetName);
            $moduleElement->setAttribute('action', $action);
            $instanceId = $this->nextEmbedInstanceId();
            $moduleElement->setAttribute('embedInstanceId', $instanceId);
            if (is_array($attributes)) {
                foreach ($attributes as $name => $value) {
                    $moduleElement->setAttribute($name, $value);
                }
            }
            return $instanceId;
        }
        return FALSE;
    }

    /**
     * Import the specified DOM element into the document and append it
     *   as a child of the DOM element at newParentPath
     *
     * @param $elementPath DOMElement
     * @param $newParentPath string
     */
    public function importElement($element, $newParentPath, $importFirst=false) {
        $xpath = new DOMXPath($this->_layout);
        $newParent = $xpath->query($newParentPath)->item(0);
        if ($element && $newParent) {
            $element = $this->_layout->importNode($element, TRUE /* deep */);
            if (is_null($element->getAttribute('embedInstanceId'))) {
                $element->setAttribute('embedInstanceId', $this->nextEmbedInstanceId());
            }
            if ($importFirst && $newParent->hasChildNodes()) {
                $newParent->insertBefore($element, $newParent->firstChild);
            } else {
                $newParent->appendChild($element);
            }
        }
    }

    //TODO this routine is very similar to getIteration and setIteration.  Create some general code?
    /**
     * Increments the ID stored in the nextEmbedInstanceId attribute of the top-level <layout/> element
     * in the layout, creating the attribute if it doesn't exist.
     *
     * @return integer  The next ID
     */
    private function nextEmbedInstanceId() {
        // Get the ID from the nextEmbedInstanceId attribute of the top-level <layout/> element
        // in the layout, creating the attribute if it doesn't exist;
        $xpath = new DOMXpath($this->_layout);
        $query = $xpath->query('/layout');
        if ($query->length != 1) { throw new Exception("Layout {$this->_type}/{$this->_name} doesn't have a top-level <layout/>"); }
        $layout = $query->item(0);
        if ($layout->hasAttribute('nextEmbedInstanceId')) {
            $nextEmbedInstanceId = $layout->getAttribute('nextEmbedInstanceId');
        } else {
            $nextEmbedInstanceId = 0;
        }
        $layout->setAttribute('nextEmbedInstanceId',$nextEmbedInstanceId + 1);
        return $nextEmbedInstanceId;
    }

    /**
     * Returns the value of the given property for the module identified by the given ID
     *
     * @param $propertyName string  the name of the property
     * @param $embedInstanceId integer  the ID identifying the module
     * @return string  the value of the property, or null if the property does not yet exist
     */
    public function getEmbedInstanceProperty($propertyName, $embedInstanceId) {
        $propertyNode = self::getEmbedInstancePropertyNode($propertyName, $embedInstanceId);
        return $propertyNode ? $propertyNode->nodeValue : null;
    }

    /**
     * Returns the node for the given property for the module identified by the given ID
     *
     * @param $propertyName string  the name of the property
     * @param $embedInstanceId integer  the ID identifying the module
     * @return the node, or null if the node does not yet exist
     */
    public function getEmbedInstancePropertyNode($propertyName, $embedInstanceId) {
        $xpath = new DOMXPath($this->_layout);
        return $xpath->query("//module[@embedInstanceId='{$embedInstanceId}']/{$propertyName}")->item(0);
    }

    /**
     * Sets the value of the given property for the module identified by the given ID
     *
     * @param $propertyName string  the name of the property
     * @param $propertyValue string  the value of the property
     * @param $embedInstanceId integer  the ID identifying the module
     */
    public function setEmbedInstanceProperty($propertyName, $propertyValue, $embedInstanceId) {
        $xpath = new DOMXPath($this->_layout);
        $moduleNode = $xpath->query("//module[@embedInstanceId='{$embedInstanceId}']")->item(0);
        // Sometimes we try to set a property when we are rendering the sitewide sidebar.
        // That doesn't work because we are in a different XML document.  So skip it.
        if (! $moduleNode) { return; }
        $oldPropertyNode = self::getEmbedInstancePropertyNode($propertyName, $embedInstanceId);
        if ($oldPropertyNode) { $moduleNode->removeChild($oldPropertyNode); }
        $propertyNode = $this->_layout->createElement($propertyName);
        $propertyNode->appendChild($this->_layout->createTextNode($propertyValue));
        $moduleNode->appendChild($propertyNode);
    }

    /**
     * Adds a colgroup element to the layout
     *
     * @param $columnWidths string  a colon-separated list of widths (e.g. 2:1)
     */
    public function addColumnGroup($columnWidths, $parentPath = '/layout/colgroup/column') {
        if (mb_strpos($columnWidths, ':') !== FALSE) {
            $widths = mb_split(':', $columnWidths);
        }
        else {
            $widths = array(intval($columnWidths));
        }
        $xpath = new DOMXPath($this->_layout);
        $results = $xpath->query($parentPath);
        $parent = $results->item(0);
        $colgroup = $this->_layout->createElement('colgroup');
        if ($parent->hasChildNodes()) {
            $colgroup = $parent->insertBefore($colgroup,
                    $parent->firstChild);
        }
        else {
            $colgroup = $parent->appendChild($colgroup);
        }
        foreach ($widths as $width) {
            $column = $colgroup->appendChild($this->_layout->createElement('column'));
            $column->setAttribute('width', $width);
        }
    }


    /**
     *  Takes an xpath query and removes the first element found by that
     *    query
     *
     * @param $path string
     */
    public function removeElement($path) {
        $xpath = new DOMXPath($this->_layout);
        $results = $xpath->query($path);
        $doomed = $results->item(0);
        if (isset($doomed)) {
            $doomed->parentNode->removeChild($doomed);
        }
    }


    /**
     *  Takes an xpath query and removes all children of the first element
     *    found by that query
     *
     * @param $path string
     */
    public function removeChildren($path) {
        $xpath = new DOMXPath($this->_layout);
        $results = $xpath->query($path);
        $parent = $results->item(0);
        if (isset($parent)) {
            while ($parent->hasChildNodes()) {
                $parent->removeChild($parent->firstChild);
            }
        }
    }

    /**
     *  Takes an xpath query and removes all children of the first element
     *    found by that query, replacing them with $newChildren.
     *
     * @param   string  $path           Path for xpath query.
     * @param   array   $newChildren    Array of nodes to replace children with.
     */
    public function replaceChildren($path, $newChildren) {
        $xpath = new DOMXPath($this->_layout);
        $this->removeChildren($path);
        $parent = $xpath->query($path)->item(0);
        foreach ($newChildren as $newChild) {
            $parent->appendChild($newChild);
        }
    }

    /**
     * Takes an array of "<widgetName>-<action>" strings, finds the DOM nodes in the page layout
     * matching those attributes and returns them as an array of "<widgetName>-<action>" => DOMNodeList.
     * Value will be null if no node with that widgetName and action combination is found in the layout.
     *
     * @param   array   Array of "<widgetName>-<action>" strings
     * @return  array   Array of "<widgetName>-<action>" => DOMNodeList (call ->length and ->item(N)).
     */
    public function getModulesByType($embeds) {
        $xpath = new DOMXPath($this->_layout);
        $nodes = array();
        foreach ($embeds as $rawEmbed) {
            list($widgetName, $action) = explode('-', $rawEmbed);
            $nodes[$rawEmbed] =  $xpath->query('//module[@widgetName="' . $widgetName . '"][@action="' . $action . '"]');
            if ($nodes[$rawEmbed]->length > 1) {
                //TODO: This should never happen but occasionally does, see BAZ-7402 [Thomas David Baker 2008-05-08]
                error_log("XPath query found " . $nodes[$rawEmbed]->length . " nodes where expecting 1 with $widgetName, $action, $rawEmbed");
            }
        }
        return $nodes;
    }

    /**
     * Perform the specified XPath query against the current page layout.  Return the resulting DOMNodeList.
     *
     * @param   $xpathQuery     string      XPath query to perform.
     * @return                  DOMNodeList Result of query.
     */
    public function query($xpathQuery) {
        $xpath = new DOMXPath($this->_layout);
        return $xpath->query($xpathQuery);
    }

    /**
     * Returns the DOM object for this layout
     *
     * @return DOMDocument  the DOM for this layout
     */
    public function getLayout() {
        return $this->_layout;
    }

    /**
     * Returns the name that, with the type, uniquely identifies a layout instance, e.g., 'index', or the content ID of a User object
     *
     * @return string  the name of this layout
     */
    public function getName() { return $this->_name; }

    /**
     * Returns the layout type. 'homepage' is the default and
     * is the front page layout. 'profiles' is the layout for a user profile page.
     *
     * @return string  the layout type
     */
    public function getType() { return $this->_type; }

    /**
     * Returns whether the specified user is considered an owner of this page.
     * If the "viewAsOther" option has been specified, returns false.
     *
     * @param $profile XN_Profile  the user (or null to specify the current user)
     * @return boolean  whether the user is considered to be an owner of the layout
     */
    public function isOwner($profile = null) {
        if (isset($this->_opts['viewAsOther']) && $this->_opts['viewAsOther']) {
            return false;
        }
        if (is_null($profile)) { $profile = XN_Profile::current(); }
        return $this->callback_isOwner($profile);
    }

    /**
     * Returns the username of the owner of this page
     *
     * @return string  the screen name of this layout's owner
     */
    public function getOwnerName() {
        return $this->callback_getOwnerName();
    }

    /**
     * Outputs the internal representation of this layout
     *
     * @return string  the layout's XML
     */
    public function dump() {
        echo "<!--\n";
        var_dump($this->_layout);
        echo $this->_layout->saveXML();
        echo "\n-->\n";
    }

    /**
     * Overwrites the existing layout with a new layout.
     *
     * @param   $layout DOMDocument Layout to load.
     * @return          void
     */
    public function loadLayout($layout) {
        $this->_layout = $layout;
    }

    /**
     * Overwrites the existing layout XML with the specified XML.
     *
     * @param   $xml    string  XML to load.
     * @return          void
     */
    public function loadXml($xml) {
        $this->_layout->loadXML($xml);
    }

    /**
     * Gets the value of the iteration attribute on the <layout/> element.
     * To be used to determine if a given layout is fresh or stale.
     *
     * @param	$layout	string	XML to get iteration value from.
     * @return 	string	Value of iteration attribute on root <layout/> element.
     */
    public static function getIteration($layout) {
        $xpath = $xpath = new DOMXPath($layout);
        $rootElement = $xpath->query('/layout')->item(0);
        return ($rootElement->hasAttribute('iteration') ? $rootElement->getAttribute('iteration') : 0);
    }

    /**
     * Sets the value of the iteration attribute on the <layout/> element of the specified layout.
     *
     * @param	$layout	string	XML to set iteration value in.
     * @return 	string	Value of iteration attribute to set on root <layout/> element.
     */
    public static function setIteration($layout, $iteration) {
        $xpath = $xpath = new DOMXPath($layout);
        $rootElement = $xpath->query('/layout')->item(0);
        $rootElement->setAttribute('iteration', $iteration);
    }

    /**
     *  Remove all but the permanent elements from the layout
     */
    public function clear() {
        $newLayout = $this->baseXml();
        $newLayout = self::removeWhitespaceBetweenTags($newLayout);
        $this->_layout->loadXML(trim($newLayout));
    }

} // XG_Layout

/**
 * A homepage-type layout
 */
class XG_Layout_homepage extends XG_Layout {

    // Callbacks
    //
    // Each type of layout must implement four callbacks:
    // - load: this returns a layout if one is available or returns null
    // - setup: this populates the layout with appropriate defaults
    // - save: this persists the layout however appropriate
    // - getOwnerName: this returns the username of the person who owns this page
    // - isOwner: whether the specified user is considered an owner of this page.
    //
    // Callback names are callback_method, e.g. callback_load or callback_save

    // TODO: Remove the "callback_" prefix and simply make the functions abstract [Jon Aquino 2008-02-29]

    /**
     * Returns the XML for the layout if one is available; otherwise returns null
     *
     * @param $name string  the name that identifies the layout instance, e.g., 'index'
     * @return string  the layout XML, or null if the specified layout does not exist
     */
    protected function callback_load($name) {
        /*
         * Protect against multiple layout objects found (BAZ-39).  Not sure
         *   why a second object is created - it would be nice to find out.
         */
         $query = $name == 'index' ? XG_Query::create('content')->addCaching(self::MAIN_PAGE_LAYOUT_CHANGED) : XN_Query::create('content');
         $query
                ->filter('type', '=', 'PageLayout')
                ->filter('title', '=', $name)
                ->filter('owner')
                ->order('createdDate', 'desc')
                ->end(1);
        $this->_layoutObject = $query->uniqueResult();
        if ($this->_layoutObject) {
            return $this->_layoutObject->my->layout;
        } else {
            /* Check for a layout under the old name (PBLayout) */
            $query = XN_Query::create('content')
                    ->filter('type', '=', 'PBLayout')
                    ->filter('title', '=', $name)
                    ->filter('owner')
                    ->order('createdDate', 'desc')
                    ->end(1);
            //  Don't set $this->_layoutObject so a new object will be created
            //    (with the proper name)
            $layoutObject = $query->uniqueResult();
            if ($layoutObject) {
                return $layoutObject->my->layout;
            } else {
                return null;
            }
        }
    }

    /**
     * Populates the layout with appropriate defaults
     *
     * @param $name string  the name that identifies the layout instance, e.g., 'index'
     */
    protected function callback_setup($name) {
        $this->clear();

        // Left column
        $this->addModule('profiles', 'embed1activeMembers', '/layout/colgroup/column/colgroup/column');
        $this->addModule('main', 'embed1siteDescription', '/layout/colgroup/column/colgroup/column');

        // Center column
        XG_App::includeFileOnce('/lib/XG_LayoutHelper.php');
        $this->setEmbedInstanceProperty('visible', 1, XG_LayoutHelper::addWelcomeBoxIfNecessary($this));
        $this->addModule('activity', 'embed2', '/layout/colgroup/column/colgroup/column[2]');

        // Site-wide sidebar
        $this->addModule('main', 'embed1createdBy', '/layout/colgroup/column[2]', array('sitewide' => true, 'fixed' => true));
        $this->addModule('main', 'embed1ads', '/layout/colgroup/column[2]', array('sitewide' => true, 'fixed' => true));
        $this->addModule('main', 'embed1you', '/layout/colgroup/column[2]', array('sitewide' => true, 'fixed' => true));

    }

    /**
     * Updates and saves the layout (e.g. to a content object, or to a file)
     *
     * @param $currentLayout  string  the layout XML
     */
    protected function callback_save($currentLayout) {
        //TODO We should perhaps do some last ditch iteration checking
        // of the iteration attribute of the <layout/> element
        // to make sure we are not saving a modified old layout over a new
        // layout.  However, this fires /after/ the response is sent to
        // the client so instead we've tried to handle this at a slightly
        // higher level so that the client doesn't get mixed messages.
        if (!$this->_layoutObject) {
            $this->_layoutObject = XN_Content::create('PageLayout', $this->_name);
        }
        $this->_layoutObject->my->layout = $currentLayout;
        $this->_layoutObject->save();
        XG_Query::invalidateCache(self::MAIN_PAGE_LAYOUT_CHANGED);
    }

    /**
     * Returns the username of the person who owns this page
     *
     * @return string  the screen name of the layout owner
     */
    protected function callback_getOwnerName() {
        return XN_Application::load()->ownerName;
    }

    /**
     * Returns whether the specified user is considered an owner of this page.
     *
     * @param $profile XN_Profile  the user
     * @return boolean  whether the user is considered to be an owner of the layout
     */
    public function callback_isOwner($profile) {
        return XG_SecurityHelper::userIsAdmin($profile);
    }

    /** Additional type-specific functionality */

    /**
     * Recreate the homepage layout.  This routine (amongst other things) sets the iteration
     * attribute of the <layout/> element to be "1".  So it should never be called when there
     * is any layout history to preserve.  This is called if the user launches without a layout
     * whereas callback_setup provides the default layout (seen during setup) and they are different [PM]
     *
     */
    public function reInitialize($mainFeature = 'photo') {
        $app = XN_Application::load();
        $appName = $app->name;
        $introTitle = xg_text('WELCOME');
        if ($appName) {
            $introTitle = xg_text('WELCOME_TO_X', $appName);
        }
        $ownerName = htmlspecialchars($app->ownerName);
        $embedInstanceId = 0;
        $newLayout = '
<layout iteration="1" nextEmbedInstanceId="100">
    <colgroup locked="1">
        <column width="3">
            <colgroup>
                <column width="1">
                    <module widgetName="main" action="embed1siteDescription" embedInstanceId="' . ++$embedInstanceId . '"/>
                    <module widgetName="profiles" action="embed1activeMembers" embedInstanceId="' . ++$embedInstanceId . '"/>
                    <module widgetName="video" action="embed1" embedInstanceId="' . ++$embedInstanceId . '"/>
                </column>
                <column width="2">
                    <module widgetName="main" action="embed2welcome" embedInstanceId="' . ++$embedInstanceId . '"><visible>1</visible></module>
                    <module widgetName="activity" action="embed2" embedInstanceId="' . ++$embedInstanceId . '"/>
                    <module widgetName="photo" action="embed2" embedInstanceId="' . ++$embedInstanceId . '"/>
                    <module widgetName="forum" action="embed2" embedInstanceId="' . ++$embedInstanceId . '"/>
                </column>
            </colgroup>
        </column>
        <column width="1" locked="1">
            <module widgetName="main" action="embed1you" fixed="1" sitewide="1" embedInstanceId="' . ++$embedInstanceId . '"/>
            <module widgetName="main" action="embed1ads" fixed="1" sitewide="1" embedInstanceId="' . ++$embedInstanceId . '"/>
            <module widgetName="main" action="embed1createdBy" fixed="1" sitewide="1" embedInstanceId="' . ++$embedInstanceId . '"/>
        </column>
    </colgroup>
</layout>';
        $newLayout = self::removeWhitespaceBetweenTags($newLayout);
        $this->_layout->loadXML(trim($newLayout));
    }

    /**
     * Marks the homepage layout for initialization when the app is launched.
     *
     * @param $reInitializeOnLaunch boolean  whether reInitialize should be called on launch
     */
    public function setReInitializeOnLaunch($reInitializeOnLaunch) {
        $this->_layoutObject->my->reInitializeOnLaunch = $reInitializeOnLaunch ? '1' : NULL;
        $this->_layoutObject->save();
        XG_Query::invalidateCache(self::MAIN_PAGE_LAYOUT_CHANGED);
    }

    /**
     * Returns whether to reinitialize the homepage layout when the app is launched.
     *
     * @return boolean  whether reInitialize should be called on launch
     */
    public function willReInitializeOnLaunch() {
        return !$this->_layoutObject || $this->_layoutObject->my->reInitializeOnLaunch;
    }

    /**
     * Set the default homepage layout and save it.  Called in XG_App if the app is launched with no custom layout
     *
     */
    public function reInitializeAndSave() {
        self::reInitialize();
        $saveCallback = $this->buildCallback('homepage','save');
        call_user_func($saveCallback, 'index');
    }

    /**
     * The basic XML for this type of layout with all the optional/movable modules removed.
     * Called by the "clear" function when reconstructing a layout.
     */
    public function baseXml() {
        return '<layout iteration="1" nextEmbedInstanceId="100">
    <colgroup locked="1">
        <column width="3">
            <colgroup>
                <column width="1">
                </column>
                <column width="2">
                </column>
            </colgroup>
        </column>
        <column width="1" locked="1">
        </column>
    </colgroup>
</layout>';
    }

    /**
     * Returns the maximum <embed> width for each column, ordered depth-first.
     *
     * @override
     */
    protected function getMaxEmbedWidthsForColumns() {
        return array(730, 220, 492, 173);
    }

} // XG_Layout_homepage

