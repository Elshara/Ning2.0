<?php $hideId = md5(uniqid()); ?>
<div class="notification easyclear">
  <p><big><%= $this->message%></big></p>
  <p class="right"><a href="javascript:void(0)" id="<%= $hideId %>"><%= xg_html('HIDE_THIS_MESSAGE') %></a></p>
</div>
<script type="text/javascript">
xg.addOnRequire(function(){
	dojo.event.connect(dojo.byId('<%= $hideId %>'), 'onclick', function(evt) {
		dojo.event.browser.stopEvent(evt);
		dojo.io.bind({
			'url': '<%= $this->_buildUrl('embed', 'hideTempMessage',array('id' => $this->embedLocator, 'xn_out' => 'json')) %>',
			'load': function(type, data, evt2) {
				var node = dojo.byId('<%= $hideId %>');
				var div = node.parentNode.parentNode;
				div.parentNode.removeChild(div);
			}
		})
	});
});
</script>
