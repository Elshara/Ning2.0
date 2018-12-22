<?php
XG_App::ningLoaderRequire('xg.index.tablayout.edit');
	$tabs = XG_TabLayout::loadOrCreate()->getTabs();
	foreach ($this->restrictedTabKeys as $k) {
		if (isset($tabs[$k])) {
			$tabs[$k]->isFixed = true;
		}
	}
?>
<script>
var xgTabMgrTabs = <%=json_encode(array_values($tabs))%>,
	xgTabMgrMaxTopTabs = <%=json_encode($this->maxTabs)%>,
	xgTabMgrMaxNonFixedTopTabs = <%=json_encode($this->maxTabs-count($this->restrictedTabKeys))%>,
	xgTabMgrMaxSubTabs = <%=json_encode($this->maxSubTabsPerTab)%>,
	xgCreatePageUrl = <%=json_encode($this->_buildUrl('tablayout','createPage','?xn_out=json'))%>;
</script>
<p><a href="javascript:void(0);" id="xj_add_tab" class="desc add"><%=xg_html('ADD_NEW_TAB')%></a></p>
<span id="xj_help" class="context_help" style="display:none;position:absolute;z-index:100">
	<a dojoType="ContextHelpToggler" href="#"><img src="<%= xg_cdn(W_Cache::getWidget('main')->buildResourceUrl('gfx/icon/help.gif')) %>" alt="?" title="<%= xg_html('WHAT_IS_THIS') %>" /></a>
    <span id="xj_help_box" class="context_help_popup" style="display:none;">
		<span class="context_help_content">
			<span id="xj_help_msg"></span>
			<small><a dojoType="ContextHelpToggler" href="#"><%= xg_html('CLOSE') %></a></small>
		</span>
    </span>
</span>
<p id="xj_placeholder"><img src="<%=xg_cdn('/xn_resources/widgets/index/gfx/icon/spinner.gif')%>"/></p>
<ul id="xj_tab_manager" class="xg_tab_manager"></ul>
