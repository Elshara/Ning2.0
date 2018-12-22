<?php
/** $Id: $
 *
 *  Encapsulates the logic related to the default ning-related page elements:
 *     ning head
 *     ning bar
 *     dojo.js
 *
 **/
class XG_HtmlLayoutHelper {
    /**
     * If new layout is used, point to this static resources version.
     * It's ok to have the hardcoded value here.
     */
    protected static $staticVersion = '6.11.8.1';

    /**
     * (new layout) Set to true when ningBar() method it called.
     */
    protected static $ningBarDisplayed = false;

    /**
     * Code to be inserted after the JS library loads
     */
    protected static $postJSLoadCode;

    /**
     * Whether header was printed.
     */
    protected static $headPrinted = false;

    /**
     * Whether to include dojo.js
     */
    protected static $includeDojo = false;

    /**
     * Whether to load the JQuery UI plugin.
     */
    private static $loadJQueryUi = false;

    /**
     * Sets the code to be inserted after the JS library is loaded.
     * Must be called before any call to ning*() methods
     */
    public static function setPostJsLoadCode($postJSLoadCode) {
        self::$postJSLoadCode = $postJSLoadCode;
    }

    /**
     * Inserts the code for the <ning:head /> replacement: system css/js code.
     * Options are:
     * 		forceDojo		bool		Force dojo.js inclusion.
     *      loadJQueryUi    bool        Whether to load the JQuery UI plugin
     *
     * @param		$options		hash		Extra options
     */
    public static function ningHead(array $options = array()) {
        self::$headPrinted = true;
        if ($options['forceDojo'] || $_GET['forceDojo']) {
            self::$includeDojo = true;
        }
        self::$loadJQueryUi = $options['loadJQueryUi'];
        self::_printNingJsonBlock();
    }

    /**
     * Inserts the code for the <ning:ningbar /> replacement: ningbar html/js
     */
    public static function ningBar() {
        $p = XN_Profile::current();
        self::$ningBarDisplayed = true;
?>
<div id="xn_bar">
    <div id="xn_bar_menu">
        <div id="xn_bar_menu_branding">
            <p id="xn_brand"><a href="http://<%=XN_AtomHelper::HOST_APP('www')%>">Ning<%/*no i18n*/%></a></p>
        </div>

        <ul id='xn_bar_menu_tabs'>
            <?php
            if ($p->isLoggedIn()) {
                $user = User::loadOrRetrieveIfLoaded($p->screenName); ?>
                <li>
                    <span class="before"></span>
                    <a href="<%= xnhtmlentities(User::quickProfileUrl($p->screenName)) %>">
                        <img src='<%=XG_UserHelper::getThumbnailUrl($p, 12, 12)%>' id="xn_bar_miniavatar" class="mini-avatar" width="12" height="12" alt=""/>
                        <%=qh(XG_UserHelper::getFullName($user))%>
                    </a>
                    <span class="after"></span>
                </li>
            <?php
            }
			if (!XG_App::protectYourNetwork() && !$p->isOwner()) { ?>
                <li>
                    <span class="before"></span>
                    <a href="<%= xnhtmlentities(XG_HttpHelper::addParameter('http://' . XN_AtomHelper::HOST_APP('www') . '/', 'appUrl', XN_Application::load()->relativeUrl)) %>"><%= xg_html('GET_YOUR_OWN_BAZEL') %></a>
                    <span class="after"></span>
                </li>
            <?php
            } ?>
        </ul>
        <div id="xn_bar_menu_more">
            <form id="xn_bar_menu_search" method="GET" action="<%=qh(W_Cache::getWidget('main')->buildUrl('search','search'))%>">
                <fieldset>
                    <input type="text" name="q" id="xn_bar_menu_search_query" value="" accesskey="4" class="text" />
                    <a id="xn_bar_menu_search_submit" href="#" onclick="document.getElementById('xn_bar_menu_search').submit();return false"><%=xg_html('SEARCH')%></a><span class="after"></span>
                </fieldset>
            </form>
        </div>
    </div>
</div>
<?php
    }

    /**
     *  Returns TRUE if page loads the full-size dojo.js and FALSE otherwise
     *
     *  @return     bool
     */
    public static function hasDojo() {
        return self::$includeDojo;
    }

    /**
     * Inserts the code for the page footer. Usualy loads the JS library.
     */
    public static function ningFooter() {
        if (!self::$headPrinted) {
            return;
        }
        self::_printJsInitBlock();
        echo self::$postJSLoadCode;
    }

