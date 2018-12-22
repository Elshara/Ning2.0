<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <%= XG_LanguageHelper::isCustomLocale(XG_LOCALE) ? '' : ' xml:lang="' . mb_substr(XG_LOCALE, 0, 2) . '"' %>>
<head>
<?php
$appName = XN_Application::load()->name;
if ($appName != $this->title) {
    $title = xnhtmlentities($this->title) . " - " . xnhtmlentities($appName);
} else {
    $title = xnhtmlentities($this->title);
}
$uiu = xg_cdn($this->_widget->buildResourceUrl('js/iui.js'));
?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <title><%= $title %></title>
    <link rel="stylesheet" type="text/css" media="screen,projection" href="<%= XG_Version::addXnVersionParameter($this->_widget->buildResourceUrl('css/common_iphone.css')) %>" />
    <?php
	if ($this->themeCSSUrl) { ?>
	    <link rel="stylesheet" type="text/css" media="screen,projection" href="<%= XG_Version::addXnVersionParameter($this->themeCSSUrl) %>" />
	<?php
	} ?>
    <script type="application/x-javascript" src="<%= $uiu %>"></script>
    <?php
    if ($this->bookmarkIconUrl) { ?>
    	<link rel="apple-touch-icon" href="<%= xnhtmlentities($this->bookmarkIconUrl) %>"/>
    <?php
    } ?>
</head>
<body class="show_normal dark" onorientationchange="updateOrientation();">
	<div id="w">
	<?php
	if ($this->contentClass) { ?>
		<div id="content" class="<%= $this->contentClass %>">
	<?php
	}
		if ($this->displayHeader !== false) { 
        	if ($this->largeIcon) { ?>
        	<div id="largeheader">
        		<span class="logo large" style="background-image:url('<%= XG_IPhoneHelper::largeIconUrl() %>');"></span><strong><%= $appName %></strong>
        	</div>
        	<?php
        	} else { ?>
        		<div id="header">
					<a id="network" href="<%= xg_absolute_url('/') %>" class="">
        			<span class="logo small" style="background-image:url(<%= XG_IPhoneHelper::smallIconUrl() %>);"></span>
        			<strong><%= $appName %></strong>
					</a>
			<?php if ($this->_user->isLoggedIn()) { ?>
					<a class="title-button" id="quick_add" href="#" onclick="javascript:void(0);"><%= xg_html('ADD') %></a>
			<?php
			} ?>
				</div><!--/#header-->
			<?php
			}
        }
        if ($this->hideNavigation === false) { ?>
        <div id="navigation">
            <ul class="lead">
            <?php
            foreach ($this->navEntries as $entry) {
                list($name, $link, $id, $subEntries) = $entry;
                $highlight = '';
                if (count($subEntries) > 0) {
	                foreach ($subEntries as $subEntry) {
	                    if ($this->navHighlight == $subEntry[2]) {
	                        $highlight = 'class="this"';
	                        $subNav = $subEntries;
	                    }
	                }
                } 
                else if ($this->navHighlight == $id) {
                	$highlight = 'class="this"';
                }
                ?>
                <li id="nav-<%= $id %>" <%= $highlight %>>
                    <a href="<%= $link %>" target="_self"><%= $name %></a>
                </li> <?php
            } ?>
            </ul> <?php

            if (count($subNav) > 0) { ?>
            <ul class="sub"> <?php
                foreach ($subNav as $subEntry) {
                    list($name, $link, $id, $subNav) = $subEntry;
                    $highlight = '';
                    if ($id == $this->navHighlight) {
                        $highlight = 'class="this"';
                    } ?>
                    <li id="nav-<%= $id %>" <%= $highlight %>>
                        <a href="<%= $link %>" target="_self"><%= $name %></a>
                    </li> <?php
                } ?>
            </ul> <?php
            } else { ?>
            <ul class="sub empty">
              <li></li>
            </ul>
            <?php } ?>
        </div>
        <?php } ?>