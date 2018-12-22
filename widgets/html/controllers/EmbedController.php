<?php

XG_App::includeFileOnce('/lib/XG_Embed.php');

class Html_EmbedController extends XG_GroupEnabledController {
    public function action_embed1($args) { $this->renderEmbed($args['embed'], $args['maxEmbedWidth']); }
    public function action_embed2($args) { $this->renderEmbed($args['embed'], $args['maxEmbedWidth']); }
    public function action_embed3($args) { $this->renderEmbed($args['embed'], $args['maxEmbedWidth']); }
    private function renderEmbed($embed, $maxEmbedWidth) {
        W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        $this->userCanEdit = $embed->isOwnedByCurrentUser() || Groups_SecurityHelper::currentUserCanEditGroupEmbed($embed);
        $this->fixTitle($embed);
        $this->embed = $embed;
        $this->maxEmbedWidth = $maxEmbedWidth;
        $this->title = $this->embed->get('title');
        $this->html = $this->embed->get('html');
        $this->html_isempty = ($embed->get('html') == null);
        if (! $this->html && ! $this->userCanEdit) {
            $this->render('blank');
            return;
        }
        list($this->title, $this->displayHtml, $this->sourceHtml, $this->hasDefaultContent) = $this->getValues($embed, $maxEmbedWidth);
        $this->nameOfGroupOrNetwork = $this->nameOfGroupOrNetwork();
        if ($this->userCanEdit) {
            $this->maxLength = self::getMaxLength($this->_widget);
        }
        $this->render('embed');
    }

    /**
     * Returns the updated module body using JSON - only called for Frink drop updates
     * Set xn_out to 'json'
     *
     * Expected GET parameters:
     *     id - The embed instance id
     *
     * Expected POST parameters:
     *     maxEmbedWidth - The maximum width in pixels of the embed's container
     *
     * @return string   JSON data
     */
    public function action_updateEmbed() {
        $embed = XG_Embed::load($_GET['id']);
        W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        if (! ($embed->isOwnedByCurrentUser() || Groups_SecurityHelper::currentUserCanEditGroupEmbed($embed))) { throw new Exception('Not embed owner.'); }
        XG_HttpHelper::trimGetAndPostValues();
        list($this->title, $this->displayHtml, $this->sourceHtml, $this->hasDefaultContent) = $this->getValues($embed, $_POST['maxEmbedWidth']);
    }

    public function action_setValues() {
        $embed = XG_Embed::load($_GET['id']);
        W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
        $this->userCanEdit = $embed->isOwnedByCurrentUser() || Groups_SecurityHelper::currentUserCanEditGroupEmbed($embed);
        if (! $this->userCanEdit) { throw new Exception('Not embed owner.'); }
        XG_HttpHelper::trimGetAndPostValues();
        $embed->set('title', $_POST['title']);
        $xhtml = $_POST['html'];
        $this->sourceHtml = $xhtml;
        // Use strlen rather than mb_strlen (BAZ-5729) [Jon Aquino 2008-02-04]
        if (self::getMaxLength($this->_widget) > 0 && strlen($xhtml) > self::getMaxLength($this->_widget)) {
            $this->errorCode = 'TOO_LONG';
            return;
        }
        if(XG_SecurityHelper::userIsAdmin()){
            //@TODO create a centralized xg_light_scrub function
            // Commented for BAZ-4980. Will be removed soon.
            // $xhtml = str_replace("\n", '(newline)',$xhtml);
            $xhtml = tidy_repair_string($xhtml, array('output-xhtml' => true,
                                                     'numeric-entities' => true,
                                                     'wrap' => 0,
                                                     'drop-empty-paras' => false
                                                     ), 'utf8');
            $xhtml = preg_replace('/<(\!DOCTYPE[^>]*|html[^>]*|\/html|head|\/head|body|\/body|title|\/title)>\r?\n*/u','',$xhtml);

            /** Commented for BAZ-4980. Will be removed soon.
            $xhtml = str_replace("(newline)", "\n", $xhtml);
            /$xhtml = preg_replace("@</p>[ \t]*\n@u", "</p>", $xhtml);
            // Hack: The list-style:none elements appear when the scrubber encounters "(newline)" between <li> elements [Jon Aquino 2007-03-02]
            $xhtml = preg_replace('@<li style="list-style: none">\s*</li>@u', '', $xhtml);
            */
            // Change <div /> to <div></div> (VID-478)  [Jon Aquino 2006-09-06]
            $xhtml = preg_replace('@></(br|hr|img)>@u', ' />', preg_replace('@<([a-z]+) ([^>]+)/>@u', '<${1} ${2}></${1}>', $xhtml));
        } else {
            $xhtml = preg_replace('/\r?\n/u', '', xg_scrub($xhtml));
        }
        $xhtml = self::limitConsecutiveLineBreaks($xhtml, 10); // BAZ-2604 [Jon Aquino 2008-03-03]
        $embed->set('html', $xhtml);
        if ($embed->getType() == 'profiles') {
            W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_ActivityLogHelper.php');
            Profiles_ActivityLogHelper::createProfileUpdateItem(XN_Profile::current()->screenName); 
        }
        $this->embed = $embed;
        $this->nameOfGroupOrNetwork = $this->nameOfGroupOrNetwork();
        $maxEmbedWidth = XG_Embed::getValueFromPostGet('maxEmbedWidth');
        list($this->title, $this->displayHtml, $this->sourceHtml, $this->hasDefaultContent) = $this->getValues($embed, $maxEmbedWidth);
        ob_start();
        $this->renderPartial('fragment_moduleHead');
        $this->moduleHead = trim(ob_get_contents());
        ob_end_clean();
        if ($this->hasDefaultContent) {
            $this->displayFoot = '<ul><li class="left"><a href="#" class="desc add">' . xg_html('ADD_TEXT') . '</a> ';
            if ($maxEmbedWidth > 480) {
                $this->displayFoot .= xg_html('CLICK_EDIT_TO_ADD_TEXT', 'href="' . xnhtmlentities($this->_buildUrl('embed', 'widgets')) . '"') . '</li></ul>';
            } 
        }

        // invalidate admin sidebar if necessary
        if ($_GET['sidebar']) {
            XG_App::includeFileOnce('/lib/XG_LayoutHelper.php');
            XG_LayoutHelper::invalidateAdminSidebarCache();
        }
    }

