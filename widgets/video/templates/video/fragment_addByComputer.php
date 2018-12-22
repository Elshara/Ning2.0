<h4><%= xg_html('FROM_YOUR_COMPUTER')%></h4>
<img class="left" width="32" height="32" src="<%= xg_cdn('/xn_resources/widgets/video/gfx/add.gif') %>" alt=""/>
<p style="margin-left: 40px;"><%= xg_html('UPLOAD_A_VIDEO_FROM') %></p>
<p class="right"><strong><a <%= XG_JoinPromptHelper::promptToJoin($this->_buildUrl('video', XG_MediaUploaderHelper::action())) %>><%= xg_html('MORE_INFORMATION') %></a></strong></p>