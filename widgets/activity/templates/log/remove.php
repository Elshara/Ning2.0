<?php
$isProfile      = $this->isProfile;
$no_activity    = (count($this->logItems) == 0);
$activity_off   = false;
?>
<?php xg_header(W_Cache::current('W_Widget')->dir, $title = xg_text('LATEST_ACTIVITY')); ?>
<div id="xg_body">
	<div class="xg_colgroup">
		<div class="xg_3col first-child">
			<%= xg_headline(xg_text('DELETE_ACTIVITY_ITEM'))%>
			<div class="xg_module">
				<form action="" method="POST">
					<div class="xg_module_body pad">
						<%= XG_SecurityHelper::csrfTokenHiddenInput() %>
						<div class="activity">
							<h2><%= xg_html('DELETE_THIS_ACTIVITY_MESSAGE_Q') %></h2>
							<p><%= xg_html('THIS_WILL_PREVENT_ACTIVITY') %></p>
							<?php foreach($this->logItems as $item){
								$this->renderPartial('fragment_logItem', 'log', array('item' => $item, 'isProfile' => false, 'removeOptionOff' => true));?>
							<?php } ?>
						</div>
					</div>
					<div class="xg_module_footer">
						<p>
							<input type="hidden" name="cancelUrl" value="<%= xnhtmlentities($this->cancelUrl) %>" />
							<input type="submit" class="button" value="<%= xg_html('OK') %>" />
							<a class="button" href="<%= xnhtmlentities($this->cancelUrl) %>"><%= xg_html('CANCEL') %></a>
						</p>
					</div>
				</form>
			</div>
		</div>
		<div class="xg_1col last-child">
			<?php xg_sidebar($this); ?>
		</div>
	</div>
</div>
<?php xg_footer(); ?>
