<?php xg_header('profile',xg_text('APPEARANCE'), null, array('otherMozzles' => 'main')); ?>
<?php XG_App::ningLoaderRequire('xg.shared.BazelColorPicker', 'xg.shared.BazelImagePicker', 'xg.index.appearance.edit', 'xg.profiles.appearance.edit') ?>
<script type="text/javascript">
    xg.addOnRequire(function() {
        xg_handleJoinFlowSubmit = xg.profiles.appearance.edit.handleJoinFlowSubmit;
    });
</script>
    <div id="xg_body">
        <div class="xg_colgroup">

            <?php
            XG_App::includeFileOnce('/lib/XG_AppearanceTemplateHelper.php');
            XG_AppearanceTemplateHelper::outputEditAppearancePage(array(
                'networkAppearance' => false, /* TODO: should we pass in each switchable value rather than one almighty boolean? */
                'themes' => $this->themes,
                'showNotification' => $this->showNotification,
                'notificationClass' => $this->notificationClass,
                'notificationTitle' => $this->notificationTitle,
                'notificationMessage' => $this->notificationMessage,
                'defaults' => $this->defaults,
                'imagePaths' => $this->imagePaths,
                'ningLogoDisplayChecked' => $this->ningLogoDisplayChecked,
                'fontOptions' => $this->fontOptions,
                'displayPrelaunchButtons' => $this->displayPrelaunchButtons,
                'inJoinFlow' => $this->inJoinFlow,
                'form' => $this->form,
                'screenName' => $this->_user->screenName,
                'appName' => $this->app->name,
                'submitUrl' => $this->submitUrl
            ));
            ?>

            <div class="xg_1col last-child">
                <?php xg_sidebar($this); ?>
            </div>
        </div>
    </div>
<?php xg_footer(); ?>
