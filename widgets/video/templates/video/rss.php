<?php
XG_App::includeFileOnce('/lib/XG_FeedHelper.php');
XG_FeedHelper::outputFeed($this->videos, $this->title);