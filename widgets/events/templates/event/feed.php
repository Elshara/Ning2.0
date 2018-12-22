<?php
XG_App::includeFileOnce('/lib/XG_FeedHelper.php');
XG_FeedHelper::outputFeed($this->events, $this->title);
