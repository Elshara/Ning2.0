<?php xg_header('invite', $title = xg_text('INVITE_TO_GROUP', XG_GroupHelper::currentGroup()->title)); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_2col first-child" style="margin-left:235px;">
			<%= xg_headline($title)%>
            <%= XG_GroupHelper::groupLink() %>
            <?php W_Cache::getWidget('main')->dispatch('invitation', 'contactList', array($this->invitationArgs)); ?>
        </div>
    </div>
</div>
<?php xg_footer(null, array('parseWidgets' => false)); ?>
