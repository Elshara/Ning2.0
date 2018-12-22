<?php
if (count($this->posts)) {
    $this->renderPartial('fragment_blogposts_body', 'embed', array('posts' => $this->posts, 'showCreateLink' =>$this->showCreateLink,
                                                                    'archiveUrl' => $this->archiveUrl, 'feedUrl' => $this->feedUrl,
                                                                    'showPromotionLinks' => $this->showPromotionLinks,
                                                                    'embed' => $this->embed,
                                                                    'maxEmbedWidth' => $this->maxEmbedWidth,
                                                                    'hidePostDescription' => ($this->embed->get('displaySet') == 'titles') ));
} else {
    $this->renderPartial('fragment_blogposts_empty', 'embed', array('embed' => $this->embed));
}

