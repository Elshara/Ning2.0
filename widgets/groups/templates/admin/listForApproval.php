<?php xg_header(W_Cache::current('W_Widget')->dir, $title = xg_text('APPROVE_GROUPS')); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<?php XG_PageHelper::subMenu(Groups_HtmlHelper::subMenu($this->_widget)) ?>
			<%= xg_headline($title, array('count' => $this->totalCount))%>
            <div class="xg_colgroup">
                <div class="xg_3col first-child" id="column">
                    <?php
                    $this->renderPartial('fragment_listForApproval', 'admin', array(
                            'groups' => $this->groups, 'changeUrl' => $this->_buildUrl('admin', 'listForApproval'), 'curPage' => $this->page, 'numPages' => $this->numPages)); ?>
                </div>
            </div>
        </div>
        <div class="xg_1col last-child">
            <?php xg_sidebar($this); ?>
        </div>
    </div>
</div>
<?php xg_footer(); ?>