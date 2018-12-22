<div class="xg_module module_about">
    <div class="xg_module_head">
        <h2><%= xg_html('ABOUT_X', $this->app->name, 'left photo') %></h2>
    </div>
    <div class="xg_module_body xg_module_ning">
        <p class="vcard small">
            <%= xg_avatar($this->owner, 48) %>
            <span class="fn"><%= xg_userlink($this->owner) %></span>
            <%= xg_html('CREATED_THIS_SOCIAL_NETWORK') %>
        </p>
        <?php
        if (! XG_App::protectYourNetwork()) { ?>
            <p class="clear small"><a href="<%= $this->cloneLink %>"><%= xg_html('CREATE_YOUR_OWN') %></a></p>
        <?php
        } ?>
    </div>
    <?php
      try {
          $adminWidget = W_Cache::getWidget('admin');
          $showUpdateBox = $adminWidget->config['showUpdateBox'];
      } catch (Exception $e) {
          $showUpdateBox = 0;
      }
      if ($showUpdateBox) {
          $version = XG_Version::currentCodeVersion();
          $pos = mb_strpos($version, ':');
          if ($pos !== FALSE) {
              $branch = mb_substr($version, 0, $pos) . ' branch';
              $revision = mb_substr($version, $pos+1);
          } else {
              $branch = 'trunk';
              $revision = $version;
          }
    ?>
    <div class="xg_module_body">
      Last updated <%= xg_elapsed_time(date('c', filemtime($_SERVER['DOCUMENT_ROOT'] . '/lib/XG_Version.php'))) %>
      from revision <%= $revision %> in <%= $branch %>
    </div>
    <?php } ?>
</div>