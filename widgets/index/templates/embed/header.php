<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"<%= XG_LanguageHelper::isCustomLocale(XG_LOCALE) ? '' : ' xml:lang="' . mb_substr(XG_LOCALE, 0, 2) . '"' %>>
<?php /* NF_Controller::showBoundaryComments(); */ ?>
<head>
<?php
$appName = XN_Application::load()->name;
if ($this->isMainPage && $this->tagline) {
    $title = xnhtmlentities($appName) . " - " . xnhtmlentities($this->tagline);
} else if ($this->isMainPage) {
    $title = xnhtmlentities($appName);
} else if ($appName != $this->title) {
    $title = xnhtmlentities($this->title) . " - " . xnhtmlentities($appName);
} else {
    $title = xnhtmlentities($this->title);
}
?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><%= $title %></title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon" />
    <link rel="SHORTCUT ICON" href="/favicon.ico" type="image/x-icon" />
<?php
if ($this->metaDescription !== false) { ?>
    <meta name="description" content="<%= $this->metaDescription /*already quoted*/ %>" />
<?php
}
if ($this->metaKeywords !== false) { ?>
    <meta name="keywords" content="<%= $this->metaKeywords /*already quoted*/ %>" />
<?php
}
if ($this->showFacebookMeta) { ?>
    <meta name="title" content="<%= $title %>" />
    <?php
    if (! is_null($this->facebookPreviewImage)) { ?>
        <link rel="image_src" href="<%= xnhtmlentities($this->facebookPreviewImage) %>" />
    <?php }
}
/* Anything added to the default replacement section should go before
<ning:head/>; otherwise IE will miss the feed-autodiscovery elements
[Jon Aquino 2007-03-12] */
echo XG_App::sectionMarker();

if (!$this->noHead) {
    $version = XG_Version::currentCodeVersion()
		. '_' . $this->_widget->config['languageVersion'] /* Version, for cache-busting [Jon Aquino 2007-02-25] */
		. ($_GET['bust_ning_loader_cache'] ? '_' . mt_rand() : '');
	$code = "<script>\n";
	$code .= "ning.loader.version = '$version';\n";
	$code .= "ning.loader.setPrefixPattern(/xg\.([^.]+)/, \"/xn_resources/widgets/\$1/js\");\n";
	$code .= "djConfig.parseWidgets = false;\n";
	// Next two if()'s are kept only for the XG_HtmlLayoutHelper::layout = old. We can remove them after the new layout is firmly established.
	/* BAZ-4641: adjust mini-icon and full name to network-specific values */
	if ($this->fullNameForNingbar) {
        // Encode and decode to prevent XSS attack. We use the ugly urlencode hack because
        // of the inline JavaScript - we should try to eliminate the inline JavaScript sometime [Jon Aquino 2007-12-12]
        // Use rawurlencode instead of urlencode, which converts space to + [Jon Aquino 2007-12-19]
		$code .= "ning.CurrentProfile.fullName = ".json_encode($this->fullNameForNingbar).";\n";
	}
	if ($this->thumbnailUrlForNingbar) {
		$code .= "ning.CurrentProfile.photoUrl = ".json_encode($this->thumbnailUrlForNingbar).";\n";
	}
	$code .= "</script>\n";
	XG_HtmlLayoutHelper::setPostJsLoadCode($code);
    XG_HtmlLayoutHelper::ningHead(array('forceDojo' => $this->forceDojo, 'loadJQueryUi' => $this->loadJQueryUi));
}

?>
    <link rel="stylesheet" type="text/css" media="screen,projection" href="<%= XG_Version::addXnVersionParameter($this->_widget->buildResourceUrl('css/common.css')) %>" />
