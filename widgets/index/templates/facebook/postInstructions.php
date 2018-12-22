<?php xg_header('manage',xg_text('FACEBOOK_SETUP')); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<%= xg_headline(xg_text('FACEBOOK_PROMOTING'))%>
            <div class="easyclear">
                <ul class="backlink navigation">
                    <li><a href="/main/facebook/setup">&larr; <%= xg_html('FACEBOOK_BACK_TO_EMBEDDING') %></a></li>
                </ul>
            </div>
            <div class="xg_module instructions">
                <div class="xg_module_body pad">
                    <p class="introduction"><%= xg_html('FACEBOOK_PROMOTE', 'href="http://www.facebook.com/developers/apps.php" target="_blank"') %></p>
                    <img src="<%= xg_cdn('/xn_resources/widgets/index/gfx/facebook/myapplications.png') %>" alt="<%= xg_html('FACEBOOK_PROMOTE_MY_APPLICATIONS_PAGE') %>" />
                    <hr />
                    <h3>1. <%= xg_html('FACEBOOK_PROMOTE_TITLE_ABOUT_PAGE') %></h3>
                    <p><%= xg_html('FACEBOOK_PROMOTE_TEXT_ABOUT_PAGE') %></p>
                    <hr />
                    <h3>2. <%= xg_html('FACEBOOK_PROMOTE_TITLE_SUBMIT') %></h3>
                    <p><%= xg_html('FACEBOOK_PROMOTE_TEXT_SUBMIT_1') %></p>
                    <p><%= xg_html('FACEBOOK_PROMOTE_TEXT_SUBMIT_2') %></p>
                    <hr />
                    <h3>3. <%= xg_html('FACEBOOK_PROMOTE_TITLE_PROFILE') %></h3>
                    <p><%= xg_html('FACEBOOK_PROMOTE_TEXT_PROFILE_1') %></p>
                    <p><%= xg_html('FACEBOOK_PROMOTE_TEXT_PROFILE_2') %></p>
                    <p><%= xg_html('FACEBOOK_PROMOTE_TEXT_PROFILE_3') %></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php xg_footer(); ?>
