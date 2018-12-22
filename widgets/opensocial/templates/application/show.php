<?php xg_header($this->gadget->ownerName == $this->gadget->viewerName ? 'profile' : 'members', $this->title);
W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_GadgetHelper.php');
XG_App::ningLoaderRequire('xg.opensocial.application.ApplicationSettingsLink');

ob_start();
$shareUrl = W_Cache::getWidget('main')->buildUrl('sharing', 'share', array('url' => $this->appData->my->appUrl, 'title' => $this->title, 'type' => 'opensocialapp')); ?>
<a href="<%=$shareUrl%>" class="desc share"><%= xg_html('SHARE') %></a>
<?php
if ($this->gadget->ownerName == $this->gadget->viewerName) { ?>
<a href="#" class="desc settings"
    dojoType="ApplicationSettingsLink"
    _logOpenSocial="<%= XG_App::logOpenSocial() ? "true" : "false" %>"
    _setValuesUrl="<%= $this->setValuesUrl %>"
    _isOnMyPage="<%= $this->appData->my->isOnMyPage ? "true" : "false" %>"
    _canAddActivities="<%= $this->appData->my->canAddActivities ? "true" : "false" %>"
    _canSendMessages="<%= $this->appData->my->canSendMessages ? "true" : "false" %>"
    ><%= xg_html('APPLICATION_SETTINGS') %></a>
<?php }
W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_LinkHelper.php');
if (OpenSocial_LinkHelper::showCanvasViewAddLink($this->appData->my->appUrl, $this->gadget->viewerName, $this->gadget->ownerName)) { 
    $this->renderPartial('fragment_addAppLink', '_shared', array('appUrl' => $this->appData->my->appUrl, 'showSignInToAdd' => "0")); 
}
$aboutUrl = W_Cache::getWidget('opensocial')->buildUrl('application','about', array('appUrl' => $this->appData->my->appUrl));
if ($this->gadget->ownerName != $this->gadget->viewerName) { ?>
    <a href="<%= xnhtmlentities($aboutUrl) %>" class="desc about"><%= xg_html('ABOUT_THIS_APPLICATION') %></a>
<?php }
if (OpenSocial_LinkHelper::showCanvasViewRemoveLink($this->gadget->viewerName, $this->gadget->ownerName)) {
    $this->renderPartial('fragment_removeAppLink', '_shared', array('appUrl' => $this->appData->my->appUrl));
}
$byline1Html = ob_get_contents();
ob_end_clean();
?>

<div id="xg_body">
    <?php if ($_GET['readded']) { ?>
        <div class="topmsg notification"><p class="last-child"><%= xg_html('YOU_ALREADY_ADDED_X_CANVAS', qh($this->gadgetPrefs['title'])) %></p></div>
    <?php } else if ($_GET['newApp']) { ?>
        <div class="topmsg success"><%= xg_html('YOU_ADDED_X_CANVAS', qh($this->gadgetPrefs['title']), 'class="last-child"', 'href="' . qh(W_Cache::getWidget('opensocial')->buildUrl('application', 'apps', array('user' => $_GET['owner']))) . '"') %></div>
    <?php } ?>
    <div id="xg_applications_settings_updated_success" class="topmsg success" style="display:none"><p class="last-child"><%= xg_html('YOUR_SETTINGS_HAVE_BEEN_SAVED') %></p></div>
    <div id="xg_applications_settings_add_application_error" class="topmsg errordesc" style="display:none"><p class="last-child"><%= xg_html('YOU_ALREADY_HAVE_FIVE_APPLICATIONS') %></p></div>
    <div class="xg_column xg_span-16">
        <%= xg_headline($this->title, array('byline1Html' => $byline1Html)) %>
        <div class="xg_module module_opensocial">
            <?php if ($this->blocked) {
                $this->renderPartial('fragment_removedApp', 'application', array('appUrl' => $this->appUrl));
            } else { 
                $this->renderPartial('fragment_moduleBodyAndFooter', '_shared', array('gadget' => $this->gadget, 'baseUrl' => $this->baseUrl, 'viewParams' => $this->viewParams, 'ningApplication' => $this->gadgetPrefs['ningApplication'] ));
            } ?>
            <div class="xg_module_foot">
                <?php $reportAbuseUrl =  W_Cache::getWidget('main')->buildUrl('index', 'reportAbuse', array('appUrl' => $this->appUrl, 'appTitle' => $this->title)); ?>
                <p><%= $this->gadgetPrefs['ningApplication'] == true ? xg_html('THIS_APPLICATION_WAS_DEVELOPED_NING', 'href="' . xnhtmlentities($reportAbuseUrl) .'"') : xg_html('THIS_APPLICATION_WAS_DEVELOPED', 'href="' . xnhtmlentities($reportAbuseUrl) .'"') %></p>
            </div>
        </div>
    </div>
    <div class="xg_column xg_span-4 xg_last">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>
