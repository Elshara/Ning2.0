<?php
$this->_widget->includeFileOnce('/lib/helpers/Index_MembersEndpointHelper.php');
echo Index_MembersEndpointHelper::instance()->buildFeed($this->profiles);
