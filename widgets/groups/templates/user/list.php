<?php xg_header('groups', $this->listColumnProperArgs['pageTitle']); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
            <?php W_Cache::getWidget('profiles')->dispatch('friend', 'listColumnProper', array($this->listColumnProperArgs)); ?>
        </div>
        <div class="xg_1col">
            <div class="xg_1col first-child"><?php xg_sidebar($this); ?></div>
        </div>
    </div>
</div>
<?php xg_footer(); ?>
