<h4><%= xg_html('BY_PHONE_OR_EMAIL')%></h4>
<img class="left" width="32" height="32" src="<%= xg_cdn('/xn_resources/widgets/photo/gfx/phoneadd_large.gif') %>" alt=""/>
<p style="margin-left: 40px;"><%= xg_html('ADD_PHOTOS_OR_VIDEOS_TO_X', $appName) %></p>
<p class="right"><strong><a href="<%= xnhtmlentities($this->_buildUrl('video', 'addByPhone')) %>"><%= xg_html('MORE_INFORMATION') %></a></strong></p>