    /**
     * Ensures that the maximum number of consecutive <br> tags is $n.
     *
     * @param $html string  the original HTML
     * @param $n integer  the maximum number of consecutive line breaks
     * @return string  the HTML with the consecutive <br> limit enforced
     */
    protected static function limitConsecutiveLineBreaks($html, $n) {
        $br = '(?:<br ?/?>\r?\n?)';
        return preg_replace('@(' . $br . '{' . $n . '})' . $br . '+@ui', '\1', $html);
    }

    /**
     * Returns the maximum multibyte string length allowed for the html.
     *
     * @param $widget W_Widget  the HTML widget
     * @return integer  the max length
     */
    protected static function getMaxLength($widget) {
        if (mb_strlen($widget->config['maxLength']) == 0) {
            $widget->config['maxLength'] = 500000;
            $widget->saveConfig();
        }
        return $widget->config['maxLength'];
    }

    /**
     * Returns the name of the current group or, if we are not in a group context,
     * the name of the network.
     *
     * @return string  the name of the current group or of the network
     */
    private function nameOfGroupOrNetwork() {
        return XG_GroupHelper::inGroupContext() ? XG_GroupHelper::currentGroup()->title : XN_Application::load()->name;
    }

    private function getValues($embed, $maxEmbedWidth) {
        $title = $embed->get('title');
        $html = $embed->get('html');
        $hasDefaultContent = false;
        $this->$maxEmbedWidth = $maxEmbedWidth;
        if (! $html) {
            $hasDefaultContent = true;
        }
        $html = self::cleanScripts($html);
        $html = str_replace('//<![CDATA[','',$html);
        $html = str_replace('//]]>','',$html);
        $resizedHtml = xg_resize_embeds($html, $maxEmbedWidth);
        return array($title, $resizedHtml, $html, $hasDefaultContent);
    }

    public function action_error() {
        $this->render('blank');
    }

    /**
     * Displays an information page describing how to get started with widget providers.
     */
    public function action_widgets() {
    }


    /**
    * Strips <br /> tags inserted inside script tags, since this breaks things. BAZ-2989
    * this is being called on getValues; consider calling during setValues for greater efficiency.
    */
    private function cleanScripts($html) {
        $scripts = explode('//<![CDATA[', $html);
        if (count($scripts) > 1) {
            // loop through
            for ($i = 1; $i <= count($scripts); $i++) {
                $scriptEnd = explode('//]]>',$scripts[$i]);
                if (count($scriptEnd) == 2) {
                    $scriptEnd[0] = str_replace('<br />','',$scriptEnd[0]);
                    $scripts[$i] = implode('//]]>',$scriptEnd);
                }
            }
            // reassemble
            return implode('//<![CDATA[', $scripts);
        } else {
            return $html;
        }
    }

    /**
     * Fixes BAZ-2253.
     *
     * @param XG_Embed  the embed to fix, if necessary
     */
    private function fixTitle($embed) {
        if ($embed->get('title') == xg_html('YOUR_X_BOX')) { $embed->set('title', ''); }
    }

}
