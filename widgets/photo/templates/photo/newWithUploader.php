<?php xg_header(W_Cache::current('W_Widget')->dir, $title = xg_text('ADD_PHOTOS')); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<?php XG_PageHelper::subMenu(Photo_HtmlHelper::subMenu($this->_widget, 'none')) ?>
			<%= xg_headline($title)%>
            <div class="xg_colgroup">
                <div class="xg_3col first-child">
                    <div class="xg_module">
                        <div class="xg_module_body pad">
                            <h3><%= xg_html('UPLOAD_PHOTOS_FROM_YOUR_COMPUTER') %></h3>
                            <?php
                            W_Cache::getWidget('main')->dispatch('mediauploader', 'container', array(array(
                                    'acceptedFormatsMessageHtml' => xg_html('EACH_PHOTO_MAY_BE_UP_TO_10MB'),
                                    'helpMessageHtml' => xg_html('HAVING_PROBLEMS_WITH_PHOTO_UPLOADER', 'href="' . xnhtmlentities($this->_widget->buildUrl('photo', 'new')) . '"'),
                                    'javaRequiredMessageHtml' => xg_html('PHOTO_UPLOADER_REQUIRES_JAVA', 'href="http://java.com"', 'href="' . xnhtmlentities($this->_widget->buildUrl('photo', 'new')) . '"')))); ?>
                        </div>
                    </div>
                </div>
                <div class="xg_3col first-child">
                    <div class="xg_module">
                        <div class="xg_module_body pad">
                            <?php
                            $showFlickrLink = $this->flickrEnabled == 'Y' || (XG_SecurityHelper::userIsAdmin() && $this->showFlickrToOwner == 'Y');
                            if ($showFlickrLink) { ?>
                                <div class="block left">
                                    <h4><%= xg_html('ADD_PHOTOS_FROM_FLICKR') %></h4>
                                    <span id="flickrlogo"><a href="<%= xnhtmlentities($this->_buildUrl('photo', 'flickr')) %>"><img src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/flickr/flickr.png'))) %>" width="100" height="28" alt="<%= xg_html('FLICKR') %>" /></a></span>
                                    <p><%= xg_html('ADD_PHOTOS_FROM_YOUR_FLICKR') %></p>
                                    <p class="left"><strong><a href="<%= xnhtmlentities($this->_buildUrl('photo', 'flickr')) %>"><%= xg_html('ADD_PHOTOS') %></a></strong></p>
                                </div>
                            <?php
                            } ?>
                            <div class="block <%= $showFlickrLink ? 'right' : 'left' %>">
                                <h4><%= xg_html('ADD_PHOTOS_BY_PHONE_OR_EMAIL') %></h4>
                                <img class="left" alt="" src="<%= xg_cdn($this->_widget->buildResourceUrl('gfx/phoneadd_large.gif')) %>" width="32" height="32" />
                                <p style="margin-left:40px"><%= xg_html('ADD_PHOTOS_OR_VIDEOS_TO_APPNAME', xnhtmlentities(XN_Application::load()->name)) %></p>
                                <p class="left"><strong><a href="<%= xnhtmlentities($this->_buildUrl('photo', 'addByPhone')) %>"><%= xg_html('MORE_INFORMATION') %></a></strong></p>
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
        'type' => 'photos',
        'uploadUrl' => $this->_widget->buildUrl('photo', 'createWithUploader'),
        'successUrl' => $this->_widget->buildUrl('photo', 'listForContributor', array('screenName' => XN_Profile::current()->screenName, 'uploaded' => 1))))); ?>
<?php xg_footer(); ?>
