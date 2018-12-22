<?php

if ($itemCount == 1) {
    $itemId = $item->type == 'User' ? 'u_' . $item->title : $item->id;
    $itemLink = 'href="http://' . $_SERVER['HTTP_HOST'] . '/xn/detail/' . $itemId . '"';
} else {
    $itemLink = 'href="http://' . $_SERVER['HTTP_HOST'] . '/' . $listLink . '"';
}

$itemTitle = $item->type == 'User' ? XG_FullNameHelper::fullName($item->title) : $item->title;
    // Huy Hong, 9/4/08, BAZ-9627: Keep this h5 until we have thumbnails of the featured content
    echo '<h5 class="xg_lightfont">' . xg_html('FEATURED') . '</h5>';
if (XG_App::onMyProfilePage()) {
    echo '<span class="message">'
    . xg_html(
        'YOUR_' . $textType. '_WAS_FEATURED',
        $itemCount,
        $itemLink,
        $item->title == '' ? xg_html('UNTITLED') : xnhtmlentities($itemTitle)
    )
    . '</span>';
} else if (XG_App::onProfilePage() && $item->type == 'User'){
    if (count($members) > 1) {
        echo '<span class="message">'
        . xg_html('THIS_PROFILE_WAS_FEATURED')
        . '</span>';
    } else {
        echo '<span class="message">'
        . xg_html(
            'XS_PROFILE_WAS_FEATURED',
            $itemTitle
        )
        . '</span>';
    }
} else {
    echo '<span class="message">'
    . xg_html(
        $textType . '_S_WAS_FEATURED',
        $itemCount,
        $itemLink,
        $item->title == '' ? xg_html('UNTITLED') : xnhtmlentities($itemTitle),
        $who,
        xnhtmlentities($fullname)
    )
    . '</span>';
}
echo $timeStamp;
if (!empty($extra)) {
    echo $extra;
}
if (!empty($photos)) {
    $this->renderPartial('fragment_thumb_photos', 'log', array('photos' => $photos));
}
if (!empty($videos)) {
    $this->renderPartial('fragment_thumb_videos', 'log', array('videos' => $videos));
}

