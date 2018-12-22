<?php XG_IPhoneHelper::header(null, xg_text('MEMBERSHIP_BY_INVITATION_ONLY'), null, array('contentClass' => 'simple','largeIcon' => true, 'hideNavigation' => true)); ?>
<div class="msg notification">
	<h2><%= xg_html('MEMBERSHIP_BY_INVITATION_ONLY') %></h2>
	<p><%= xg_html('MEMBERSHIP_TO_APPNAME_BY_INVITATION_ONLY', xnhtmlentities(XN_Application::load()->name)) %></p>
</div>
<?php xg_footer(NULL,array('contentClass' => 'simple','displayFooter' => false)); ?>
