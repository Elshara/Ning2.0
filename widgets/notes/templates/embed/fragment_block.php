<?php
/*  $Id: $
 *
 *  Renders content body for embed module
 *
 *  Parameters:
 *		$content		XG_PagingList<Note>|Note
 *		$settings		struct{display,from,count}
 *		$columns		int
 *		$viewAllUrl		string
 */
W_Cache::getWidget('notes')->includeFileOnce('/lib/helpers/Notes_TemplateHelper.php');
if ($content === NULL || !($content instanceof XG_PagingList)) {
    if ($content) { ?>
        <div class="xg_module_body">
            <?php
            echo '<h3><a href="'.Notes_UrlHelper::noteUrl($content).'">'.xnhtmlentities($content->title).'</a></h3>';
            echo Notes_TemplateHelper::excerpt($content, 30000); // BAZ-7175 [Jon Aquino 2008-04-08]
            ?>
        </div>
    <?php
    }
} ?>
<?php if (0 != count($content)) { ?>
    <div class="xg_module_body">
        <?php
        foreach($content as $note) { $url = Notes_UrlHelper::noteUrl($note); ?>
        <div class="note">
            <h3><a href="<%=xnhtmlentities($url)%>"><%=xnhtmlentities($note->title)%></a></h3>
            <?php if($settings['display'] == 'details') {
                echo '<p class="last-child">';
                echo xg_resize_embeds(xg_excerpt_html($note->description, 500, $excerpted));
                if ($excerpted) {
                    echo ' <a href="',xnhtmlentities($url),'">',xg_html('CONTINUE'),'</a>';
                }
                echo '</p>';
            }?>
            <p class="small xg_lightfont"><%=Notes_TemplateHelper::noteInfo($note)%></p>
        </div>
        <?php
        } ?>
    </div>
<?php
}
$links = array();
if (XG_SecurityHelper::userIsAdmin()) {
    if ($settings['from'] == 'featured' && $content instanceof XG_PagingList && count($content) == 0) {
        echo '<div class="xg_module_body">';
        echo '<h3>' . xg_html('THERE_ARE_NO_FEATURED_NOTES') . '</h3>';
        echo '<p>' . xg_html('START_FEATURING_X_CLICK_Y', 'href="' . xnhtmlentities(W_Cache::getWidget('main')->buildRelativeUrl('admin','featuring')) .'"') . '</p>';
        echo '</div>';
    } else {
		$this->renderPartial('fragment_addNoteForm','index');
        $links[] = '<li class="left"><a class="desc add" style="display:none" href="#" dojoType="AddNoteLink" _baseUrl="' .
            xnhtmlentities(Notes_UrlHelper::noteUrl(NULL)).'" _maxLength="'.Note::MAX_TITLE_LENGTH.'">'.xg_html('ADD_NOTE').'</a></li>';
    }
}
if (count($content)) {
    $links[] = '<li class="right"><a href="'.xnhtmlentities($this->viewAllUrl).'">'.xg_html('VIEW_ALL').'</a></li>';
}
if ($links) { ?>
<div class="xg_module_foot">
    <ul><?php foreach($links as $l) { echo $l; }?></ul>
</div>
<?php
}?>
