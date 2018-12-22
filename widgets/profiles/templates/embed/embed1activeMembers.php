<?php
$membersTitle = xg_html('MEMBERS');
if ($this->embed->get('sortSet') == 'featured') {
    $membersTitle = xg_html('FEATURED_MEMBERS');
}
if ($_GET['debug_show_random_number_in_member_box']) { var_dump(mt_rand()); }
if (! $this->embed->isOwnedByCurrentUser()) {
    if (count($this->activeProfiles) > 0) { ?>
<div class="xg_module module_members">
<?php
    }
} else {
$json = new NF_JSON();
XG_App::ningLoaderRequire('xg.profiles.embed.embed'); ?>
<div class="xg_module module_members" dojoType="MembersModule"
       _setValuesUrl="<%= xnhtmlentities($this->setValuesUrl)%>"
       _displayOptionsJson="<%= xnhtmlentities($json->encode($this->display_options))%>"
       _sortOptionsJson="<%= xnhtmlentities($json->encode($this->sort_options))%>"
       _rowsOptionsJson="<%= xnhtmlentities($json->encode($this->rows_options))%>"
       _displaySet="<%= xnhtmlentities($this->embed->get('displaySet'))%>"
       _sortSet="<%= xnhtmlentities($this->embed->get('sortSet'))%>"
       _rowsSet="<%= xnhtmlentities($this->embed->get('rowsSet'))%>">
<?php
}

if (($this->embed->isOwnedByCurrentUser()) || (count($this->activeProfiles) > 0)) { ?>
    <div class="xg_module_head">
        <h2><%= $membersTitle %></h2>
    </div>
    <?php $this->_widget->dispatch('embed', 'membersBodyAndFooter', array(array('profiles' => $this->activeProfiles, 'smallthumbs' => $this->smallthumbs, 'usersPerRow' => $this->usersPerRow, 'inviteUrl' => $this->inviteUrl, 'viewAllUrl' => $this->viewAllUrl, 'sortSet' => $this->embed->get('sortSet')))); ?>
</div><?php
} ?>
