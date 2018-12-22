<?php

W_Cache::getWidget('opensocial')->includeFileOnce('/controllers/EndpointController.php');

/**
 * Provides requestNavigateTo redirects
 */
class OpenSocial_ViewController extends OpenSocial_EndpointController {
    
    protected function _before() {
        $this->ownerId = $_GET['ownerId'];
        $this->appUrl = $_GET['appUrl'];
    }
   
    /**
     * requestNavigateTo('canvas')
     */
    public function action_navigateToCanvas() {
        $viewParams = isset($_GET['view-params']) ? $_GET['view-params'] : '';
        $this->redirectTo("show", "application", array('owner' => $this->ownerId, 'appUrl' => $this->appUrl, 'view-params' => $viewParams));
    }
    
    /**
     * requestNavigateTo('profile')
     */    
    public function action_navigateToProfile() {
        $this->redirectTo(User::profileUrl($this->ownerId));
    }
    
    /**
     * requestNavigateTo('preview')
     */    
    public function action_navigateToPreview() {
        $this->redirectTo("about", "application", array('appUrl' => $this->appUrl));
    }
}

?>
