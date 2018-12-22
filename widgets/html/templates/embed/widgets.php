<?php xg_header('manage',xg_text('WIDGETS')); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<%= xg_headline(xg_text('WIDGETS'))%>
            <div class="xg_module">
                <div class="xg_module_head notitle"></div>
                <div class="xg_module_body pad">
                    <h3><%= xg_html('ABOUT_WIDGETS') %></h3>
                    <p><%= xg_html('LOOKING_TO_SPICE_UP_YOUR_TEXT_BOXES') %></p>
                </div>
            </div>

            <div class="xg_module">
                <div class="xg_module_head notitle"></div>
                <div class="xg_module_body pad">
                    <h3><%= xg_html('GETTING_STARTED') %></h3>
                    <p><a href="http://www.clearspring.com/" target="_blank"><img class="right" src="<%= xnhtmlentities(xg_cdn(W_Cache::getWidget('html')->buildResourceUrl('gfx/clearspring_badge.png'))) %>" alt="clearspring.com" /></a><%= xg_html('BELOW_IS_LIST_OF_WIDGET', 'href="#widget-providers"', 'href="http://www.yourminis.com" target="_blank"') %></p>
                </div>
                <div class="xg_module_body pad">
                    <h3><%= xg_html('POPULAR_NEWS') %></h3>
                    <div class="block left">
                        <p><%= xg_html('POPULAR_NEWS_THIS_WIDGET_DISPLAYS') %></p>
                        <p><%= xg_html('TO_ADD_THE_WIDGET_GET_AND_SHARE') %> <%= xg_html('COPY_THE_EMBED_CODE_DONE') %></p>
                        <p><%= xg_html('EXPLORE_ADDITIONAL_CNN_WIDGETS', 'href="http://www.cnn.com/tools/index.html"') %></p>
                    </div>
                    <div class="block right"><object type="application/x-shockwave-flash" data="http://widgets.turner.com/o/46ddfbdfe8853f18/482b37ef563aed41/46ddfbdfe8853f18/1b57c2c1" id="W46ddfbdfe8853f18482b37ef563aed41" height="273" width="320"><param value="http://widgets.turner.com/o/46ddfbdfe8853f18/482b37ef563aed41/46ddfbdfe8853f18/1b57c2c1" name="movie"/><param value="transparent" name="wmode"><param value="all" name="allowNetworking"><param value="always" name="allowScriptAccess"></object></div>
                </div>
                <div class="xg_module_body pad">
                    <h3><%= xg_html('TRIVIA') %></h3>
                    <div class="block left">
                        <p><%= xg_html('TRIVIA_THIS_WIDGET_DISPLAYS') %></p>
                        <p><%= xg_html('TO_ADD_THE_WIDGET_GRAB_IT') %> <%= xg_html('COPY_THE_EMBED_CODE_DONE') %></p>
                    </div>
                    <div class="block right"><object type="application/x-shockwave-flash" data="http://widgets.clearspring.com/o/479f731506ec8c8d/482b42f986886037/47e149756a5b1121/c6dc2ab8" id="W479f731506ec8c8d482b42f986886037" height="250" width="300"><param value="http://widgets.clearspring.com/o/479f731506ec8c8d/482b42f986886037/47e149756a5b1121/c6dc2ab8" name="movie"/><param value="transparent" name="wmode"><param value="all" name="allowNetworking"><param value="always" name="allowScriptAccess"></object></div>
                </div>
                <div class="xg_module_body pad">
                    <h3><%= xg_html('COMICS') %></h3>
                    <div class="block left">
                        <p><%= xg_html('COMICS_THIS_WIDGET_DISPLAYS') %></p>
                        <p><%= xg_html('TO_ADD_THE_WIDGET_GRAB_IT') %> <%= xg_html('COPY_THE_EMBED_CODE_DONE') %></p>
                    </div>
                    <div class="block right"><object type="application/x-shockwave-flash" data="http://widgets.dilbert.com/o/478bf9182f409c7e/482b42c301d14344/47e14893771d6f17/bc4086fc" id="W478bf9182f409c7e482b42c301d14344" height="250" width="300"><param value="http://widgets.dilbert.com/o/478bf9182f409c7e/482b42c301d14344/47e14893771d6f17/bc4086fc" name="movie"/><param value="transparent" name="wmode"><param value="all" name="allowNetworking"><param value="always" name="allowScriptAccess"></object></div>
                </div>
                <div class="xg_module_body pad">
                    <h3><%= xg_html('TV_CLIPS') %></h3>
                    <div class="block left">
                        <p><%= xg_html('TV_CLIPS_THIS_WIDGET_DISPLAYS') %></p>
                        <p><%= xg_html('TO_ADD_THE_WIDGET_SHARE') %> <%= xg_html('COPY_THE_EMBED_CODE_DONE') %></p>
                        <p><%= xg_html('EXPLORE_ADDITIONAL_NBC_WIDGETS', 'href="http://www.nbc.com/Widgets/"') %></p>
                    </div>
                    <div class="block right"><object type="application/x-shockwave-flash" data="http://widgets.nbc.com/o/46c0ade5287de8fd/482b4034e1ca9dcc/4787d3507d1e9624/71f14322" id="W46c0ade5287de8fd482b4034e1ca9dcc" height="414" width="294"><param value="http://widgets.nbc.com/o/46c0ade5287de8fd/482b4034e1ca9dcc/4787d3507d1e9624/71f14322" name="movie"/><param value="transparent" name="wmode"><param value="all" name="allowNetworking"><param value="always" name="allowScriptAccess"></object></div>
                </div>
            </div>

            <div class="xg_module" id="widget-providers">
                <div class="xg_module_head notitle"></div>
                <div class="xg_module_body pad">
                    <h3><%= xg_html('MORE_WIDGETS') %></h3>
                    <p><%= xg_html('NEED_MORE_WIDGET_OPTIONS') %></p>
                    <p>
                        <a href="http://www.google.com/ig/directory?synd=open" target="_blank"><img src="<%= xnhtmlentities(xg_cdn(W_Cache::getWidget('html')->buildResourceUrl('gfx/google.png'))) %>" alt="Google Gadgets" /></a>
                        <a href="http://microsoftgadgets.com/" target="_blank"><img src="<%= xnhtmlentities(xg_cdn(W_Cache::getWidget('html')->buildResourceUrl('gfx/msgadgets.png'))) %>" alt="Microsoft Gadgets" /></a>
                        <a href="http://togo.ebay.com/" target="_blank"><img src="<%= xnhtmlentities(xg_cdn(W_Cache::getWidget('html')->buildResourceUrl('gfx/ebay.png'))) %>" alt="eBay To Go" /></a>
                        <a href="http://www.digg.com/tools" target="_blank"><img src="<%= xnhtmlentities(xg_cdn(W_Cache::getWidget('html')->buildResourceUrl('gfx/digg.png'))) %>" alt="Digg Tools" /></a>
                        <a href="http://www.widgetbox.com/" target="_blank"><img src="<%= xnhtmlentities(xg_cdn(W_Cache::getWidget('html')->buildResourceUrl('gfx/widgetbox.png'))) %>" alt="Widgetbox" /></a>
                        <a href="http://www.yourminis.com/minis" target="_blank"><img src="<%= xnhtmlentities(xg_cdn(W_Cache::getWidget('html')->buildResourceUrl('gfx/yourminis_badge.png'))) %>" alt="yourminis.com" /></a>
                        <a href="http://www.snipperoo.com/" target="_blank"><img src="<%= xnhtmlentities(xg_cdn(W_Cache::getWidget('html')->buildResourceUrl('gfx/snipperoo.png'))) %>" alt="Snipperoo" /></a>
                        <a href="http://www.springwidgets.com/" target="_blank"><img src="<%= xnhtmlentities(xg_cdn(W_Cache::getWidget('html')->buildResourceUrl('gfx/springwidgets.png'))) %>" alt="Spring Widgets" /></a>
                        <a href="http://www.ustream.tv/" target="_blank"><img src="<%= xnhtmlentities(xg_cdn(W_Cache::getWidget('html')->buildResourceUrl('gfx/ustreamtv.png'))) %>" alt="ustream.tv" /></a>
                        <a href="http://www.eyejot.com/" target="_blank"><img src="<%= xnhtmlentities(xg_cdn(W_Cache::getWidget('html')->buildResourceUrl('gfx/eyejot.png'))) %>" alt="EyeJot" /></a>
                    </p>
                </div>
            </div>
        </div>
        <div class="xg_1col last-child">
            <?php xg_sidebar($this); ?>
        </div>
    </div>
</div>
<?php xg_footer(); ?>
