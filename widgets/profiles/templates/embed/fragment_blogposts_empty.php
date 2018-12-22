<?php
/** Partial template that displays the appropriate sparse message for the embed
 * This template assumes that the caller has done the right calculations to assure that
 * the current user should see the message this partial displays.
 *
 * @param $embed The embed
 */
 if ($this->embed->get('postsSet') == 0) {
     $line1  = null; 
     $line2  = null; 
     $anchor = xg_html('ADD_A_BLOG_POST2');
     $linkClass = 'add';
 } else {
     if ($embed->getType() == 'profiles') {
         $line1 = xg_html('YOU_HAVE_NOT_ADDED_BLOG_POSTS');
         $line2 = xg_html('YOU_CAN_WRITE_ABOUT_ANYTHING');
         $anchor = xg_html('WRITE_YOUR_FIRST_ENTRY');
         $linkClass = 'edit';
     } else {
         if ($embed->get('selected') == 'promoted') {
             $line1 = xg_html('THERE_ARE_NO_FEATURED_X', mb_strtolower(xg_html('BLOG_POSTS')));
             $line2 = xg_html('START_FEATURING_X_CLICK_Y', 'href="' . xnhtmlentities(W_Cache::getWidget('main')->buildRelativeUrl('admin','featuring')) .'"');
             $anchor = null;
         } elseif ($embed->get('selected') == 'owner') {
             $line1 = xg_html('THERE_ARE_NO_X_THAT_MATCH_SETTINGS', mb_strtolower(xg_html('BLOG_POSTS')));
             $line2 = xg_html('CLICK_EDIT_AND_CHANGE_SETTINGS', mb_strtolower(xg_html('BLOG_POSTS')));
             $anchor = null;
         } else {
             $anchor = xg_html('ADD_A_BLOG_POST');
             $linkClass = 'add';
         }
     }
 }
?>
<?php if (! is_null($line1) && ! is_null($line2)) { ?>
    <div class="xg_module_body">
      <h3><%= $line1 %></h3>
      <p><%= $line2 %></p>
    </div>
<?php } ?>
<?php if (! is_null($anchor)) { ?>
<div class="xg_module_foot">
  <ul><li class="left"><a href="<%= xnhtmlentities($this->_buildUrl('blog','new')) %>" class="desc <%= $linkClass %>"><%= $anchor %></a></li></ul>
</div>
<?php } ?>
