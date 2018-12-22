<?php XG_App::addToCssSection('<link rel="stylesheet" type="text/css" media="screen" href="'.xnhtmlentities(XG_Version::addXnVersionParameter($this->_widget->buildResourceUrl('css/module.css'))).'" />');
XG_App::ningLoaderRequire('xg.opensocial.embed.OpenSocialModule');
if ($this->embed->isOwnedByCurrentUser()) { ?>
    <div class="xg_module module_opensocial no_cross_container"
            dojoType="OpenSocialModule"
            logOpenSocial="<%= (XG_App::logOpenSocial() ? '1' : '') %>"
            gadgetTitle="<%= xnhtmlentities($this->title) %>"
            appUrl="<%= xnhtmlentities($this->appUrl) %>"
            removeBoxUrl="<%= xnhtmlentities(W_Cache::getWidget('opensocial')->buildUrl('application', 'removeFromMyPage', array('appUrl' => urlencode($this->appUrl)))) %>"
            removeAppUrl="<%= xnhtmlentities(W_Cache::getWidget('opensocial')->buildUrl('application', 'remove', array('appUrl' => urlencode($this->appUrl)))) %>"
            isOnMyPage="<%= $this->appData->my->isOnMyPage %>"
            canSendMessages="<%= $this->appData->my->canSendMessages %>"
            canAddActivities="<%= $this->appData->my->canAddActivities %>"
            installedByUrl="<%= $this->appData->my->installedByUrl %>"
            setValuesUrl="<%= xnhtmlentities($this->_buildUrl('embed', 'setValues', array('id' => $this->embed->getLocator(), 'xn_out' => 'json', 'maxEmbedWidth' => $this->maxEmbedWidth))) %>">
<?php } else { ?>
    <div class="xg_module module_opensocial">
<?php }
W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_LinkHelper.php');
?>
        <div class="xg_module_head" id="<%= qh(OpenSocial_LinkHelper::getUniqueId($this->appUrl)) %>">
            <h2><%= xnhtmlentities($this->title) %></h2>
        </div>
    <?php $this->renderPartial('fragment_moduleBodyAndFooter', '_shared', array('gadgetTitle' => $this->title, 'gadget' => $this->gadget, 'baseUrl' => $this->baseUrl,
                                                                         'maxEmbedWidth' => $this->maxEmbedWidth, 'ningApplication' => $this->ningApplication)) ?>
    </div><!--/.xg_module-->
