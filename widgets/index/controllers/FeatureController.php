<?php
XG_App::includeFileOnce('/lib/XG_LangHelper.php');
XG_App::includeFileOnce('/lib/XG_LayoutHelper.php');
XG_App::includeFileOnce('/lib/XG_ModuleHelper.php');

class Index_FeatureController extends W_Controller {

   protected function _before() {
       XG_SecurityHelper::redirectIfNotAdmin();
   }

   public function action_add() {
       if ($_SERVER['REQUEST_METHOD'] == 'POST') {
           //  validation should go here
           $this->forwardTo('addSave');
           return;
       }
       if (isset($_GET['saved']) && $_GET['saved']) {
           $this->showNotification = true;
           $this->notificationMessage = xg_text('YOUR_SITES_FEATURE_LIST');
           $this->notificationClass = 'success';
       }

       // Prevent the browser caching the main page layout which would cause an "Uh-oh" error if submitted when stale.
       XG_LangHelper::browserNeverCache();

       XG_App::includeFileOnce('/lib/XG_LayoutEditHelper.php');
       $this->embeds = XG_LayoutEditHelper::getEmbedList('homepage');

       // Find any standard embeds already in the layout and display them
       $xgLayout = XG_Layout::load('index');
       $xgLayout = XG_LayoutHelper::updateSidebarIfNecessary($xgLayout);

       XG_App::includeFileOnce('/lib/XG_LayoutEditHelper.php');
       $this->hiddenLayoutDetails = XG_LayoutEditHelper::hiddenLayoutDetails($xgLayout);

       // Don't interfere with the default layout if we've just loaded it.
       if (XG_App::appIsLaunched()) {
           // Let's check that the required modules are there for any pay services they aren't paying for.
           $xgLayout = XG_LayoutHelper::putPayServicesInSidebarIfNecessary($xgLayout);
       }
       $xpath = new DOMXPath($xgLayout->getLayout());

       $this->initialEmbeds = array();
       $this->initialEmbeds['sidebar'] = array();
       for ($col = 1; $col <= 3; $col++) {
           $this->initialEmbeds[$col] = array();
           if ($col === 1) {
               $path = '/layout/colgroup/column/colgroup/column';
               $width = 1;
           } else if ($col === 2) {
               $path = '/layout/colgroup/column/colgroup/column[2]';
               $width = 2;
           } else if ($col === 3) {
               $path = '/layout/colgroup/column[2]';
               $width = 1;
           }
           $nodeList = $xpath->query($path);
           $column = $nodeList->item(0);
           if ($column && $column->getAttribute('width') == $width) {
               $node = $column->firstChild;
               while ($node) {
                   $attrList = array();
                   XG_LayoutHelper::getAttributes($node, $attrList);
                   $attrList = array_pop($attrList);
                   foreach ($this->embeds as $name => $embed) {
                       if ($attrList['widgetName'] == $embed['widgetName']
                               && $attrList['action'] == $embed['col' . $width . 'Action']) {
                           $initialEmbed = array();
                           $initialEmbed['embedKey'] = $name;
                           $initialEmbed['embedInstanceId'] = $attrList['embedInstanceId'];
                           if (isset($attrList['sitewide']) && $attrList['sitewide']) {
                               $this->initialEmbeds['sidebar'][] = $initialEmbed;
                           } else {
                               $this->initialEmbeds[$col][] = $initialEmbed;
                           }
                           continue;
                       }
                   }
                   $node = $node->nextSibling;
               }
           }
       }


       //  If we're in the prelaunch ('gyo') sequence, the buttons at the bottom
       //    change
       $this->displayPrelaunchButtons = !XG_App::appIsLaunched();
       if ($this->displayPrelaunchButtons) {
           $this->backLink = XG_App::getPreviousStepUrl();
           $this->nextLink = XG_App::getNextStepUrl();
       }
       $application = XN_Application::load();
       $premiumServices = $application->premiumServices;
       $hasPremiumServices = false;
       if (is_array($premiumServices) || $premiumServices instanceof Countable) {
           $hasPremiumServices = count($premiumServices) > 0;
       } else {
           $hasPremiumServices = (bool) $premiumServices;
       }

       $this->showPremiumServicesPromo = (!$hasPremiumServices) && XG_SecurityHelper::userIsOwner() && XG_App::appIsLaunched();
       $this->premiumServicesUrl = 'http://' . XN_AtomHelper::HOST_APP('www') . '/home/apps/premium?appUrl=' . $application->relativeUrl;
       $this->initialVisibleSourceFeatureCount = XG_App::appIsLaunched() ? 9999 : 6;
   }

