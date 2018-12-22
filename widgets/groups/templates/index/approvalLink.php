<?php
if ($this->numGroups) { ?>
    <li><a href="<%= xnhtmlentities($this->_buildUrl('admin', 'listForApproval')) %>"><%= xg_html('N_GROUPS', xg_number($this->numGroups)) %></a></li>
<?php
}