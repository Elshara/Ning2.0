<?php XG_IPhoneHelper::header(null, '', NULL, array('displayHeader' => false, 'hideNavigation' => true)); ?>
<div id="fullscreen">
	<h1 id="photo_title"><%= $this->photos[($this->first ? $this->first : 0)]['title'] %></h1>
	<div id="frameSlider">
		<?php foreach($this->photos as $i => $photo) { ?>
			<div class="frame" id="frame<%= $i %>"><img id="photo<%= $i %>" src="<%= $photo['url'] %>" alt="<%= $photo['title'] %>"/></div>
		<?php } ?>
	</div>
	<a id="return" href="<%= $this->previousUrl %>"><%= xg_html('X') %></a>
	<div id="controls" <%=count($this->photos) > 1 ? '' : 'style="display:none"'%>>
		<a href="#" id="back" class="disabled">&#9668;</a>
		<a href="#" id="forward">&#9658;</a>
	</div>
</div>
<?php
$titles = array();
foreach ($this->photos as $i => $photo) {
	$titles[] = $photo['title'];
}
?>
<script type="text/javascript">
	var frame = <%= ($this->first ? $this->first : 0) %>;
	var titles = <%=json_encode($titles)%>;
	var total = <%= count($this->photos) %>;
</script>
<script type="application/x-javascript" src="<%= xg_cdn($this->_widget->buildResourceUrl('js/photo/show_iphone.js')) %>"></script>
<?php xg_footer(NULL,array('displayFooter' => false)); ?>