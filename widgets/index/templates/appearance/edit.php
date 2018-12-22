<?php xg_header('manage',xg_text('APPEARANCE')); ?>
<?php XG_App::ningLoaderRequire('xg.shared.BazelImagePicker', 'xg.shared.BazelColorPicker', 'xg.index.appearance.edit'); ?>
<script type="text/javascript">
    xg.addOnRequire(function() {
        xg_handleLaunchBarSubmit = xg.index.appearance.edit.handleLaunchBarSubmit;
    });
</script>
    <div id="xg_body">
        <div class="xg_colgroup">
            <?php XG_App::includeFileOnce('/lib/XG_AppearanceTemplateHelper.php'); ?>
            <%= XG_AppearanceTemplateHelper::outputEditAppearancePage(array(
                'networkAppearance' => true,
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
                'successMessageIfAny' => $this->successMessageIfAny,
                'submitUrl' => $this->submitUrl
            )) %>
            <div class="xg_1col last-child">
                <?php xg_sidebar($this) ?>
            </div>
        </div>
    </div>
<?php xg_footer(); ?>
