<?php
/*  $Id: $
 *
 *  Displays the list of notes (search, all, etc)
 *
 *  Parameters:
 *	$this->pageTitle        Title of this page
 *      $this->heading          H1 (heading) of this page
 *	$this->hideSort		Hide sort block
 *	$this->sort		current sort order
 *	$this->allNotes	        XG_PagingList<Note>
 *	$this->canCreate
 */
XG_App::includeFileOnce('/lib/XG_Form.php');
XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
XG_App::includeFileOnce('/lib/XG_PageHelper.php');

$f = new XG_Form(array('sort'=>$this->sort));
xg_autodiscovery_link($this->feedUrl, $this->title, 'atom');
?>
<?php xg_header(W_Cache::current('W_Widget')->dir, $this->pageTitle); ?>
<div id="xg_body">
    <div class="xg_column xg_span-16">
        <?php $this->renderPartial('fragment_navigation') ?>
        <div class="xg_module">
            <?php
                $searchUrl = Notes_UrlHelper::url('search');
                $sortOptions = array(
                	'updated' => array('displayText' => xg_html('RECENTLY_UPDATED'), 'url' => xg_url(true,'sort=updated')),
                	'created' => array('displayText' => xg_html('RECENTLY_ADDED'), 'url' => xg_url(true,'sort=created')),
                    'alpha' => array('displayText' => xg_html('ALPHABETICAL'), 'url' => xg_url(true,'sort=alpha')),
				);
            ?>
			<?php if (count($this->featuredNotes)) {
				echo xg_headline($this->featuredNotesHeading);
                if ($this->onlyFeatured) { ?>
                    <%= XG_PageHelper::searchBar(array(
                            'url' => $searchUrl,
                            'buttonText' => xg_html('SEARCH_NOTES'))); %>
                <?php } ?>
                <div class="xg_module_body notes_list">
                    <?php foreach($this->featuredNotes as $note) { $url = Notes_UrlHelper::noteUrl($note); ?>
                        <h3><a href="<%=$url%>"><%=xnhtmlentities($note->title)%></a></h3>
                        <p class="last-child"><?php
                            echo xg_resize_embeds(xg_excerpt_html($note->description, $this->excerptLength, $excerpted));
                            if ($excerpted) {
                                echo ' <a href="',xnhtmlentities($url),'">',xg_html('CONTINUE'),'</a>';
                            }
                        ?>
                        <p class="small xg_lightfont"><%=Notes_TemplateHelper::noteInfo($note)%></p>
                    <?php }?>
                    <?php if ($this->onlyFeatured) {
                        XG_PaginationHelper::outputPagination($this->featuredNotes->totalCount, $this->featuredNotes->pageSize);
                    } ?>
                </div>
                <?php if ($this->showViewAllFeaturedUrl) {?>
                    <div class="xg_module_foot">
                        <p class="right"><a href="<%= xnhtmlentities($this->viewAllFeaturedUrl) %>"><%= xg_html('VIEW_ALL') %></a></p>
                    </div>
                <?php } ?>
            <?php } ?>

			<?php if (!$this->onlyFeatured) {
				echo xg_headline($this->title, array('count' => $this->allNotes->totalCount));
                $searchBarArray = array('url' => $searchUrl,
                                              'buttonText' => xg_html('SEARCH_NOTES'));
                if (!$this->hideSort) $searchBarArray['sortOptions'] = $sortOptions;
                ?>
                <%= XG_PageHelper::searchBar($searchBarArray); %>
                <div class="xg_module_body notes_list">
                    <?php foreach($this->allNotes as $note) { $url = Notes_UrlHelper::noteUrl($note); ?>
                        <h3><a href="<%=$url%>"><%=xnhtmlentities($note->title)%></a></h3>
                        <p class="last-child"><?php
                            echo xg_resize_embeds(xg_excerpt_html($note->description, $this->excerptLength, $excerpted));
                            if ($excerpted) {
                                echo ' <a href="',xnhtmlentities($url),'">',xg_html('CONTINUE'),'</a>';
                            }
                        ?>
                        <p class="small xg_lightfont"><%=Notes_TemplateHelper::noteInfo($note)%></p>
                    <?php }?>
                    <?php XG_PaginationHelper::outputPagination($this->allNotes->totalCount, $this->allNotes->pageSize);?>
                </div>
                <?php if ($this->feedUrl && !XG_App::appIsPrivate()) { ?>
                    <div class="xg_module_foot">
                        <p class="last-child"><a class="desc rss" href="<%= $this->feedUrl %>"><%= xg_html('RSS') %></a></p>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
    <div class="xg_column xg_span-4 last-child">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>
