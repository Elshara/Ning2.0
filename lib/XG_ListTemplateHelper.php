<?php

/**
 * Useful functions for displaying list pages, like the photo/list and video/list pages.
 */
class XG_ListTemplateHelper {

    /**
     * Displays the list page.
     *
     * If you are caching the output, you may need to explicitly
     *
     * @param $cssInfix string  string to use for the middle part of the CSS classes, e.g., albums
     * @param $featuredTitleText string  plain text for the title of the Featured section
     * @param $featuredObjects array  the promoted XN_Content objects to display
     * @param $isSortRandom  boolean  whether the results are sorted randomly
     * @param $showViewAllFeaturedUrl boolean  whether to show the View All link in the Featured section
     * @param $viewAllFeaturedUrl string  URL for the View All link in the Featured section
     * @param $objects array  the XN_Content objects to display
     * @param $numObjects integer  the total number of objects
     * @param $rowSize integer  number of objects per row
     * @param $pageSize integer  the number of objects per page
     * @param $sortOptions array  array of arrays, each with displayText, url, and selected; or null to hide the Sort By drop-down
     * @param $searchUrl string  URL for the search endpoint
     * @param $searchButtonText string the text that appears for the search button. Defaults to xg_text('SEARCH') optional
     * @param $tabsHtml string  HTML for the tabs, if any
     * @param $extraTemplateArgs array  extra arguments to pass to the model templates.
     *         Model templates are passed $i denoting their overall position, $column denoting their
     *         position in the row, and ${type} for the object (e.g., $photo)
     * @param $feedUrl string  URL of the comment feed, or null if no such feed exists.
     *         Ignored for private networks and private groups.
     * @param $feedTitle string  title for the comment feed
     * @param $feedFormat string  "atom" or "rss"
     * @param $noObjectsSubtitle string  plain text for the subheading displayed when there are no objects
     * @param $noObjectsMessageHtml string  inner HTML for the <p> displayed when there are no objects
     * @param $noObjectsShowInviteLink boolean  if no objects, show invite link
     * @param $noObjectsShowAddAsFriendLink boolean  if no objects, show add as friend link
     * @param $noObjectsAddAsFriendLinkHtml string  add as friend link html
     * @param $noObjectsLinkUrl string  URL for the page for adding new objects
     * @param $noObjectsLinkText string  plain text for the link, e.g., Add Photos
     * @param $paginationUrl string  the pagination url to pass to XG_PaginationHelper, if any
     *
     * Changed/added parameters:
	 * @param $titleHtml string  Title block. Usually is a result of xg_headline(). If not set, block is not displayed.
	 * 							Ignored for search result pages.
	 * @param $itemCallback	callback	Callback to display items (@see XG_PageHelper::objectList())
     */
	public function outputListPage($args) {
		$isSearch = isset($_GET['q']);
    	if ($_GET['test_empty']) {
			$args['numObjects'] = 0;
			$args['objects'] = array();
		}
    	if (!$isSearch) {
			XG_PageHelper::featuredObjects(array(
				'titleHtml'	=> $args['featuredTitleText'],
				'cssInfix' => $args['cssInfix'],
				'objects' => $args['featuredObjects'],
				'viewAllUrl' => $args['showViewAllFeaturedUrl'] ? $args['viewAllFeaturedUrl'] : '',
				'callback' => array(__CLASS__,'_renderFeaturedItem'),
				'args' => $args['extraTemplateArgs'],
			));
		}

		if ($args['titleHtml']) {
			if ($isSearch) {
				echo xg_headline( xg_text('SEARCH_RESULTS'), array( 'count' => $args['numObjects']) );
			} else {
				echo $args['titleHtml'];
			}
		}

		echo '<div class="xg_module">';
			if ($args['numObjects'] == 0 && $isSearch) {
				XG_PageHelper::searchBar(array(
					'url' => $args['searchUrl'],
					'buttonText' => $args['searchButtonText'],
					'sortOptions' => NULL,
				));
                echo '<div class="xg_module_body">';
					echo $args['tabsHtml'];
                    echo '<p>', xg_html('NO_RESULTS_FOUND_FOR_SEARCH_TERM', qh($_GET['q'])), '</p>';
                echo '</div>';
			} else if ($args['numObjects'] == 0) {
				echo '<div class="xg_module_body">';
                    echo $args['tabsHtml'];
                    if ($args['noObjectsSubtitle']) { echo '<h3>' . xnhtmlentities($args['noObjectsSubtitle']) . '</h3>'; }
                    echo '<p>' . $args['noObjectsMessageHtml'] . '</p>';
                    if ($args['noObjectsShowInviteLink']) {
                        echo '<p><a href="/invite" class="desc add">'.xg_html('INVITE_FRIENDS').'</a></p>';
                    }
                    if ($args['noObjectsShowAddAsFriendLink'] && $args['noObjectsAddAsFriendLinkHtml']) {
                        echo '<p>'.$args['noObjectsAddAsFriendLinkHtml'].'</p>';
                    }
					if ($args['noObjectsLinkUrl']) {
						echo '<p><a href="' . qh($args['noObjectsLinkUrl']) . '" class="desc add">' . qh($args['noObjectsLinkText']) . '</a></p>';
					}
                echo '</div>';
			} else {
				XG_PageHelper::searchBar(array(
					'url' => $args['searchUrl'],
					'buttonText' => $args['searchButtonText'],
					'sortOptions' => $args['sortOptions'],
				));
				echo '<div class="xg_module_body">';
					$olArgs = array(
						'noPagination' => $args['isSortRandom'],
						'callback' => $args['itemCallback'] ? $args['itemCallback'] : array(__CLASS__,'_renderItem'),
						'args' => $args['extraTemplateArgs'],
					);
					foreach(array('paginationUrl', 'pageSize', 'numObjects', 'rowSize', 'cssInfix', 'objects') as $key) {
						$olArgs[$key] = $args[$key];
					}
					echo $args['tabsHtml'];
					XG_PageHelper::objectList($olArgs);
				echo '</div>';
				if ($args['feedUrl'] && !XG_App::appIsPrivate() && !XG_GroupHelper::groupIsPrivate()) {
					xg_autodiscovery_link($args['feedUrl'], $args['feedTitle'], $args['feedFormat']);
					echo '<div class="xg_module_foot"><p class="left">';
						echo '<a class="desc rss" href="' . qh($args['feedUrl']) . '">' . xg_html('RSS') .'</a>';
					echo '</p></div>';
				}
			}
		echo '</div>';
    }

	public function _renderFeaturedItem ($args) { # void
		$object = $args['object'];
		$args[XG_LangHelper::lcfirst($object->type)] = $object;
		W_Content::create($object)->render('featuredListItem', $args);
	}

    public function _renderItem ($args) { # void
		$object = $args['object'];
		$args[XG_LangHelper::lcfirst($object->type)] = $object;
		W_Content::create($object)->render('listItem', $args);
    }
}
