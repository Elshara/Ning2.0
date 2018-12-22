<?php
/**
 * Display the "Add to My Page" link for an application.
 *
 * @param   $appUrl     string  URL of application to create add link for.
 * @param   $loaderId   string  Optional param for id of HTML element to add a spinner to when add to page is confirmed.
 * @param   $cssClass   string  Optional param for class of HTML element other than "add desc"
 */
if (XN_Profile::current()->isLoggedIn()) {
    XG_App::ningLoaderRequire('xg.opensocial.shared.AddToMyPageLink');
    $prefs = OpenSocial_GadgetHelper::readGadgetUrl($appUrl); ?>
    <a href="#" class="<%= $cssClass ? $cssClass : 'add desc' %>" dojoType="AddToMyPageLink" _appUrl="<%= xnhtmlentities($appUrl) %>" 
        _gadgetTitle="<%= xnhtmlentities($prefs['title']) %>" _loaderId="<%= $loaderId %>"
        _postUrl="<%= xnhtmlentities($this->_buildUrl('application', 'add')) %>" _ningApplication="<%= $prefs['ningApplication'] ? 1 : 0 %>"
        _tosUrl="<%= xnhtmlentities(W_Cache::getWidget('main')->buildUrl('authorization', 'applicationTos')) %>"><%= xg_html('ADD_TO_MY_PAGE') %></a>
<?php } else if ($showSignInToAdd !== "0") {
    XG_App::ningLoaderRequire('xg.opensocial.shared.AddToMyPageLink');
    $target = $this->_buildUrl('application', 'add', array('appUrl' => $appUrl));
    $signInLink = XG_AuthorizationHelper::signInUrl($target); ?>
    <a href="#" class="<%= $cssClass ? $cssClass : 'add desc' %>" dojoType="AddToMyPageLink" _appUrl="<%= xnhtmlentities($appUrl) %>" 
        _gadgetTitle="<%= xnhtmlentities($prefs['title']) %>" _loaderId="<%= $loaderId %>"
        _getUrl="<%= xnhtmlentities($signInLink) %>" _ningApplication="<%= $prefs['ningApplication'] ? 1 : 0 %>"
        _tosUrl="<%= xnhtmlentities(W_Cache::getWidget('main')->buildUrl('authorization', 'applicationTos')) %>"><%= xg_html('ADD_TO_MY_PAGE') %></a>
<?php }
