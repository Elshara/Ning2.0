<?php
if ($this->embed->isOwnedByCurrentUser()) {
    XG_App::ningLoaderRequire('xg.profiles.embed.blog');
}
if (XG_SecurityHelper::userIsAdmin() && $this->showPromotionLinks) {
    XG_App::ningLoaderRequire('xg.index.actionicons');
}
// If there are no posts, you don't see anything unless:
// -- you're looking at your own page
// -- you're the admin and you're looking at the front page
if ((count($this->posts) == 0) && (! $this->embed->isOwnedByCurrentUser())) {
    return;
}
?>
<div class="xg_module module_blog"<?php
  if ($this->embed->isOwnedByCurrentUser()) {
      $json = new NF_JSON(); ?>
    dojoType="BlogModule"
    isContainer="true"
    _url="<%= xnhtmlentities($this->embedUrl) %>"
    _updateUrl="<%= xnhtmlentities($this->embedUpdateUrl) %>"
    _displayOptionsJson="<%= xnhtmlentities($json->encode($this->display_options)) %>"
  <?php if ($this->sort_options) { ?>
      _sortOptionsJson="<%= xnhtmlentities($json->encode($this->sort_options)) %>"
  <?php } ?>
    _postsOptionsJson="<%= xnhtmlentities($json->encode($this->posts_options)) %>"
    _layoutType="<%= xnhtmlentities($this->embedLayoutType) %>"
    _displaySet="<%= xnhtmlentities($this->embed->get('displaySet'))%>"
    _sortSet="<%= xnhtmlentities($this->embedSelected) %>"
    _postsSet="<%= xnhtmlentities($this->embed->get('postsSet'))%>"
<?php } ?>>
    <div class="xg_module_head">
        <h2><%= xnhtmlentities($this->embedTitle) %></h2>
    </div>
    <div class="xg_module_body_wrapper">
        <?php
        if ((count($this->posts) && $this->embed->get('postsSet') != 0)) {
        $this->renderPartial('fragment_blogposts_body', array('posts' => $this->posts, 'maxEmbedWidth' => $this->maxEmbedWidth, 'showCreateLink' => $this->showCreateLink,
                                                                     'feedUrl' => $this->feedUrl, 'archiveUrl' => $this->archiveUrl, 'showPromotionLinks' => $this->showPromotionLinks,
                                                                     'embed' => $this->embed, 'feedAutoDiscoveryTitle' => $this->embedTitle, 'hidePostDescription' => ($this->embed->get('displaySet') == 'titles') ));
        } else {
            $this->renderPartial('fragment_blogposts_empty', array('embed' => $this->embed));
        }
        ?>
    </div>
</div>