   public function action_addSave() {

       // check current state of embed2welcome (green welcome box) and preserve
       //TODO: We do a lot of checking for the welcome embed here but that kind of thing is
       // now covered in XG_LayoutEditHelper::determineNewLayout and should be moved to there. [Thomas David Baker 2008-05-26]

       $xpath = new DOMXPath(XG_Layout::load('index')->getLayout());
       $nodes = $xpath->query("//module[@action='embed2welcome']/visible");
       $displayWelcome = false;
       if ($nodes->length > 0) {
           $nodeValue = $nodes->item(0)->nodeValue;
           $displayWelcome = mb_strlen($nodeValue) && ($nodeValue != '0');
       }

       //  true if any element was successfully dropped into a drop target
       $successfulDrop = (isset($_POST['successfulDrop']) && $_POST['successfulDrop']);

       XG_App::includeFileOnce('/lib/XG_LayoutHelper.php');
       $this->_widget->includeFileOnce('/lib/helpers/Index_FeatureHelper.php');
       $xgLayout = XG_Layout::load('index');
       if (isset($_POST['xg_feature_layout']) && mb_strlen($_POST['xg_feature_layout'])) {
            XG_App::includeFileOnce('/lib/XG_LayoutEditHelper.php');
            $initialWidgetNames = XG_LayoutHelper::widgetNamesInLayout($xgLayout->getLayout());
            $hadBlogs = XG_LayoutHelper::hasEmbed($xgLayout->getLayout(), 'profiles', array('embed1','embed2'));
            list($embedsAdded, $newLayout) = XG_LayoutEditHelper::determineNewLayout($xgLayout, $_POST['xg_feature_layout']);
            $hasBlogs = XG_LayoutHelper::hasEmbed($xgLayout->getLayout(), 'profiles', array('embed1','embed2'));
            $finalWidgetNames = XG_LayoutHelper::widgetNamesInLayout($xgLayout->getLayout());
            Index_FeatureHelper::fireEvents($initialWidgetNames, $finalWidgetNames);

            // blogs are not a first order feature but they now are given tabs (BAZ-8920) [ywh 2008-09-04]
            if ($hadBlogs !== $hasBlogs) {
                // blogs were either added or removed from the layout; update configs
                $profilesWidget = W_Cache::getWidget('profiles');
                $profilesWidget->config['showBlogsTab'] = $hasBlogs ? 1 : 0;
                $profilesWidget->saveConfig();

                // update tab layout (cannot be triggered via fireEvents because blogs is not a first order feature)
                XG_App::includeFileOnce('/lib/XG_TabLayout.php');
                $tabLayout = XG_TabLayout::loadOrCreate(false);
                if ($tabLayout) {
                    if ($hasBlogs) {
                        // add to the tab layout
                        $tabLayout->insertTabBefore('manage', 'blogs', $profilesWidget->buildUrl('blog', 'list'), xg_text('BLOGS_TAB_TEXT'))->save();
                    } else {
                        // remove from the tab layout
                        $tabLayout->removeTab('blogs', true)->save();
                    }
                }

                if ($hasBlogs) {
                    // generate activity log item
                    XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
                    XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_NETWORK, XG_ActivityHelper::SUBCATEGORY_MESSAGE_NEW_FEATURE, null, null, null, 'profiles');
                }
            }
       }
       if (! XG_App::appIsLaunched()) {
           if ($embedsAdded == 0) { $xgLayout->reInitialize(); }
           // BAZ-7599 [Jon Aquino 2008-05-16]
           if (!$successfulDrop && !XG_App::stepCompleted('Features')) { $xgLayout->setReInitializeOnLaunch(true); }
           if ($successfulDrop) { $xgLayout->setReInitializeOnLaunch(false); }
       }
       $instanceId = XG_LayoutHelper::addWelcomeBoxIfNecessary(XG_Layout::load('index'));

       // restore state of embed2welcome (green welcome box)
       $xgLayout->setEmbedInstanceProperty('visible', $displayWelcome ? '1' : '0', $instanceId);

       //  Update the enabled mozzles based on the new layout
       Index_FeatureHelper::updateMozzleStatusFromLayout($xgLayout->getLayout());

       //  Mark the step completed if we haven't yet
       if (!XG_App::allStepsCompleted()) {
           //  Mark the prelaunch step as completed if necessary
           XG_App::markStepCompleted('Features');
       }

       //  Check for an explicit success target (e.g. launch)
       if (isset($_POST['successTarget']) && mb_strlen($_POST['successTarget']) > 0) {
           header('Location: ' . $_POST['successTarget']);
           exit;
       }
       else {
           if (XG_App::appIsLaunched()) {
               $this->redirectTo('add', 'feature', array('saved' => 1));
           } else {
               //  Redirect to the new current step
               $nextStep = XG_App::currentLaunchStepRoute();
               $this->redirectTo($nextStep['actionName'], $nextStep['controllerName']);
           }
       }
   }
}

