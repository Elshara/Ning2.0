<?php
XG_App::includeFileOnce('/lib/XG_FeedHelper.php');
XG_FeedHelper::outputFeed($this->friends, $this->title, $this->profile,
        'return \'<img src="\' . xg_xmlentities(XG_UserHelper::getThumbnailUrl(XG_Cache::profiles($object->contributorName),48,48)) . \'" width="48" height="48" alt=""/>\';');
