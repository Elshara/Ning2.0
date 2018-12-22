<?php xg_header($this->highlightedTab, $this->pageTitle); ?>
<div id="xg_body">
    <div class="xg_column xg_span-16">
        <ul class="navigation navigation-solo">
            <li class="right"><a href="<%= qh($this->_buildUrl('application', 'list')) %>" class="desc add"><%= xg_html('ADD_APPLICATIONS') %></a></li>
        </ul>
        <%= xg_headline($this->pageTitle, array("avatarUser" => $this->user, "count" => $this->numApps)) %>
        <?php if(!count($this->apps)) { ?>
            <div class="xg_module">
                <div class="xg_module_body">
                    <h3><%= xg_text('ADD_APPLICATIONS') %></h3>
                    <p><%= xg_html('YOU_HAVE_NOT_ADDED_APPLICATIONS') %></p>
                    <p><a href="<%= qh($this->_buildUrl('application', 'list')) %>" class="desc add"><%= xg_html('ADD_APPLICATIONS') %></a></p>
                </div>
            </div>
        <?php } else { ?>
            <%= XG_PageHelper::searchBar(array('url' => $this->searchUrl, 'buttonText' => xg_html('SEARCH_APPLICATIONS'))); %>
            <div class="xg_module module_application">
                <div class="xg_module_body body_list">
                    <ul class="clist">
                        <?php
                        $lastApp = count($this->apps) - 1;
                        foreach ($this->apps as $i => $app) { 
                            $this->renderPartial('fragment_appDetail', 'application', array('showAddLink' => ($_GET['user'] != XN_Profile::current()->screenName), 'app' => $app, 'lastChild' => ($lastApp == $i)));
                        } ?>
                    </ul>
                    <?php
                        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
                        XG_PaginationHelper::outputPagination($this->numApps, $this->pageSize);
                    ?>
                </div><!--xg_module_body-->
                <?php if ($this->apps && $this->thirdPartyApps) { ?>
                    <div class="xg_module_foot">
                        <p><%= xg_html('DEVELOPED_BY_THIRD_PARTY') %></p>
                    </div>
                <?php } ?>
            </div><!--/xg_module-->
        <?php } ?>
	</div>
	<div class="xg_column xg_span-4 xg_last">
	    <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>
