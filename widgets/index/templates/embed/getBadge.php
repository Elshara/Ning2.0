<div class="xg_module module_badge">
    <div class="xg_module_head">
    <h2><%= xg_html('GET_NETWORK_BADGE', xnhtmlentities(XN_Application::load()->name)) %></h2>
    </div>
    <div class="xg_module_body">
		<%= $this->_widget->dispatch('embeddable', 'embeddable', array(array('large' => false,'internal'=>1))); %>
    </div>
    <div class="xg_module_foot">
        <p class="right"><a href="<%= xnhtmlentities($this->_widget->buildUrl('embeddable', 'list')) %>"><%= xg_html('GET_MORE_BADGES') %></a></p>
    </div>
</div>
