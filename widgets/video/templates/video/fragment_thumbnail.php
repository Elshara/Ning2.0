<?php
/**
 * @param video
 * @param editable
 * @param deleteUrl
 * @param deleteText
 * @param deleteConfirmQuestion
 */ ?>
<div class="video">
    <p class="video_thumb">
        <?php $this->renderPartial('fragment_thumbnailProper', 'video', array('video' => $video, 'thumbWidth' => 150)); ?>
    </p>
    <div class="video_description">
        <?php
        if (! $editable) { ?>
            <h3><a href="<%= xnhtmlentities($this->_buildUrl('video', 'show', array('id' => $video->id))) %>"><%= xnhtmlentities($video->title) %></a></h3>
        <?php
        } else { ?>
            <?php XG_App::ningLoaderRequire('xg.shared.InPlaceEditor'); ?>
            <h3 dojoType="InPlaceEditor" _control="textInput" _controlAttributes="<%= xnhtmlentities('class="textfield h3" size="50" style="width:250px"') %>" _instruction="<%= xg_html('CLICK_TO_ADD_A_TITLE') %>" _maxLength="200" _setValueUrl="<%= xnhtmlentities($this->_buildUrl('video', 'setTitle', '?id=' . $video->id)) %>"><%= xnhtmlentities($video->title) %></h3>
        <?php
        } ?>
        <p><%= Video_HtmlHelper::averageRating($video->my->ratingAverage, $video->my->ratingCount) %></p>
        <?php
        $descriptionHtml = Video_HtmlHelper::excerpt($video->description, 100, $this->_buildUrl('video', 'show', '?id=' . $video->id), $excerpted);
        if (! $editable) { ?>
            <p><%= $descriptionHtml ? $descriptionHtml : '<em>' . xg_html('NO_DESCRIPTION') . '</em>' %></p>
        <?php
        } else {
            // Two reasons to specify _getValueUrl: (1) the description may be very long and thus excerpted, so we need to get the full text
            // (2) if excerpted, the description's HTML tags (if any) will not be present, so we need to get them  [Jon Aquino 2006-07-17]
            ?>
            <?php XG_App::ningLoaderRequire('xg.shared.InPlaceEditor'); ?>
            <p class="description" dojoType="InPlaceEditor" _controlAttributes="<%= xnhtmlentities('class="p" rows="3" cols="30" style="width:250px"') %>" _html="true" _instruction="<%= xg_html('CLICK_TO_ADD_A_DESCRIPTION') %>" _maxLength="4000"
                <?php
                if ($excerpted) { ?>
                    _getValueUrl="<%= xnhtmlentities($this->_buildUrl('video', 'getDescription', '?id=' . $video->id)) %>"
                <?php
                } ?>
                _setValueUrl="<%= xnhtmlentities($this->_buildUrl('video', 'setDescription', '?id=' . $video->id . '&maxEmbedWidth=260')) %>">
                <%= $descriptionHtml %>
            </p>
        <?php
        }
        $commentCounts = Comment::getCounts($video) ?>
        <p><small>
            <%= xg_html('ADDED_ON_DATE_BY_X', Video_HtmlHelper::prettyDate($video->createdDate), Video_HtmlHelper::linkedScreenName($video->contributorName, FALSE, FALSE)) %>
            <strong><a href="<%= $this->_buildUrl('video', 'show') . '?id=' . $video->id %>#comments"><%= str_replace(' ', '&nbsp;', xg_html('N_COMMENTS', $commentCounts['commentCount'])) %></a></strong>
        </small></p>
        <?php if ($deleteUrl) { ?>
            <p><small><a href="#null" dojoType="PostLink" _confirmTitle="<%= $deleteText %>" _confirmOkButtonText="<%= xg_html('OK') %>" _confirmQuestion="<%= $deleteConfirmQuestion %>" _url="<%= sprintf($deleteUrl, $video->id) %>" _reload="true"><%= $deleteText %></a></small></p>
        <?php } ?>
    </div>
</div>