<?php
$cssMedia = 'screen,projection';
if (is_array($this->moduleCssFiles)) {
    foreach ($this->moduleCssFiles as $path) {
        echo '<link rel="stylesheet" type="text/css" media="'.$cssMedia.'" href="' . XG_Version::addXnVersionParameter($path) . "\" />\n";
    }
}
// This is the default CSS replacement section after <ning:head/> (BAZ-8228) [ywh 2008-06-25]
echo XG_App::cssSectionMarker(); ?>
<link rel="stylesheet" type="text/css" media="<%=$cssMedia%>" href="<%= xnhtmlentities(XG_Version::addXnVersionParameter($this->typographyCssUrl)) %>">
<?php
if (! $this->profileThemeCssUrl) { ?>
    <link rel="stylesheet" type="text/css" media="<%=$cssMedia%>" href="<?php echo XG_Version::addXnVersionParameter($this->userCssFilename) ?>" />
<?php
}
if (! $this->profileThemeCssUrl && $this->includeCustomCss) { ?>
    <link rel="stylesheet" type="text/css" media="<%=$cssMedia%>" href="<%= XG_Version::addXnVersionParameter($this->customCssUrl) %>" />
<?php
}
$darkNingbarTextUserAttribute = XG_App::widgetAttributeName(W_Cache::getWidget('profiles'), 'darkNingbarText');
if (!$this->userObject && $this->_widget->config['darkNingbarText']) { ?>
    <link rel="stylesheet" type="text/css" media="<%=$cssMedia%>" href="<%= XG_Version::addXnVersionParameter('/xn_resources/widgets/index/css/ningbar-invert.css') %>" />
<?php
} elseif ($this->userObject && ! mb_strlen($this->userObject->my->$darkNingbarTextUserAttribute) && $this->_widget->config['darkNingbarText']) { ?>
    <link rel="stylesheet" type="text/css" media="<%=$cssMedia%>" href="<%= XG_Version::addXnVersionParameter('/xn_resources/widgets/index/css/ningbar-invert.css') %>" />
<?php
} elseif ($this->userObject && $this->userObject->my->$darkNingbarTextUserAttribute) { ?>
    <link rel="stylesheet" type="text/css" media="<%=$cssMedia%>" href="<%= XG_Version::addXnVersionParameter('/xn_resources/widgets/index/css/ningbar-invert.css') %>" />
<?php
}
if ($this->profileThemeCssUrl) { ?>
    <link rel="stylesheet" type="text/css" media="<%=$cssMedia%>" href="<%= xnhtmlentities($this->profileThemeCssUrl) %>" />
<?php
}
if ($this->profileCustomCssUrl) { ?>
    <link rel="stylesheet" type="text/css" media="<%=$cssMedia%>" href="<%= xnhtmlentities($this->profileCustomCssUrl) %>" />
<?php
}
if (! $this->hideAdColors && ! XG_App::runOwnAds()) { Index_AppearanceHelper::outputAdInitScript($this->userObject); } ?>
</head>

<body>
<?php /* BAZ-1633: This JS is in the header so it can run early and affect the Ningbar */ ?>
    <script>
        window.xg = {};
        xg.addOnRequire = function(f) { xg.addOnRequire.functions.push(f); };
        xg.addOnRequire.functions = [];
    </script>
<?php if ($this->hideNingLogo) { ?>
    <style>
        #xn_brand { display:none !important;}
    </style>
<?php }

if (!$this->hideNingbar) {
	XG_HtmlLayoutHelper::ningBar();
} else { ?>
    <div id="xn_bar">&#160;</div>
<?php }
$reqRoute = XG_App::getRequestedRoute();
?>
<div id="xg" class="<%= $this->xgDivClass %> xg_widget_<%=$reqRoute['widgetName']%> xg_widget_<%=$reqRoute['widgetName']%>_<%=$reqRoute['controllerName']%> xg_widget_<%=$reqRoute['widgetName']%>_<%=$reqRoute['controllerName']%>_<%=$reqRoute['actionName']%>">
    <div id="xg_head">
        <?php
        if ($this->displayLaunchBar) {
            $this->renderPartial('_launchbar', 'embed');
        } else if ($this->displayBlankLaunchBar) {
            echo "<div id='xg_setup'></div>\n";
        }
        if ($this->displayHeader) { ?>
            <div id="xg_masthead">
                <p id="xg_sitename"><a id="application_name_header_link" href="/"><?php if ($this->logoImage) { echo '<img src="' . $this->logoImage . '" alt="' . $this->app->name . '">'; } else { echo $this->app->name; } ?></a></p>
                <?php if ($this->tagline) { ?><p id="xg_sitedesc"><%= $this->tagline %></p><?php } ?>
            </div>
            <div id="xg_navigation" <?php if ($this->hideNavigation) echo 'class="hidden"' ?>>
                <ul>
                    <?php
                    if($this->navEntries['method'] == XG_ModuleHelper::TABS_TAB_MANAGER){
                        $this->renderPartial('fragment_tabsTabManager', 'embed', array('tabs' => $this->navEntries['tabs'], 'subTabColors' => $this->navEntries['subTabColors']));
                    } else {
                        $this->renderPartial('fragment_tabsWidgets', 'embed', array('tabs' => $this->navEntries['tabs']));
                    } ?>
                </ul>
            </div>
        <?php
        } // displayHeader
        ?>
    </div>
