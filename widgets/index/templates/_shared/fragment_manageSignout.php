<?php
// provides a signout link on the manage pages for admins
if (!XG_App::appIsLaunched()) {
    return;
}
XG_App::ningLoaderRequire('xg.shared.PostLink');
?>
<p class="navigation-solo right">
<%= xg_html('HELLO_USERNAME_SIGN_OUT', xnhtmlentities(xg_username($this->_user)), 'href="#" dojoType="PostLink" _url="' . xnhtmlentities(XG_AuthorizationHelper::signOutUrl()) . '"') %></p>
