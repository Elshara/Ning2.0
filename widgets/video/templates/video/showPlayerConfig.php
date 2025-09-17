<?php echo '<?xml version="1.0" encoding="UTF-8" ?>'; ?>
<config>
    <video_url><%= xg_xmlentities($this->videoAttachmentUrl) %></video_url>
    <frame_url><%= xg_xmlentities($this->previewFrameUrl) %></frame_url>
    <brand_format><%= xg_xmlentities(XG_EmbeddableHelper::getPlayerBrandFormat()) %></brand_format>
    <watermark_url><%= xg_xmlentities(XG_EmbeddableHelper::getPlayerLogoUrl()) %></watermark_url>
    <watermark_width><%= xg_xmlentities(XG_EmbeddableHelper::getPlayerLogoWidth()) %></watermark_width>
    <watermark_height><%= xg_xmlentities(XG_EmbeddableHelper::getPlayerLogoHeight()) %></watermark_height>
    <bg_image_url><%=  xg_xmlentities(XG_EmbeddableHelper::getBackgroundImageUrl()) %></bg_image_url>
    <app_name><%= xg_xmlentities(XN_Application::load()->name) %></app_name>
    <css><%= xg_xmlentities(('h1{font-family: '.XG_EmbeddableHelper::getNetworkNameFontFamily().';color:#ffffff;}')) %></css>
    <app_url><%= xg_xmlentities('http://' . $_SERVER['HTTP_HOST']) %></app_url>
    <video_size><%= xg_xmlentities($this->videoSizeInBytes) %></video_size>
    <video_id><%= $this->videoId %></video_id>
    <bgcolor><%=  xg_xmlentities(XG_EmbeddableHelper::getBackgroundColor()) %></bgcolor>
    <embed_code><%= xg_xmlentities($this->embedCode) %></embed_code>
    <l_play_again><%= xg_html('PLAY_AGAIN') %></l_play_again>
    <l_share><%= xg_html('SHARE') %></l_share>
    <l_embed><%= xg_html('EMBED') %></l_embed>
    <l_copy_to_clipboard><%= xg_html('COPY_TO_CLIPBOARD') %></l_copy_to_clipboard>
    <l_copied_to_clipboard><%= xg_html('COPIED_TO_CLIPBOARD') %></l_copied_to_clipboard>
    <l_see_video_on_network><%= xg_html('SEE_VIDEO_ON_NETWORK', XN_Application::load()->name) %></l_see_video_on_network>
    <l_get_embed_code><%= xg_html('GET_EMBED_CODE_SENTENCE_CASE') %></l_get_embed_code>
    <l_back><%= xg_html('BACK') %></l_back>
    <l_change_size><%= xg_html('CHANGE_SIZE') %></l_change_size>
    <l_fullscreen><%= xg_html('FULLSCREEN') %></l_fullscreen>
    <l_exit_fullscreen><%= xg_html('EXIT_FULLSCREEN') %></l_exit_fullscreen>
    <l_rewind><%= xg_html('REWIND') %></l_rewind>
    <l_copy_and_paste_link><%= xg_html('COPY_AND_PASTE_LINK') %></l_copy_and_paste_link>
</config>
