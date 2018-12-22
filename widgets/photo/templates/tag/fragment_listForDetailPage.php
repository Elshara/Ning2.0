<?php
if (count($tags)) {
    $this->renderPartial('fragment_list', 'tag', array('tags' => $tags, 'maxLength' => 15));
}
