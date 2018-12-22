<?php
/**
 * Partial that generates a link to remove an OpenSocial Application.
 * No logic is included to determine if the app is installed or not.  Call an OpenSocial_LinkHelper function to determine viability before calling this fragment.
 *
 * @param   $appUrl string  URL of app to create remove link for.
 */
XG_App::ningLoaderRequire('xg.opensocial.application.remove');
?>
<a id="xg_opensocial_remove_application" class="delete desc" href="#" appUrl="<%= xnhtmlentities($appUrl) %>"><%= xg_html('REMOVE_APPLICATION') %></a>
<form id="xg_opensocial_remove_form" style="display:none" method="post" action="<%= xnhtmlentities($this->_buildUrl('application', 'remove')) %>">
    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
    <input type="hidden" name="appUrl" value="<%= xnhtmlentities($appUrl) %>" />
</form>
