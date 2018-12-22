<?php
xg_header(W_Cache::current('W_Widget')->dir, xg_text('GROUPS'), null, array('metaDescription' => $this->group->description)); ?>
<div id="xg_body">
<?php if(Group::userIsInvited($this->group)) {
    $this->renderPartial('fragment_invite', array('group' => $this->group,'invitation'=>$this->groupInvitation,'private'=> true));
} else {
    $this->renderPartial('fragment_private', array('group' => $this->group,
                'canCreate'=>Groups_SecurityHelper::currentUserCanSeeCreateGroupLinks(),
                'userHasRequested' => $this->userHasRequested));
}?>
    <?php
    ob_start();
    $this->_widget->dispatch('embed', 'embed3pagetitle');
    $this->_widget->dispatch('embed', 'embed2description');
    $aboutBoxHtml = trim(ob_get_contents());
    ob_end_clean();
    if ($aboutBoxHtml) { ?>
        <div class="xg_column xg_span-16 first-child">
            <%= $aboutBoxHtml %>
        </div>
    <?php
    } ?>
    <div class="xg_column xg_span-4 last-child">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>