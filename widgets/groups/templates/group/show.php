<?php
xg_header(W_Cache::current('W_Widget')->dir, $this->group->title, null, array('metaDescription' => $this->group->description)); ?>
<div id="xg_body">
    <?php if(Group::userIsInvited($this->group)) {
        $this->renderPartial('fragment_invite', array('group' => $this->group,'invitation'=>$this->groupInvitation,'private'=> Group::isPrivate($this->group)));
    } ?>
    <?php if($this->showWelcome) {
        $this->renderPartial('fragment_welcome', array('group' => $this->group));
    } ?>
    <?php if($this->userIsBanned) {
        $this->renderPartial('fragment_banned', array('group' => $this->group));
    } ?>
    <?php XG_LayoutHelper::renderLayout($this->layout, $this); ?>
</div>
<?php xg_footer(); ?>
