<?php
/**
 * The body and footer of the Page module, which displays recent or popular pages and comments on the homepage and profile page.
 *
 * @param $pagesAndComments array  The Page and Comment objects to display
 * @param $pages array  A mapping of page IDs to Page objects
 * @param $columnCount integer  The number of columns that the module will span
 * @param $embed XG_Embed  Stores the module data.
 * @param $showContributorName boolean  Whether to show the name of the contributor
 */
foreach($pagesAndComments as $pageOrComment) {
	$comment = $pageOrComment->type == 'Comment' ? $pageOrComment : NULL;
	$page = $pageOrComment->type == 'Comment' ? $pages[$pageOrComment->my->attachedTo] : $pageOrComment; ?>
	<div class="xg_module_body">
		<?php
		if ($columnCount > 1) {
			$this->renderPartial('fragment_page', 'page', array('page' => $page, 'comment' => $comment, 'showListForContributorLink' => TRUE, 'showContributorName' => $showContributorName));
		} else { ?>
			<div class="wpage vcard">
				<h3>
					<a href="<%= xnhtmlentities(W_Cache::getWidget('main')->buildUrl('index', 'detail', array('id' => $pageOrComment->id))) %>"><%= xg_excerpt($pageOrComment->type == 'Page' ? $pageOrComment->title : $pageOrComment->description, 50) %></a>
					<?php
					// Keep this in sync with fragment_page.php  [Jon Aquino 2007-02-03]
					$counts = Comment::getCounts($page);
					if ($pageOrComment->type == 'Page' && $counts['commentCount']) { ?>
						<small><%= str_replace(' ', '&nbsp;', xg_html('N_REPLIES', $counts['commentCount'])) %></small>
					<?php
					} ?>
				</h3>
				<p><small><%= $this->renderPartial('fragment_metadata', 'page', array('pageOrComment' => $pageOrComment, 'showContributorName' => $showContributorName, 'terse' => TRUE)); %></small></p>
			</div>
		<?php
		} ?>
	</div>
<?php
}
if (! count($pages) && $embed->isOwnedByCurrentUser()) { ?>
	<div class="xg_module_body">
		<h3><strong><%= xg_html('THERE_ARE_NO_DISCUSSIONS') %></strong></h3>
		<p><%= xg_html('THIS_BOX_WILL_NOT_SHOW_DISCUSSIONS') %></p>
		<p><a href="<%= xnhtmlentities($this->_buildUrl('page','new')) %>" class="desc add"><%= xg_html('START_A_DISCUSSION') %></a></p>
	</div>
<?php
}
if (count($pages)) { ?>
	<div class="xg_module_foot">
		<?php
		if ($embed->isOwnedByCurrentUser()) { ?>
			<p class="left"><a class="desc add" href="<%= xnhtmlentities($this->_buildUrl('page', 'new')) %>"><%= xg_html('START_DISCUSSION') %></a></p>
		<?php
		} ?>
		<p class="right"><a href="<%= xnhtmlentities($embed->getType() == 'profiles' ? $this->_buildUrl('page', 'listForContributor', array('user' => $embed->getOwnerName())) : $this->_buildUrl('index', 'index')) %>"><%= xg_html('VIEW_ALL') %></a></p>
	</div>
<?php
} ?>
