<?php
/**
 * @param changeUrl  The url to use when the page has been changed. May contain parameters
 * @param sortParamName  The name of the sort parameter; if not given, then the url in the pagination won't include a sort parameter
 * @param selectedSorting  The currently selected sorting, one of the items returned by Video_VideoHelper::getKnownSortingOrders()
 * @param pageParamName  The name of the curPage parameter
 * @param videos  Array of Video objects
 * @param curPage  The current page to show
 * @param numPages  The total number of pages
 * @param title
 * @param feedUrl  Optional url for an appropriate rss feed
 * @param feedTitle
 * @param feedLabel
 * @param editable
 * @param deleteUrl
 * @param deleteText
 * @param deleteConfirmQuestion
 */
$selectedSortingValue = null;
if ($sortParamName) {
    foreach (Video_VideoHelper::getKnownSortingOrders() as $optionValue => $optionData) {
        if ($selectedSorting['name'] == $optionData['name']) {
            $selectedSortingValue = $optionValue;
        }
    }
}
if ($selectedSortingValue) {
    $paginationUrl = Video_HtmlHelper::addParamToUrl($changeUrl, $sortParamName, $selectedSortingValue);
} else {
    $paginationUrl = $changeUrl;
} ?>
<div class="xg_module">
    <div class="xg_module_head notitle"></div>
    <div class="xg_module_body">
<?php
if ($editable) { ?>
        <p class="left"><button <%= XG_JoinPromptHelper::promptToJoinButton($this->_buildUrl('video', XG_MediaUploaderHelper::action())) %>><strong><%= xg_html('ADD_A_VIDEO') %></strong></button></p>
<?php
} ?>
        <p class="right"><?php $this->renderPartial('fragment_sort', 'video', array('changeUrl' => $changeUrl, 'sortParamName' => $sortParamName, 'selectedSorting' => $selectedSorting)); ?></p>
    </div>
    <div class="xg_module_body">
        <?php
        foreach($videos as $video) {
            $this->renderPartial('fragment_thumbnail', 'video', array('video' => $video, 'editable' => $editable, 'deleteUrl' => $deleteUrl, 'deleteText' => $deleteText, 'deleteConfirmQuestion' => $deleteConfirmQuestion));
        }
        if ($this->page && $this->numPages && ! $this->isSortRandom) {
            $this->renderPartial('fragment_pagination', 'video', array(
                    'targetUrl' => $paginationUrl, 'pageParamName' => 'page', 'curPage' => $this->page, 'numPages' => $this->numPages));
        } ?>
    </div>
<?php
if ($feedUrl && (Video_PrivacyHelper::getPrivacyType() == 'public')) {
    Video_HtmlHelper::outputFeedAutoDiscoveryLink($feedUrl, $feedTitle); ?>
    <div class="xg_module_foot">
        <p class="left"><a class="desc rss" href="<%= xnhtmlentities($feedUrl) %>"><%= xg_html('RSS') %></a></p>
        <?php
        if ($showAddVideoFooterLink) { ?>
            <p class="right"><a class="desc add" <%= XG_JoinPromptHelper::promptToJoin($this->_buildUrl('video', XG_MediaUploaderHelper::action())) %>><%= xg_html('ADD_A_VIDEO') %></a></p>
        <?php
        } ?>
    </div>
<?php
} ?>
</div>
