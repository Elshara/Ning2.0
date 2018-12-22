<?php xg_header('main', xg_text('SITE_IS_NOT_READY'), NULL, array('blankLaunchBar' => TRUE)); ?>
    <div id="xg_setup"></div>
    <div id="xg_body">
        <div class="xg_colgroup">
            <div class="first-child">
                <h1><%= xg_html('BAZEL_IS_BEING_SET_UP') %></h1>
                <div class="xg_module">
                    <div class="xg_module_body pad">
                            <p>
                                <img src="<%= xg_cdn('/xn_resources/widgets/index/gfx/setup/construction.gif') %>" alt="<%= xg_html('UNDER_CONSTRUCTION') %>" width="80" height="80" class="left" style="margin-right:15px" /><big><strong><%= xg_html('SORRY_HOWEVER_ITS_EASY') %><br />
                                <?php /* We send them to the homepage here and not the clone page because the homepage does not force login.  Better user experience.  See BAZ-4930. */ ?>
                                <a href="<%= xnhtmlentities('http://' . XN_AtomHelper::HOST_APP('www')) %>"><%= xg_html('GET_YOUR_OWN_BAZEL') %></a></strong></big>
                            </p>
                            <p><small><%= xg_html('IF_YOU_ARE_ADMIN', 'href="' . xnhtmlentities(XN_Request::signInUrl(xg_absolute_url('/'))) . '"') %></small></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php xg_footer(); ?>
