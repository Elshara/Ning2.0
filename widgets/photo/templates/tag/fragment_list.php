<?php
/**
 * @param $tags
 * @param $maxLength The maximum length of the tag text; tags longer than this will be excerpted. Defaults to 30.
 */
if (! $maxLength) { $maxLength = 30; } ?>
<strong>Tags:</strong>
<?php
for ($i = 0; $i < count($tags); $i++) {
    $url  = $this->_buildUrl('photo', 'listTagged') . '?tag=' . urlencode($tags[$i]);
    $displayTag = xnhtmlentities(Photo_HtmlHelper::excerpt($tags[$i], $maxLength)); ?>
    <a class="tag" href="<%= xnhtmlentities($url) %>"><?php echo $displayTag ?></a><%= $i < count($tags) - 1 ? ', ' : '' %>
<?php
} ?>

