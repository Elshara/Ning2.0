<?php XG_IPhoneHelper::header('main', xg_text('OUR_APOLOGIES'), NULL, array('contentClass' => 'simple','largeIcon' => true, 'hideNavigation' => true)); ?><br/>
<div class="msg error">
	<h2><%= xg_html('OUR_APOLOGIES') %></h2>
	<p><%= xg_html('WE_ARE_SORRY_WE_ARE_HAVING') %></p>
	<p><%= xg_html('FOLLOW_LINK_TO_HOMEPAGE', 'href="'. xg_absolute_url('/') .'"') %></p>
</div>
<?php xg_footer(NULL,array('contentClass' => 'simple','displayFooter' => false)); ?>
