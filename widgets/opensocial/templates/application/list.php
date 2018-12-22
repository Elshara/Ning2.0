<?php xg_header($this->selectedTab, $this->title);
XG_App::includeFileOnce('/lib/XG_ListTemplateHelper.php');
W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_LinkHelper.php'); ?>
<div id="xg_body">
    <div class="xg_column xg_span-16">
        <%= xg_headline(xg_html('ALL_APPLICATIONS'), array('count' => $this->totalCount, 'byline1Html' =>  '<a href="' . qh(OpenSocial_LinkHelper::getMyApplicationsLink()) . '">' . xg_html('MY_APPLICATIONS') . '</a>')) %>
        <div class="xg_column xg_span-4">
            <div class="xg_module">
                <div class="xg_module_head">
                    <h2><%= xg_html('DIRECTORY') %></h2>
                </div>
                <div class="xg_module_body">
                    <?php $this->renderPartial('fragment_errorMsg', 'application'); ?>
                    <ul class="nobullets categories">
                        <?php foreach ($this->superCategories as $category) {
                            $this->renderPartial('fragment_categoryLink', 'application', array('category' => $category));
                        } ?>
                    </ul>
                </div>
            </div>
            <div class="xg_module">
                <div class="xg_module_head">
                      <h2><%= xg_html('CATEGORIES') %></h2>
                </div>
                <div class="xg_module_body">
                    <ul class="nobullets categories">
                        <?php foreach ($this->categories as $category) {
                            $this->renderPartial('fragment_categoryLink', 'application', array('category' => $category));
                        } ?>
                    </ul>
                </div>
            </div>
            <div class="xg_module">
                <div class="xg_module_body">
                    <p class="item_add_url"><%= xg_html('ADD_BY_URL_LINK', 'class="desc add" href="' . xnhtmlentities($this->addByUrlUrl) . '"') %></p>
                </div>
            </div>
        </div>
        <div class="xg_column xg_span-12 xg_last">
            <%= XG_PageHelper::searchBar(array('url' => $this->searchUrl, 'buttonText' => xg_html('SEARCH_APPLICATIONS'))); %>
            <div class="xg_module module_application">
                <div class="xg_module_body body_aplist">
                    <?php if (count($this->apps) > 0) { ?>
                        <ul class="clist">
                            <?php
                            $lastApp = count($this->apps) - 1;
                            foreach ($this->apps as $i => $app) {
                                $this->renderPartial('fragment_appDetail', 'application', array('showAddLink' => true, 'app' => $app, 'lastChild' => ($lastApp == $i)));
                            } ?>
                        </ul>
                        
                        <?php XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
                        XG_PaginationHelper::outputPagination($this->numApps, $this->pageSize);
                    } else { ?>
                        <p><%= xg_html("NO_RESULTS_FOUND_FOR_SEARCH_TERM", $this->searchTerm) %></p>
                    <?php } ?>
                </div><!--xg_module_body-->
                <?php if (count($this->apps) > 0) { ?>
                    <div class="xg_module_foot">
                        <p><%= xg_html('DEVELOPED_BY_THIRD_PARTY') %></p>
                    </div>
                <?php } ?>
            </div><!--/xg_module-->
         </div>
    </div>
    <div class="xg_column xg_span-4 last-child">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>
