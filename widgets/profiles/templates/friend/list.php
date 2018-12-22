<?php xg_header($this->tab, $this->pageTitle); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child xj_list_container">
			<?php $this->_widget->dispatch('friend', 'listColumn', array($this->profile));?>
        </div>
        <div class="xg_1col">
            <div class="xg_1col first-child"><?php xg_sidebar($this); ?></div>
        </div>
    </div>
</div>
<?php xg_footer(); ?>
