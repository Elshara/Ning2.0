<?php
if ($this->numPhotos) { ?>
    <li><a href="<%= xnhtmlentities($this->_buildUrl('photo', 'listForApproval')) %>"><%= xg_html('N_PHOTOS', xg_number($this->numPhotos)) %></a></li>
<?php
}