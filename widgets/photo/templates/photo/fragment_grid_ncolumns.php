<?php
/**
 * Renders a multi column grid of small photo thumbs.
 *
 * @param changeUrl  The url to use when the page has been changed. May contain parameters
 * @param sortParamName  The name of the sort parameter; if not given, then the url in the pagination won't include a sort parameter
 * @param selectedSorting  The currently selected sorting, one of the items returned by Photo_PhotoHelper::getKnownSortingOrders()
 * @param pageParamName  The name of the curPage parameter
 * @param photos  The photos (array of Photo objects)
 * @param curPage  The current page to show
 * @param numPages  The total number of pages
 * @param sortTitle
 * @param numColumns  The number of columns to use in the grid (default is 4)
 * @param feedUrl  Optional url for an appropriate rss feed
 * @param feedTitle
 * @param feedLabel
 * @param moreLink
 * @param slideshowLink The url for the associated slideshow
 * @param addPhotos  Whether to show the link for adding new photos
 * @param supressHead  supresses the head div for display on the front page
 * @param thumbSize  for custom thumb sizes; used on front page widget
 * @param notification  optional message to display at the top
 * @param $context
 * @param $albumId
 */
$this->_widget->includeFileOnce('/lib/helpers/Photo_HtmlHelper.php');
$this->_widget->includeFileOnce('/lib/helpers/Photo_PhotoHelper.php');
if (!isset($numColumns)) { $numColumns = 3; }
if (!isset($thumbSize)) { $thumbSize = 124; }
$selectedSortingValue = null;
if ($sortParamName) {
    foreach (Photo_PhotoHelper::getKnownSortingOrders() as $optionValue => $optionData) {
        if ($selectedSorting['name'] == $optionData['name']) {
            $selectedSortingValue = $optionValue;
        }
    }
}
if ($selectedSortingValue) {
    $paginationUrl = Photo_HtmlHelper::addParamToUrl($changeUrl, $sortParamName, $selectedSortingValue);
} else {
    $paginationUrl = $changeUrl;
} ?>
<?php /* TODO: Fix inconsistent indenting in this file [Jon Aquino 2008-01-24] */ ?>
    <?php if ($supressHead == false) { ?>
    <div class="xg_module_head notitle"></div>
    <?php
    if ($notification) { ?>
        <div class="xg_module_body notification">
            <p><%= xnhtmlentities($notification) %></p>
        </div>
    <?php
    } ?>
    <div class="xg_module_body">
    <?php } ?>
    <?php
    if ($sortTitle) { ?>
        <h3 class="left">
            <%= xnhtmlentities($sortTitle) %>
            <?php if ($moreLink) { ?><a href="<%= xnhtmlentities($moreLink) %>"><%= xg_html('MORE') %>&nbsp;&#187;</a> <?php } ?>
        </h3>
    <?php
    }
    if ($slideshowLink) { ?>
        <p class="right"><strong><a href="<%= $slideshowLink %>"><%= xg_html('VIEW_AS_SLIDESHOW') %></a></strong></p>
    <?php
    }
    if ($sortParamName && count($this->photos) > 1) { ?>
        <p class="right clear">
            <?php
            $this->renderPartial('fragment_sort', 'photo', array(
                    'changeUrl' => $changeUrl, 'sortParamName' => $sortParamName, 'selectedSorting' => $selectedSorting)); ?>
        </p>
    </div>
    <div class="xg_module_body">
        <?php
        } ?>
        <ul class="clist">
            <?php
            $i = -1;
            foreach ($photos as $photo) {
                $href = 'href="' . xnhtmlentities($this->_buildUrl('photo', 'listForContributor') . '?screenName=' . $photo->contributorName) . '"';
                $i++; ?>
                <%= $i>0 && $i % $numColumns == 0 ? '</ul><ul class="clist">' : '' %>
                <li>
                    <span class="thumb"><?php $this->renderPartial('fragment_thumbnailProper', 'photo', array('photo' => $photo, 'thumbWidth' => $thumbSize, 'thumbHeight' => $thumbSize, 'context' => $context, 'albumId' => $albumId)); ?></span>
                    <%= $photo->contributorName == $this->_user->screenName ? xg_html('BY_ME', $href) : xg_html('BY_X', '<a ' . $href . '>' . xnhtmlentities(Photo_FullNameHelper::fullName($photo->contributorName)) . '</a>') %>
                </li>
            <?php
            } ?>
        </ul>
        <?php
        if ($this->page && $this->numPages && ! $this->isSortRandom) {
            $this->renderPartial('fragment_pagination', 'photo', array(
                    'targetUrl' => $paginationUrl, 'pageParamName' => 'page', 'curPage' => $this->page, 'numPages' => $this->numPages));
        } ?>
    <?php if ($supressHead == false) { ?>
    </div>
    <?php } ?>
    <?php
    if ($feedUrl && (Photo_PrivacyHelper::getPrivacyType() == 'public')) {
        Photo_HtmlHelper::outputFeedAutoDiscoveryLink($feedUrl, $feedTitle); ?>
        <div class="xg_module_foot">
            <?php
            if ($addPhotos) { ?>
                <p class="right"><a class="desc add" <%= XG_JoinPromptHelper::promptToJoin($this->_buildUrl('photo', XG_MediaUploaderHelper::action())) %>><%= xg_text('ADD_PHOTOS') %></a></p>
            <?php
            }
            ?>
        </div>
    <?php
    } ?>