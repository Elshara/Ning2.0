<?php
if ($this->photos) {
	$ids = array();
	foreach ($this->photos as $photo) {
		$ids[] = $photo->id;
	}
	$ids = implode(',', $ids); ?>
	
	<ul class="list photos">
		<li class="section"><a href="#"><%= xg_html('PHOTOS') %></a></li>
		<?php
		foreach ($this->photos as $i => $photo) { 
	    	$thumbSize = 64; ?>
	    <li class="simple simple-<%= $thumbSize %>"><a href="<%= xnhtmlentities(W_Cache::getWidget('photo')->buildUrl('photo', 'show', array('ids' => $ids, 'first' => $i, 'previousUrl' => XG_HttpHelper::currentUrl()))) %>"><?php $this->renderPartial('fragment_thumbnailProper', 'photo', array('photo' => $photo, 'thumbWidth' => $thumbSize, 'thumbHeight' => $thumbSize)); ?></a></li>
	    <?php
		} ?> <!-- 
		if (count($this->photos) > 0) { ?>
		<li class="more"><a href="#"><%= xg_html('VIEW_MORE') %></a></li> -->
	</ul>
<?php
} ?>