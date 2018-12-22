<?php
if ($this->numVideos) { ?>
    <li><a href="<%= xnhtmlentities($this->_buildUrl('video', 'listForApproval')) %>"><%= xg_html('N_VIDEOS', xg_number($this->numVideos)) %></a></li>
<?php
}
