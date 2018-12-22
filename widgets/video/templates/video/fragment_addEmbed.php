<h4><%= xg_html('FROM_YOUTUBE_OR_GOOGLE')%></h4>
<span id="youtubelogo"><a href="<%= xnhtmlentities($this->_buildUrl('video', 'addEmbed')) %>"><img width="90" height="35" alt="YouTube" src="<%= xg_cdn('/xn_resources/widgets/video/gfx/youtube.png') %>"/></a></span>
<span id="googlelogo"><a href="<%= xnhtmlentities($this->_buildUrl('video', 'addEmbed')) %>"><img width="90" height="35" alt="Google" src="<%= xg_cdn('/xn_resources/widgets/video/gfx/google.png') %>"/></a></span>
<p><%= xg_html('GRAB_THE_HTML_EMBED_CODE')%></p>
<p class="right"><strong><a href="<%= xnhtmlentities($this->_buildUrl('video', 'addEmbed')) %>"><%= xg_html('ADD_VIDEO') %></a></strong></p>