<?php
XG_App::includeFileOnce('/lib/XG_Cache.php');
$id = 'feed-' . md5($feedUrl . ',' . $itemCount . ',' . $showDescriptions . ',' . $maxEmbedWidth);
if (! XG_Cache::outputCacheStart($id, 60 * Feed_EmbedController::CACHE_MAX_MINUTES)) {
    list(, $moduleBodyAndFooterProper) = $this->_widget->capture('embed', 'moduleBodyAndFooterProper', array($feedUrl, $itemCount, $showDescriptions, $maxEmbedWidth));
    $moduleBodyAndFooterProper = trim($moduleBodyAndFooterProper);
    if (! $feedUrl) { ?>
        <div class="xg_module_foot">
            <ul>
                <li class="left"><a href="#" class='xj_add_rss desc add'><%= xg_html('ADD_RSS') %></a></li>
            </ul>
        </div>
    <?php
    } elseif (($itemCount == 0)  && XG_SecurityHelper::userIsAdmin()) { ?>
        <div class="xg_module_foot">
            <ul>
                <li class="left"><a href="#" class="xj_add_rss desc add"><%= xg_html('ADD_RSS') %></a></li>
            </ul>
        </div>
    <?php
    } elseif ($itemCount == 0) {
    } elseif (! $moduleBodyAndFooterProper && XG_SecurityHelper::userIsAdmin()) { ?>
        <div class="xg_module_body">
            <p class="last-child"><%= xg_html('PROBLEM_OCCURRED_RETRIEVING_FEED') %></p>
        </div>
    <?php
    } else {
        echo $moduleBodyAndFooterProper;
    }
    XG_Cache::outputCacheEnd($id);
}
