<?php
/**
 * Fragment to render instead of an embed if an app has been removed from the application directory by it's creator or banned.
 *
 * @param   $appUrl string  URL of application.
 */
?>
<div class="xg_module module_opensocial">
    <div class="xg_module_head">
        <h2><%= xg_html('APPLICATION_REMOVED') %></h2>
    </div>
    <div class="xg_module_body">
        <p><%= xg_html('NO_LONGER_IN_APP_DIR') %></p>
	</div>
	<div class="xg_module_foot">
        <p><?php $this->renderPartial('fragment_removeAppLink', '_shared', array('appUrl' => $appUrl)); ?></p>
	</div>
</div>
