<?php
/*  $Id: $
 *
 *  Parameters:
 *		$message
 *		$error
 */
?>
<?php xg_header(null, xg_text('CHANGE_EMAIL_SETTINGS'), null, array('hideNavigation' => true)); ?>
<div id="xg_body">
	<div class="xg_column xg_span-8 xg_prepend-6">
		<%= xg_headline(xg_text('CHANGE_EMAIL_SETTINGS'))%>
		<div class="xg_module">
			<?php if ($this->error) {?>
			<div class="xg_module_body pad errordesc">
				<p class="last-child">
					<%=$this->error%>
				</p>
			</div>
			<?php } else if ($this->message) {?>
			<div class="xg_module_body pad success">
				<p class="last-child">
					<%=$this->message%>
				</p>
    		</div>
			<?php } ?>
		</div>
	</div>
</div>
<?php xg_footer(); ?>