    protected static function _printNingJsonBlock() { # void
        if (! ($https = ($_SERVER['HTTPS'] == 'on')) ) {
            $staticHost = 'http://'.XN_AtomHelper::HOST_APP('static');
        }
        $app 		= XN_Application::load();
        $domains 	= $app->domains;
        $premium 	= $app->premiumServices;
        $currentApp	= array(
            "viewSourceUrl"	=> 'http://'.XN_AtomHelper::HOST_APP('www') . '/view-source.html?appUrl='.$app->relativeUrl,
            "premium"		=> (bool)count($premium),
            "iconUrl" 		=> $app->iconUrl(64,64),
            "url" 			=> "http://" . array_shift($domains),
            "domains" 		=> $domains,
            "online"		=> $app->publishedState == 'Y',
            "privateSource"	=> (bool)isset($premium[XN_Application::PREMIUM_PRIVATE_SOURCE]),
            "id"			=> $app->relativeUrl,
            "description"	=> qh($app->description),
            "name"			=> htmlentities($app->name), /** don't need ENT_QUOTES here - BAZ-8849 */
            "owner"			=> qh($app->ownerName),
            "createdDate"	=> $app->createdDate,
            "runOwnAds"		=> (bool)isset($premium[XN_Application::PREMIUM_RUN_OWN_ADS]),
        );
        if ($app->categories) {
            $currentApp["category"] = $app->categories;
        }
        if ($app->tags) {
            $currentApp["tags"] = $app->tags;
        }

        $profile		= XN_Profile::current();
        $currentProfile	= !$profile->isLoggedIn() ? NULL : array(
            //"relationship"	=> array("description" => "none"), // can be removed. not used.
            "id"			=> qh($profile->screenName),
            "presence"		=> $profile->presence,
            "profileUrl"	=> xg_absolute_url(User::quickProfileUrl($profile->screenName)),
            "location"		=> qh(XG_UserHelper::getLocation($profile)),
            "age"			=> XG_UserHelper::getAge($profile),
            "gender"		=> XG_UserHelper::getGender($profile),
            "fullName"		=> qh(xg_username($profile)),
            "photoUrl"		=> XG_UserHelper::getThumbnailUrl($profile),
            //"emailVerified"	=> false,
            "country"		=> qh(XG_UserHelper::getCountry($profile)),
            //"zipcode"		=>
            "description"	=> qh($profile->description),
        );
        $ningJSON 	= array(
            "CurrentApp" 		=> $currentApp,
            "CurrentProfile" 	=> $currentProfile,
        );
?>
    <script type="text/javascript">
<?php 	if ($https) {?>
        djConfig = { preventBackButtonFix: false, isDebug: false }
<?php 	} else {?>
        djConfig = { baseScriptUri: 'http://<%=$_SERVER['SERVER_NAME']%>/xn/static-<%=self::$staticVersion%>/js/dojo-0.3.1-ning/', isDebug: false }
<?php 	}?>
        ning = <%=json_encode($ningJSON)%>;
    </script>
<?php
    }

    protected static function _printJsInitBlock() { # void
        if (! ($https = ($_SERVER['HTTPS'] == 'on')) ) {
            $staticHost = 'http://'.XN_AtomHelper::HOST_APP('static');
        }
        if (self::$includeDojo) {
        	echo '<script type="text/javascript" src="'.xg_cdn('/xn_resources/widgets/lib/js/jquery/jquery.min.js').'"></script>',"\n";
			echo '<script type="text/javascript" src="'.xg_cdn('/xn_resources/widgets/lib/js/jquery/jquery.json.min.js').'"></script>',"\n";
    		echo '<script>window.x$ = jQuery.noConflict()</script>',"\n";
			$dojo = ($https ? '' : $staticHost) . '/xn/static-'.self::$staticVersion.'/js/dojo-0.3.1-ning/dojo.js';
            echo '<script type="text/javascript" src="'.$dojo.'"></script>';
			// Use it as a mark to show the dojo.js warning.
            // echo '<div style="z-index:1020;background-color:red;color:white;border:1px solid black;position:absolute;left:0;top:0;padding:5px;"><b>DOJO.JS</b></div>';
		} else {
            echo '<script type="text/javascript" src="'.xg_cdn('/xn_resources/widgets/lib/core.min.js').'"></script>',"\n";
		}
		if (self::$loadJQueryUi) {
			echo '<script type="text/javascript" src="'.xg_cdn('/xn_resources/widgets/lib/js/jquery/jquery-ui.min.js').'"></script>',"\n";
		}
?>
    <script type="text/javascript">
        if (!ning._) {ning._ = {}}
        ning._.compat = { encryptedToken: "<empty>" }
        ning._.CurrentServerTime = "<%=date('c')%>";
        ning._.probableScreenName = "";
        ning._.domains = {
            base: '<%=mb_substr(XN_AtomHelper::$DOMAIN_SUFFIX, 1) /* strip leading "." */ %>',
            ports: { http: '<%=XN_AtomHelper::$EXTERNAL_PORT%>', ssl: '<%=XN_AtomHelper::$EXTERNAL_SSL_PORT%>' }
        };
    </script>
<?php
    }
}
