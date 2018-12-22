<?php
XG_App::includeFileOnce('/lib/XG_FeedHelper.php');
XG_FeedHelper::outputFeed($this->postInfo['posts'], $this->title, $this->profile instanceof XN_Profile ? $this->profile : NULL, NULL,
        'return xg_xmlentities(BlogPost::getTextTitle($object));', 'publishTime');
