<?php
// Renders a bar of a small number of photos.
//
// @param photos     The photos to show
// @param sparseText The text to show when the given list of photos is empty; needs to be
//                   properly escaped
//
// Use like this in a template:
//
// $this->renderPartial('fragment_bar',
//                      'photo',
//                      array('photos' => $this->favorites));

$this->_widget->includeFileOnce('/lib/helpers/Photo_HtmlHelper.php');

$photoUrl = $this->_buildUrl('photo', 'show') . '?id=';

if (count($photos) > 0)  {
    $firstPhoto = true;
    foreach ($photos as $photo) {
        $photoTitle = xnhtmlentities($photo->title);
?>
    <dl id="user-list" class="photo clear<%= $firstPhoto ? ' first' : '' %>">
        <dt>
        <?php
        $this->renderPartial('fragment_thumbnailProper',
                             'photo',
                             array('photo'       => $photo,
                                   'thumbWidth'  => 48,
                                   'thumbHeight' => 48));
        ?>
        </dt>
        <dd>
            <a class="user" href="<?php echo $photoUrl . $photo->id ?>" title="<?php echo $photoTitle ?>"><?php echo Photo_HtmlHelper::excerpt($photoTitle, 20); ?></a>
        </dd>
        <dd><strong><?php echo $photo->my->viewCount ?></strong> <%= xg_html('N_VIEWS', $photo->my->viewCount) %></dd>
    </dl>
    <?php
        $firstPhoto = false;
    }
} else if ($sparseText) {
    echo '<p>' . $sparseText . '</p>';
}

