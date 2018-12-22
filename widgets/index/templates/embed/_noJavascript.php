<noscript>
	<style type="text/css" media="screen">
        #xg { position:relative;top:120px; }
        #xn_bar { top:120px; }
	</style>
	<div class="errordesc noscript">
		<div>
            <h3><strong><%= xg_html('HELLO_YOU_NEED_TO_ENABLE_JAVASCRIPT') %></strong></h3>
            <p><%= xg_html('PLEASE_CHECK_YOUR_BROWSER_SETTINGS') %></p>
<?php //No support page yet - see BAZ-2805
// <p><strong><a href="#">Click here to learn how to enable JavaScript on your browser.</a></strong></p>
?>
			<img src="<%= /* no cdn! */ $this->_widget->buildResourceUrl('gfx/jstrk_off.gif'); %>" height="1" width="1" />
		</div>
	</div>
</noscript>
