<?php xg_header('manage', xg_html('TAB_MANAGER'), null, array('loadJQueryUi' => true)); ?>
<div id="xg_body">
	<%= xg_headline(xg_text('TAB_MANAGER'))%>
	<?php $this->renderPartial('fragment_success', 'admin'); ?>
	<?php if ($this->reset) { ?>
	<dl class="success msg">
		<dt><%= xg_html('SUCCESS_EXCLAMATION') %></dt>
		<dd><p><%= xg_html('YOUR_NEW_TAB_LAYOUT_HAS_BEEN_SAVED_RESET') %></p></dd>
	</dl>
	<?php } ?>
	<div class="xg_column xg_span-20">
        <div class="xg_column xg_span-6">
            <div class="xg_module" style="z-index:1; position:relative">
				<div class="xg_module_body"><?php $this->renderPartial('fragment_left')?></div>
            </div>
        </div>
        <div class="xg_column xg_span-14 xg_last">
            <div class="xg_module" style="z-index:0; position:relative">
				<div class="xg_module_body"><?php $this->renderPartial('fragment_right')?></div>
            </div>
        </div>
    </div>
</div>
<?php xg_footer(); ?>
