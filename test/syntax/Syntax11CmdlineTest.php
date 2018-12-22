<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

/**
 * Syntax tests continued.
 */
class Syntax11CmdlineTest extends CmdlineTestCase {

    public function testDoNotUseCssExpressions() {
        // http://developer.yahoo.com/performance/rules.html#css_expressions  [Jon Aquino 2008-01-24]
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $existingLinesWithExpressions = <<<JSON
["* html #xg #xg_body .image_picker li img { width: expression(this.width > 200 ? 200: true); }","* html #xg #xg_body .xg_1col img { width: expression(this.width > 206 ? 206: true); height:expression(this.width > 206 ? 'auto': true); }","* html #xg #xg_body .xg_1col .pad img { width: expression(this.width > 190 ? 190: true); height:expression(this.width > 190 ? 'auto': true); }","* html #xg #xg_body .xg_1col .comment img { width: expression(this.width > 119 ? 119: true); height:expression(this.width > 119 ? 'auto': true); }","* html #xg_body .xg_2col img { width: expression(this.width > 441 ? 441: true); height:expression(this.width > 441 ? 'auto': true); }","* html #xg_body .xg_2col .pad img { width: expression(this.width > 425 ? 425: true); height:expression(this.width > 425 ? 'auto': true); }","* html #xg_body .xg_2col .comment img { width: expression(this.width > 354 ? 354 : true); height:expression(this.width > 354 ? 'auto': true); }","* html .xg_3col img { width: expression(this.width > 676 ? 676: true); height:expression(this.width > 676 ? 'auto': true); }","* html .xg_3col img .pad { width: expression(this.width > 660 ? 660: true); height:expression(this.width > 660 ? 'auto': true); }","* html .xg_3col img .comment { width: expression(this.width > 589 ? 589: true); height:expression(this.width > 589 ? 'auto': true); }","top:expression(eval(documentElement.scrollTop));","top:expression(eval(documentElement.scrollTop+((documentElement.clientHeight-this.clientHeight)\/2)));","* html #xg #xg_body .image_picker li img { width: expression(this.width > 200 ? 200: true); }","* html .xg_1col img { width: expression(this.width > 159 ? 159: true); height:expression(this.width > 159 ? 'auto': true); }","* html .xg_3col .xg_1col img { width: expression(this.width > 220 ? 220: true); height:expression(this.width > 220 ? 'auto': true); }","* html .xg_1col .pad img { width: expression(this.width > 143 ? 143: true); height:expression(this.width > 143 ? 'auto': true); }","* html .xg_3col .xg_1col .pad img { width: expression(this.width > 200 ? 200: true); height:expression(this.width > 200 ? 'auto': true); }","* html .xg_1col .comment img { width: expression(this.width > 62 ? 62: true); height:expression(this.width > 62 ? 'auto': true); }","* html .xg_3col .xg_1col .comment img { width: expression(this.width > 119 ? 119: true); height:expression(this.width > 119 ? 'auto': true); }","* html .xg_2col img { width: expression(this.width > 441 ? 441: true); height:expression(this.width > 441 ? 'auto': true); }","* html .xg_3col .xg_2col img { width: expression(this.width > 502 ? 502: true); height:expression(this.width > 502 ? 'auto': true); }","* html .xg_2col .pad img { width: expression(this.width > 425 ? 425: true); height:expression(this.width > 425 ? 'auto': true); }","* html .xg_3col .xg_2col .pad img { width: expression(this.width > 482 ? 482: true); height:expression(this.width > 482 ? 'auto': true); }","* html .xg_2col .comment img { width: expression(this.width > 354 ? 354 : true); height:expression(this.width > 354 ? 'auto': true); }","* html .xg_3col .xg_2col .comment img { width: expression(this.width > 401 ? 401 : true); height:expression(this.width > 401 ? 'auto': true); }","* html .xg_3col img { width: expression(this.width > 723 ? 723: true); height:expression(this.width > 723 ? 'auto': true); }","* html .xg_3col img .pad { width: expression(this.width > 707 ? 707: true); height:expression(this.width > 707 ? 'auto': true); }","* html .xg_3col img .comment { width: expression(this.width > 636 ? 636: true); height:expression(this.width > 636 ? 'auto': true); }","top:expression(eval(documentElement.scrollTop));","top:expression(eval(documentElement.scrollTop+((documentElement.clientHeight-this.clientHeight)\/2)));"]
JSON;
        $existingLinesWithExpressions = $json->decode($existingLinesWithExpressions);
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.css') as $file) {
            $contents = self::getFileContent($file);
            if (strpos($contents, 'expression') === false) { continue; }
            $lineNumber = 0;
            foreach (explode("\n", $contents) as $line) {
                $lineNumber++;
                // Skip existing uses [Jon Aquino 2008-01-24]
                if (strpos($line, 'expression') !== false) {
                    $expressionCount++;
                    if (in_array(trim($line), $existingLinesWithExpressions)) { continue; }
                    if (strpos($line, '* html') !== false) { continue; }
                    if (strpos($line, 'width: expression(this.width > 200 ? 200: true);') !== false) { continue; }
                    if (strpos($line, 'height:expression(this.height > 200 ? 200: true);') !== false) { continue; }
                    if (strpos($line, '_height:expression(this.height > 1000 ? 1000: true);') !== false) { continue; }
                    if (strpos($file, 'index/css/common.css') !== FALSE && preg_match('/width:\s*expression\(this\.width/', $line) > 0) { continue; }
                    $this->fail($line . ' ' . $file . ' ' . $lineNumber . ' ***');
                }
            }
        }
    }

    public function testUseCamelCaseInsteadOfUnderscoresForVariableNames() {
        $existingViolations = array('$in_base', '$out_base', '$g_in', '$place_value', '$g_rem', '$encrypted_chunks', '$encrypted_chunk', '$decrypted_chunks', '$decrypted_chunk', '$pd_start', '$LOCAL_API_HOST_PORT', '$query_string', '$DOMAIN_SUFFIX', '$SECURITY_TOKEN', '$__displayHtml', '$__templateFile', '$__k', '$__v', '$__content', '$response_body_len', '$response_time', '$CURRENT_URL', '$APPCORE_IP', '$not_anchor', '$not_http', '$xg_max_embed_width', '$no_ws_ctl', '$obs_char', '$obs_text', '$obs_qp', '$quoted_pair', '$obs_fws', '$quoted_string', '$obs_local_part', '$obs_domain', '$dot_atom_text', '$dot_atom', '$domain_literal', '$local_part', '$addr_spec', '$api_client', '$api_key', '$fb_params', '$session_key', '$auth_token', '$params_array', '$prefix_len', '$expected_sig', '$start_time', '$end_time', '$rsvp_status', '$image_1', '$image_1_link', '$image_2', '$image_2_link', '$image_3', '$image_3_link', '$image_4', '$image_4_link', '$to_ids', '$no_email', '$subj_id', '$post_params', '$post_string', '$api_error_descriptions', '$profile_field_array', '$w_user', '$w_content', '$w_contentType', '$w_userContributorName', '$data_1', '$key_1', '$data_2', '$key_2', '$cacheId_2', '$cacheId_1', '$player_url', '$app_url', '$query_2', '$delta_bytes', '$xn_query', '$res_2', '$query_3', '$res_3', '$query_4', '$res_4', '$strip_htmltags', '$feed_url', '$num_options', '$visibility_choices', '$excludeFromPublicSearch_choices', '$no_activity', '$activity_off', '$partial_line', '$CATEGORIES_PER_PAGE', '$TOPICS_PER_CATEGORY', '$CATEGORIES_TO_SHOW', '$membersCanAddTopics_choices', '$membersCanReply_choices', '$commentsClosed_choices', '$ONE_HOUR', '$groupPrivacy_choices', '$allowInvitations_choices', '$allowInvitationRequests_choices', '$deleted_choices', '$status_choices', '$welcomed_choices', '$approved_choices', '$kind_choices', '$emailActivityPref_choices', '$emailModeratedPref_choices', '$emailApprovalPref_choices', '$emailCommentApprovalPref_choices', '$emailInviteeJoinPref_choices', '$emailFriendRequestPref_choices', '$emailNewMessagePref_choices', '$emailAllFriendsPref_choices', '$emailSiteBroadcastPref_choices', '$emailGroupBroadcastPref_choices', '$isFollowing_choices', '$autoFollowOnReplyPref_choices', '$emailNeverPref_choices', '$activityNewContent_choices', '$activityNewComment_choices', '$activityNewConnection_choices', '$activityProfileUpdate_choices', '$defaultVisibility_choices', '$addCommentPermission_choices', '$blogPingPermission_choices', '$isAdmin_choices', '$syncdWithProfile_choices', '$jstrk_on', '$jstrk_all', '$isSource_choices', '$explicit_choices', '$playlist_url', '$placeholder_url', '$logo_link', '$display_add_links', '$display_contributor', '$display_feature_btn', '$display_opacity', '$stripped_filename', '$get_allowed', '$hidden_choices', '$fullsize_url', '$slideshow_width', '$slideshow_height', '$slideshowplayer_url', '$config_url', '$signup_favorize_link', '$app_name', '$brand_url', '$internal_referrer', '$rows_numbers', '$max_chatters', '$mood_choices', '$publishStatus_choices', '$publishWhen_choices', '$allowComments_choices', '$conversionStatus_choices', '$signup_share_target');
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, '/test') !== false) { continue; }
            if (strpos($file, 'W_WIDGETAPP_6_12_STUB.php') !== false) { continue; }
            if (strpos($file, '/lib/ext/facebook/facebook.php') !== false) { continue; }
            if (strpos($file, '/lib/ext/facebook/facebookapi_php5_restlib.php') !== false) { continue; }
            if (strpos($file, 'buildSpamWords.php') !== false) { continue; }
            $contents = self::getFileContent($file);
            if (strpos($file, 'playlist/fragment_playerProper.php') !== false) { continue; }
            preg_match_all('@\$.\w+@ui', $contents, $matches);
            $violations = array();
            foreach (array_unique($matches[0]) as $violation) {
                if (in_array($violation, $existingViolations)) { continue; }
                if (strpos($violation, '_choices') !== false) { continue; }
                if ($violation == '$id_html') { continue; }
                if ($violation == '$id_src') { continue; }
                if ($violation == '$id_header') { continue; }
                if ($violation == '$my_callback') { continue; }
                if ($violation == '$id_text') { continue; }
                if ($violation == '$__displayMode') { continue; }
                if ($violation == '$__templateDir') { continue; }
                if ($violation == '$__subDir') { continue; }
                if ($violation == '$NEW_GRID_PATH') { continue; }
                if ($violation == '$EXTERNAL_PORT') { continue; }
                if ($violation == '$EXTERNAL_SSL_PORT') { continue; }
                if ($violation == '$OLD_LEFT_COLUMN') { continue; }
                if ($violation == '$OLD_CENTER_COLUMN') { continue; }
                if ($violation == '$ADD_FEATURES_SORT_ORDER') { continue; }
                if ($violation == '$PAGE_SIZE') { continue; }
                if ($violation == '$RSVP_STATUSES') { continue; }
                if ($violation == '$has_end') { continue; }
                if ($violation == '$show_my') { continue; }
                if ($violation == '$show_user') { continue; }
                if ($violation == '$might_attend') { continue; }
                if ($violation == '$not_attending') { continue; }
                if ($violation == '$not_rsvped') { continue; }
                if ($violation == '$xn_auth') { continue; }
                if ($violation == '$share_raw_type') { continue; }
                if ($violation == '$share_raw_description') { continue; }
                if ($violation == '$share_content_author') { continue; }
                if (strpos(str_replace('$_', '', $violation), '_') !== false) { $violations[] = $violation; }
            }
            $this->assertFalse($violations, implode(', ', $violations) . ' in ' . $line . ' ' . $file . ' ' . $lineNumber . ' ***');
        }
    }

    public function testUseVideoPlayerConstants() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, '/test') !== false) { continue; }
            if (strpos($file, 'XG_Message/new/invitation.php') !== false) { continue; }
            $contents = self::getFileContent($file);
            $contents = preg_replace('@handleSortingAndPagination.null, 24.| == 24|360 =>|pageSize = 24|\+= 360|NUM_THUMBS_GRIDVIEW = 24|%28|24 hours|"24"|"28"|24 \*|\* 24|-28|/28/@', '', $contents);
            preg_match_all('@^.*\b(448|336|360|364)\b[^\]].*$@um', $contents, $matches);
            foreach ($matches[0] as $match) {
                if (strpos($match, 'const VIDEO_') !== false) { continue; }
                if (strpos($match, 'const EXTERNAL_VIDEO_') !== false) { continue; }
                if (strpos($match, 'widgets.turner.com') !== false) { continue; }
                $this->fail($match . ' ' . $file . ' ***');
            }
        }
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, '/test') !== false) { continue; }
            $contents = self::getFileContent($file);
            $this->assertTrue(strpos($contents, 'Video_VideoHelper::VIDEO_WIDTH') === false, 'Video_VideoHelper::VIDEO_WIDTH in ' . $file);
            $this->assertTrue(strpos($contents, 'Video_VideoHelper::VIDEO_HEIGHT') === false, 'Video_VideoHelper::VIDEO_HEIGHT in ' . $file);
            $this->assertTrue(strpos($contents, 'Video_VideoHelper::VIDEO_PLAYER_CONTROLS_HEIGHT_INTERNAL') === false, 'Video_VideoHelper::VIDEO_PLAYER_CONTROLS_HEIGHT_INTERNAL in ' . $file);
            $this->assertTrue(strpos($contents, 'Video_VideoHelper::VIDEO_PLAYER_CONTROLS_HEIGHT_EXTERNAL') === false, 'Video_VideoHelper::VIDEO_PLAYER_CONTROLS_HEIGHT_EXTERNAL in ' . $file);
        }
    }

    public function testNoEntropyc() {
        // Remove references to entropyc.ning.com [Jon Aquino 2008-01-26]
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.*') as $file) {
            if (strpos($file, '/test') !== false) { continue; }
            $contents = self::getFileContent($file);
            $this->assertTrue(strpos($contents, 'entropyc') === false, $file);
        }
    }

    public function testNoLinkindex() {
        // linkindex is a Firebug attribute [Jon Aquino 2008-02-01]
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.*') as $file) {
            if (strpos($file, '/test') !== false) { continue; }
            $contents = self::getFileContent($file);
            $this->assertTrue(strpos($contents, 'linkindex') === false, $file);
        }
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
