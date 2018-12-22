<?php 
if ($this->enabledModules['activity'] != null) {
	W_Cache::getWidget('activity')->dispatch('log', 'list');
} 
elseif ($this->enabledModules['forum'] != null) {
	W_Cache::getWidget('forum')->dispatch('index', 'index');
}
else {
	W_Cache::getWidget('profiles')->dispatch('index', 'index');
}
?>