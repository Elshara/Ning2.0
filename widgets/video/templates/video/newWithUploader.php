<?php xg_header(W_Cache::current('W_Widget')->dir, $title = xg_text('ADD_VIDEOS')); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<?php XG_PageHelper::subMenu(Video_HtmlHelper::subMenu($this->_widget,'none')) ?>
			<%= xg_headline($title)%>
            <div class="xg_colgroup">
                <div class="xg_3col first-child">
                    <div class="xg_module">
                        <div class="xg_module_body pad">
                            <h3><%= xg_html('UPLOAD_VIDEOS_FROM') %></h3>
                            <?php
                            W_Cache::getWidget('main')->dispatch('mediauploader', 'container', array(array(
                                    'acceptedFormatsMessageHtml' => xg_html('EACH_VIDEO_MAY_BE_100MB'),
                                    'helpMessageHtml' => xg_html('HAVING_PROBLEMS_WITH_VIDEO_UPLOADER', 'href="' . xnhtmlentities($this->_widget->buildUrl('video', 'new')) . '"'),
                                    'javaRequiredMessageHtml' => xg_html('VIDEO_UPLOADER_REQUIRES_JAVA', 'href="http://java.com"', 'href="' . xnhtmlentities($this->_widget->buildUrl('video', 'new')) . '"')))); ?>
                        </div>
                    </div>
                </div>
                <div class="xg_3col first-child">
                    <div class="xg_module">
                        <div class="xg_module_body pad">
                            <div class="block left">
                                <h4><%= xg_html('ADD_VIDEOS_FROM_YOUTUBE') %></h4>
                                <span id="youtubelogo"><a href="<%= xnhtmlentities($this->_buildUrl('video', 'addEmbed')) %>"><img src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/youtube.png'))) %>" width="90" height="35" alt="<%= xg_html('YOUTUBE') %>" /></a></span>
                                <span id="googlelogo"><a href="<%= xnhtmlentities($this->_buildUrl('video', 'addEmbed')) %>"><img src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/google.png'))) %>" width="90" height="35" alt="<%= xg_html('GOOGLE') %>" /></a></span>
                                <p><%= xg_html('GRAB_HTML_EMBED') %></p>
                                <p class="left"><strong><a href="<%= xnhtmlentities($this->_buildUrl('video', 'addEmbed')) %>"><%= xg_html('ADD_VIDEO') %></a></strong></p>
                            </div>
                            <div class="block right">
                                <h4><%= xg_html('ADD_VIDEOS_BY_PHONE_OR') %></h4>
                                <img class="left" alt="" src="<%= xg_cdn(W_Cache::getWidget('photo')->buildResourceUrl('gfx/phoneadd_large.gif')) %>" width="32" height="32" />
                                <p style="margin-left:40px"><%= xg_html('ADD_VIDEOS_OR_PHOTOS_TO_APPNAME', xnhtmlentities(XN_Application::load()->name)) %></p>
                                <p class="left"><strong><a href="<%= xnhtmlentities($this->_buildUrl('video', 'addByPhone')) %>"><%= xg_html('MORE_INFORMATION') %></a></strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="xg_1col last-child">
            <?php xg_sidebar($this); ?>
        </div>
    </div>
</div>
<?php
W_Cache::getWidget('main')->dispatch('mediauploader', 'footer', array(array(
        'type' => 'videos',
        'uploadUrl' => $this->_widget->buildUrl('video', 'createWithUploader'),
        'successUrl' => $this->_widget->buildUrl('video', 'listForContributor', array('screenName' => XN_Profile::current()->screenName, 'uploaded' => 1))))); ?>
<?php xg_footer(); ?>
