<?php
xg_header( ($this->userIsOwner ? 'profile' : 'members'), $this->userIsOwner ? xg_text('MY_PAGE') : xg_text('XS_PAGE', xg_username($this->pageOwner)), $this->pageOwner, array(
	'metaDescription' => $this->metaDescription,
	'showFacebookMeta' => $this->showFacebookMeta,
	'facebookPreviewImage' => $this->facebookPreviewImage)
);
// is xg.index.actionicons needed here? [ywh 2008-06-25]
XG_App::ningLoaderRequire('xg.index.actionicons', 'xg.shared.util');
?>
<div id="xg_body">
    <?php if ($_GET['error']) { ?>
        <div class="xg_module topmsg notification">
            <p class="last-child"><%= xg_html(qh($_GET['error'])) %></p>
        </div>
    <?php } else if ($this->newAppData) {
        W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_LinkHelper.php');
        XG_App::ningLoaderRequire('xg.opensocial.embed.ScrollLink'); ?>
        <div class="xg_module topmsg success">
            <p class="last-child"><%= xg_html('YOU_JUST_ADDED_THE_X_APPLICATION',
                                              'href="#" dojoType="ScrollLink" _scrollToId="' . qh(OpenSocial_LinkHelper::getUniqueId($this->newAppUrl)) . '"',
                                              xnhtmlentities($this->newAppData['title']),
                                              qh(W_Cache::getWidget('opensocial')->buildUrl('application','apps')) ) %></p>
        </div>
    <?php } else if ($this->oldAppData) {
        W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_LinkHelper.php');
        XG_App::ningLoaderRequire('xg.opensocial.embed.ScrollLink'); ?>
        <div class="xg_module topmsg notification">
            <p class="last-child"><%= xg_html('YOU_ALREADY_ADDED_X', 'href="#" dojoType="ScrollLink" _scrollToId="' . qh(OpenSocial_LinkHelper::getUniqueId($this->oldAppUrl)) . '"', xnhtmlentities($this->oldAppData['title']) ) %></p>
        </div>
    <?php } ?>
    <?php XG_LayoutHelper::renderLayout($this->xgLayout, $this); ?>
</div>
<?php xg_footer(); ?>
