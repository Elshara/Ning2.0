<?php xg_header('main', null, null, array('showFacebookMeta' => $this->showFacebookMeta, 'facebookPreviewImage' => $this->facebookPreviewImage, 'isMainPage' => $this->isMainPage)); ?>
<div id="xg_body">
  <?php XG_LayoutHelper::renderLayout($this->xgLayout, $this); ?>
  <script>xg_quickadd_forceReload = true /* enable the quick add refreshing. */ </script>
</div>
<?php xg_footer(); ?>
