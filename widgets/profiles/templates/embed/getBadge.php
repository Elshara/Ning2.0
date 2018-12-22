<?php $galleryUrl = W_Cache::getWidget('main')->buildUrl('embeddable', 'list'); ?>
<div class="xg_module module_badge">
    <div class="xg_module_head">
    <h2><%= xg_html('GET_NETWORK_BADGE', xnhtmlentities(XN_Application::load()->name)) %></h2>
    </div>
    <div class="xg_module_body">
        <?php
        if(User::isMember(XN_Profile::current())) {
            $this->_widget->dispatch('profile', 'embeddable', array(array('username' => XN_Profile::current()->screenName,'internal'=>1)));
        }else {
			W_Cache::getWidget('main')->dispatch('embeddable', 'embeddable', array(array('large' => false, 'includeFooterLink' => false, 'internal'=>1)));
        } ?>
    </div>
    <div class="xg_module_foot">
        <p class="right"><a href="<%= xnhtmlentities($galleryUrl) %>"><%= xg_html('GET_MORE_BADGES') %></a></p>
    </div>
</div>
