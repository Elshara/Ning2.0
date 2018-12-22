<?php
foreach ($this->commentInfo['comments'] as $chatter) {
    $this->renderPartial('fragment_chatter','chatter',array('comment' => $chatter,
                                                                     'friendStatus' => $friendStatus[$chatter->contributorName],
                                                                     'isResponse' => $chatter->contributorName == $responder));
}
?>