<?php
/**
 * @param $albums
 */
?>
<ul>
<?php 
for ($i = 0; $i < count($albums); $i++) {
    $album = $albums[$i];
    ?>
    <li><a href="<%= xnhtmlentities($this->_buildUrl('album', 'show') . '?id=' . $album->id) %>"><%= xnhtmlentities($album->title) %></a></li>

<?php
} ?>
    </ul>