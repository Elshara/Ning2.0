<?php /* TODO pass in params explicitly not on $this and document [Thomas David Baker 2008-07-29] */ ?>
<?php XG_App::ningLoaderRequire('xg.opensocial.embed.moduleBodyAndFooter'); ?>

<div class="xg_module_body xg_opensocial_body">

    <%= OpenSocial_GadgetHelper::requireOpenSocialJavascript($this->renderUrl); %>

    <div class="_opensocial_container">
        <div class="_opensocial_gadget">
            <div id="_opensocial-chrome-<%= "$gadget->index" %>" class="_opensocial-gadget-chrome"></div>
        </div>
    </div>

     <script type="text/javascript">
     xg.addOnRequire(function(){
             xg.opensocial.embed.moduleBodyAndFooter.renderGadgets({
                     index: '<%= "$gadget->index" %>',
                     url: '<%= $gadget->appUrl %>',
                     domain: '<%= $gadget->domain %>',
                     secureToken: '<%= $gadget->secureToken %>',
                     baseUrl: '<%= $this->baseUrl %>',
                     viewerId: '<%= $gadget->viewerName %>',
                     ownerId: '<%= $gadget->ownerName %>',
                     renderUrl: '<%= $this->renderUrl %>',
                     viewParams: '<%= $this->viewParams %>',
                     view: '<%= $this->openSocialView %>',
                     viewerId: '<%= $gadget->viewerName %>',
                     ownerId: '<%= $gadget->ownerName %>',
                     iframeUrl: '<%= $gadget->iframeUrl %>'
             });
     });
     </script>
     <a style="display:none" class="xg_osskin_link">link</a>
     <span style="display:none" class="xg_osskin_text">text</span>
</div>
<?php if ($this->openSocialView === "profile"){ ?>
    <div class="xg_module_foot">
        <ul>
        <?php W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_LinkHelper.php');
        if (OpenSocial_LinkHelper::showProfileViewAddLink($gadget->appUrl, $gadget->viewerName, $gadget->ownerName)) {
            echo '<li class="left">';
            $this->renderPartial('fragment_addAppLink', '_shared', array('appUrl' => $gadget->appUrl));
            echo '</li>';
        } ?>
        <?php // TODO: deal with broken display in the left column ?>
        <li class="right showcase-wide"><a href="<%= xnhtmlentities($this->canvasUrl) %>"><%= xg_html('VIEW_APPLICATION') %></a></li>
        </ul>
    </div>
<?php } ?>
