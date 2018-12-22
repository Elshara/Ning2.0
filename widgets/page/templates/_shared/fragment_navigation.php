<?php
/**
 * The Page-specific links at the top of the page.
 */
/*	<li><a href="<%=qh($this->_buildUrl('page','list'))%>"><%=xg_html('ALL_PAGES')%></a></li> */
?>
<?php if (Page_SecurityHelper::currentUserCanCreatePage() && !$noAddLink) { ?>
<ul class="navigation">
    <li class="left"><a href="<%= xnhtmlentities($this->_buildUrl('page', 'list')) %>"><%= xg_html('ALL_PAGES') %></a></li>
    <li class="left"><a href="<%= xnhtmlentities($this->_buildUrl('page', 'listForContributor', array('user' => XN_Profile::current()->screenName))) %>"><%= xg_html('MY_PAGES') %></a></li>
    <li class="right"><strong><a href="<%= xnhtmlentities($this->_buildUrl('page', 'new')) %>" class="bigdesc add"><%= xg_html('ADD_A_PAGE') %></a></strong></li>
</ul>
<?php } ?>
