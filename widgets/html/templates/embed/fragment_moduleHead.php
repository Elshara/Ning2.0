<?php if ($this->title) { ?>
    <div class="xg_module_head"><h2><%= xnhtmlentities($this->title) %></h2></div>
<?php } elseif ($this->userCanEdit && $this->hasDefaultContent) { ?>
    <div class="xg_module_head"><h2><%= xg_html('YOUR_X_BOX') %></h2></div>
<?php } elseif ($this->userCanEdit) { ?>
    <div class="xg_module_head"><h2>&nbsp;</h2></div><?php // Accomodate Edit-button height. [Jon Aquino 2008-01-14] ?>
<?php } else { ?>
    <div class="xg_module_head notitle"></div>
<?php } ?>