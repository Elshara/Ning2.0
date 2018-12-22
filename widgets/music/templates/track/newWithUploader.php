<?php xg_header(W_Cache::current('W_Widget')->dir, $title = xg_text('ADD_MUSIC')); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<%= xg_headline($title)%>
            <div class="xg_colgroup">
                <div class="xg_3col first-child">
                    <div class="xg_module">
                        <div class="xg_module_body pad">
                            <h3><%= xg_html('UPLOAD_MUSIC_FROM_YOUR_COMPUTER') %></h3>
                            <?php
                            W_Cache::getWidget('main')->dispatch('mediauploader', 'container', array(array(
                                    'acceptedFormatsMessageHtml' => xg_html('YOU_CAN_ADD_100_SONGS'),
                                    'helpMessageHtml' => xg_html('HAVING_PROBLEMS_WITH_MUSIC_UPLOADER', 'href="' . xnhtmlentities($this->_widget->buildUrl('track', 'new', array('isMainPlaylist'=>$this->addToMainPlaylist, 'playlistId' => $this->playlistId))) . '"'),
                                    'javaRequiredMessageHtml' => xg_html('MUSIC_UPLOADER_REQUIRES_JAVA', 'href="http://java.com"', 'href="' . xnhtmlentities($this->_widget->buildUrl('track', 'new', array('isMainPlaylist'=>$this->addToMainPlaylist, 'playlistId' => $this->playlistId))) . '"')))); ?>
                        </div>
                    </div>
                </div>
                <div class="xg_3col first-child">
                    <div class="xg_module">
                        <div class="xg_module_body pad">
                            <div class="block left">
                                <h4><%= xg_html('ADD_MUSIC_FROM_WEBSITE') %></h4>
                                <img class="left" alt="" src="<%= xg_cdn($this->_widget->buildResourceUrl('gfx/add.gif')) %>" width="32" height="32" />
                                <p style="margin-left:40px"><%= xg_html('ADD_MUSIC_FROM_ANY_WEBSITE', xnhtmlentities(XN_Application::load()->name)) %></p>
                                <p class="left"><strong><a href="<%= xnhtmlentities($this->_buildUrl('track', 'newLink', array('isMainPlaylist'=>$this->addToMainPlaylist, 'playlistId' => $this->playlistId))) %>"><%= xg_html('MORE_INFORMATION') %></a></strong></p>
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
W_Cache::getWidget('main')->dispatch('mediauploader', 'footer', array($this->musicUploaderArgs)); ?>
<?php xg_footer(); ?>
