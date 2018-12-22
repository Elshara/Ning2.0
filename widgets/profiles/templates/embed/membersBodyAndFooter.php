<?php if(count($this->activeProfiles) > 0) { ?>
<div class="xg_module_body<%= (($this->smallthumbs) ? ' body_small' : ' body_large') %>">
    <ul class="clist">
        <?php
        $n = 0;
        foreach ($this->activeProfiles as $profile) {
            $args = array('profile' => $profile, 'size' => ($this->smallthumbs)?48:96);
            if ($n % $this->usersPerRow == 0) {
                if ($n != 0 && !$this->smallthumbs) { echo '</ul><ul class="clist">'; }
                $args['addClass'] = 'clear'.(($this->smallthumbs)?' tiny':'');
                $args['endRow'] = false;
            } else {
                $args['addClass'] = ($this->smallthumbs)?'tiny':'';
                $args['endRow'] = true;
            }
            $n++;
            if ($this->smallthumbs==true) $args['showScreenName'] = false;
            $this->renderPartial('fragment_user_thumbnail', $args);
        } ?>
    </ul>
</div>
<?php } elseif(count($this->activeProfiles) == 0 && $this->sortSet == 'featured') { 
    $this->inviteUrl = false;
    $this->viewAllUrl = false;
    ?>
    <div class="xg_module_body<%= (($this->smallthumbs) ? ' body_small' : ' body_large') %>">
        <h3><%= xg_html('THERE_ARE_NO_FEATURED_MEMBERS') %></h3>
        <p><%= xg_html('START_FEATURING_X_CLICK_Y','href="' . xnhtmlentities(W_Cache::getWidget('main')->buildRelativeUrl('admin','featuring')) .'"') %></p>
</div>
    <?php } ?>
<?php if($this->inviteUrl || $this->viewAllUrl) { ?>
<div class="xg_module_foot">
    <ul>
        <?php
        if ($this->inviteUrl) { ?>
            <li class="left"><a href="<%= xnhtmlentities($this->inviteUrl) %>" class="desc add"><%= xg_html('INVITE_MORE') %></a></li>
        <?php
        }
        if ($this->viewAllUrl && count($this->activeProfiles) > 0) { ?>
            <li class="right"><a href="<%= xnhtmlentities($this->viewAllUrl) %>"><%= xg_html('VIEW_ALL') %></a></li>
        <?php
        } ?>
    </ul>
</div>
<?php } ?>