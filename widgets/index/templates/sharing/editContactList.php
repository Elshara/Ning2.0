<?php xg_header(null, $this->pageTitle, null, array('hideNavigation' => true)) ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_2col first-child" style="margin-left:235px;">
			<%= xg_headline($this->pageTitle)%>
            <?php W_Cache::getWidget('main')->dispatch('invitation', 'contactList', array($this->invitationArgs)); ?>
        </div>
    </div>
</div>
<?php xg_footer(null, array('parseWidgets' => false)); ?>
