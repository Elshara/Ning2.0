<div class="xg_column xg_span-5">
    <?php
    if (count($this->showCategories) > 0) { ?>
        <div class="xg_module">
          <div class="xg_module_head notitle"></div>
            <div class="xg_module_body categories">
                <h3><%= xg_html('CATEGORIES'); %></h3>
                <ul>
                <?php foreach ($this->showCategories as $link => $text) { ?>
                    <li><a href="<%= xnhtmlentities($link) %>"><%= xnhtmlentities($text) %></a></li>
                <?php } ?>
                </ul>
            </div>
            <?php if ($this->totalCategories > 10) { ?>
            <div class="xg_module_foot">
                <p class="right"><a href="<%= xnhtmlentities($this->_buildUrl('category', 'listCategories')) %>"><%= xg_html('VIEW_ALL_N_CATEGORIES', $this->totalCategories) %></a></p>
            </div>
            <?php } ?>
        </div>
    <?php } ?>
   <div class="xg_module">
        <?php if (count($this->mostActiveUsers) > 1) {
            $this->renderPartial('fragment_mostActiveUsersModule', 'embed', array('users' => $this->mostActiveUsers, 'showViewUsersLink' => $this->numActiveUsers > count($this->mostActiveUsers)));
        }
        ?>
    </div>
</div>
