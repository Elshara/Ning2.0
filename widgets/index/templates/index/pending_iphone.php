<?php XG_IPhoneHelper::header(null, xg_text('MEMBERSHIP_PENDING_APPROVAL'), NULL, array('contentClass' => 'simple','largeIcon' => true, 'hideNavigation' => true)); ?>
<div class="msg notification">
	<h2><%= xg_html('PENDING_APPROVAL') %></h2>
	<p><%= xg_html('YOUR_MEMBERSHIP_TO_X_IS_PENDING_APPROVAL', XN_Application::load()->name) %></p>
</div>
<p class="promo"><a href="<%= xnhtmlentities(XG_AuthorizationHelper::signOutUrl(XG_HttpHelper::currentUrl())) %>"><%= xg_html('SIGN_OUT') %></a></p>
<?php xg_footer(NULL,array('contentClass' => 'simple','displayFooter' => false)); ?>
