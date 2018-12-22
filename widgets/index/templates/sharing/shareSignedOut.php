<?php xg_header(null, $this->pageTitle) ?>
<?php XG_App::ningLoaderRequire('xg.index.invitation.pageLayout'); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_2col first-child" style="margin-left:235px;">
			<%= xg_headline($this->pageTitle)%>
            <div class="xg_module">
                <div class="xg_module_body pad">
                    <div class="share_preview">
                        <?php
                        if ($this->itemInfo['display_thumb']) { ?>
                            <div class="share_thumbnail"><img src="<%= $this->itemInfo['display_thumb'] %>" /></div>
                        <?php
                        } ?>
                        <div class="share_description"><%= $this->itemInfo['description'] %></div>
                    </div>
                </div>
                <div class="xg_module_body pad">
                    <?php
                        $type = xnhtmlentities(mb_strtolower($this->itemInfo['share_type']));
                    ?>
                    <h3><%= xg_html('YOU_CAN_SHARE_TYPE_TWO_WAYS', $type) %></h3>
                    <ol class="share">
                        <li>
                            <%= xg_html('SHARE_THIS_LINK') %><br />
                            <input onfocus="this.select();" type="text" class="textfield" size="50" value="<%= $this->itemInfo['share_url'] %>"/>
                        </li>
                        <li class="last-child">
                            <%= xg_html('SEND_WITH_EMAIL_PROGRAM') %><br />
                            <big><a href="<%= xnhtmlentities($this->mailToLink) %>"><%= xg_html('EMAIL_THIS') %></a></big>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
<?php xg_footer() ?>