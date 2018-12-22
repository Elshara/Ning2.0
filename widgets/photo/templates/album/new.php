<?php xg_header(W_Cache::current('W_Widget')->dir, $title = xg_text('ADD_AN_ALBUM'), null, array('forceDojo' => true)); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<?php XG_PageHelper::subMenu(Photo_HtmlHelper::subMenu($this->_widget,'none')) ?>
			<%= xg_headline($title)%>
			<?php $this->renderPartial('fragment_editForm', 'album'); ?>
        </div>
        <div class="xg_1col last-child">
            <?php xg_sidebar($this); ?>
        </div>
    </div>
</div>
<?php xg_footer(); ?>
