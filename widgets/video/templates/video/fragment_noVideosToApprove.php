<div class="xg_module">
    <div class="xg_module_head notitle"></div>
    <div class="xg_module_body">
        <h3><big><%= xg_html('YOU_HAVE_FINISHED_MODERATING') %></big></h3>
        <p><%= xg_html('NO_VIDEOS_AWAITING_APPROVAL', 'href="' . xnhtmlentities($this->_buildUrl('video', 'index')) . '?sort=mostRecent"') %></p>
    </div>
</div>