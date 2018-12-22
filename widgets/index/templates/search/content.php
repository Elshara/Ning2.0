<?php
/**
 * This partial template displays the search results for some retrieved content
 *
 * @param $content an array of content objects to display
 */ ?>
<div class="xg_module_body">
    <form id="xg_search_form" method="get" action="<%= xnhtmlentities($this->_buildUrl('search','search')) %>">
        <input type="text" style="margin:0 3px 0 7px; padding:3px" class="textfield large left" name="q" value="<%= xnhtmlentities($this->term) %>" /> <big><input type="submit" class="button" value="<%= xg_html('SEARCH_AGAIN') %>" /></big>
    </form>
</div>
<div class="xg_module_body pad">
    <div class="search_results">
<?php if (count($content)) {
    foreach($content as $c) {
        $typeName = isset($this->contentTypeNameMap[$c->type]) ? $this->contentTypeNameMap[$c->type] : $c->type;
        $profile = $this->profiles[$c->contributorName];

        if ($c->type == 'User') { ?>
            <dl class="result vcard">
                <dt><a href="<%= xnhtmlentities(User::quickProfileUrl($profile->screenName)) %>" class="fn url"><img class="photo" src="<%= xnhtmlentities(XG_UserHelper::getThumbnailUrl($profile,48,48)) %>" alt="" /><%= xnhtmlentities(xg_username($profile)) %></a></dt>
                <?php
                $link = xg_add_as_friend_link($profile->screenName, $this->friendStatuses[$profile->screenName]);
                if ($link) { echo '<dd class="relationship">' . $link . '</dd>'; } ?>
            </dl>
        <?php
        } else {
        $timestamp = strtotime($c->createdDate);
        $addedByLine = xg_html('ADDED_BY_X_AT_X', 'href="' . xnhtmlentities(User::quickProfileUrl($profile->screenName)).'"', xnhtmlentities(xg_username($profile)), xg_date(xg_text('G_IA'), $timestamp), xg_date(xg_text('F_JS_Y'),$timestamp));
        if ($this->groups[$c->my->groupId]) {
            $groupLink = '<a href="/xn/detail/' . $c->my->groupId . '">' . xnhtmlentities($this->groups[$c->my->groupId]->title) . '</a>';
            $addedByLine = xg_html('ADDED_BY_USER_TO_GROUP_AT_DATE', 'href="'.xnhtmlentities(User::quickProfileUrl($profile->screenName)).'"', xnhtmlentities(xg_username($profile)), $groupLink, xg_date(xg_text('G_IA'), $timestamp), xg_date(xg_text('F_JS_Y'),$timestamp));
        }
        if ($c->type == 'BlogPost') {
            $summary = BlogPost::summarize($c, 200);
        } elseif ($c->type == 'Note') {
            $summary = xg_excerpt(strip_tags($c->description), 200);
        } else {
            $summary = ((mb_strlen($c->description) > 200) ? (xnhtmlentities(mb_substr($c->description, 200)) . '&hellip;') : xnhtmlentities($c->description));
        }
        $title = mb_strlen($c->title) ? $c->title : '[ ' . xg_text('CLICK_TO_VIEW') . ' ]';
        if ($c->type == 'Comment') {
            // Some special handling for chatters
            if ($c->my->attachedToType == 'User') {
                $prefix = xg_html('COMMENT_FOR');
                $linkText = xg_username(XG_Cache::profiles($c->my->attachedToAuthor));
            } else {
                $prefix = xg_html('COMMENT_ON');
                $linkText = isset($this->contentTypeNameMap[$c->my->attachedToType]) ? $this->contentTypeNameMap[$c->my->attachedToType] : $c->my->attachedToType;
                if (mb_strlen($c->my->attachedToTitle) > 0) {
                    $linkText .= " '" . xg_excerpt($c->my->attachedToTitle, 80) . "'";
                }
            }
            ?>
<dl class="result">
    <dt><%= $prefix %> <a href="/xn/detail/<%= xnhtmlentities($c->id) %>"><%= xnhtmlentities($linkText) %></a></dt>
    <dd><em><%= $summary %></em></dd>
    <dd><small><%= $addedByLine %></small></dd>
</dl>
        <?php } elseif ($c->type == 'Photo') {
            $photoUrl = $c->fileUrl('data');
            $photoUrl .= ((mb_strpos($photoUrl, '?') === false) ? '?' : '&');
            $photoUrl .= 'width=95';
            ?>
<dl class="result">
    <dt><%= xnhtmlentities($typeName) %>: <a href="/xn/detail/<%= xnhtmlentities($c->id) %>"><%= xnhtmlentities($title) %></a></dt>
    <dd><img src="<%= xnhtmlentities($photoUrl) %>" class="right" alt="<%= xnhtmlentities($title) %>" /><%= $summary %></dd>
    <dd><small><%= $addedByLine %></small></dd>
</dl>
        <?php } elseif ($c->type == 'Page') {
            $summary = mb_substr(strip_tags($c->description), 0, 200);
        ?>
<dl class="result">
    <dt><%= xnhtmlentities($typeName) %>: <a href="/xn/detail/<%= xnhtmlentities($c->id) %>"><%= xnhtmlentities($title) %></a></dt>
    <dd><%= $summary %></dd>
    <dd><small><%= $addedByLine %></small></dd>
</dl>
        <?php } else { ?>
<dl class="result">
    <dt><%= xnhtmlentities($typeName) %>: <a href="/xn/detail/<%= xnhtmlentities($c->id) %>"><%= xnhtmlentities($title) %></a></dt>
    <dd><%= $summary %></dd>
    <dd><small><%= $addedByLine %></small></dd>
</dl>
        <?php } /* different displays for each type */
        } /* user or not */
    } /* each content object */
    } /* content to display? */
    else { ?>
        <h3><%= xg_html('WE_COULD_NOT_FIND_ANYTHING', xnhtmlentities($this->term)) %></h3>

    <?php } ?>
    </div>
</div>