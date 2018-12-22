<?php
XG_IPhoneHelper::header($this->tab, $this->pageTitle, $this->profile, array('metaDescription' => $this->metaDescription, 'user' => $this->currentScreenName)); ?>
<?php $this->_widget->dispatch('friend', 'listColumn', array($this->profile)); ?>
<ul>
	<li class="add"><a href="<%=qh(W_Cache::getWidget('main')->buildUrl('invitation', 'new', array('previousUrl' => XG_HttpHelper::currentUrl())))%>"><%= xg_html('INVITE_MORE_PEOPLE') %></a></li>
</ul>
<?php xg_footer(NULL,NULL); ?>
