<?php

XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');

class Index_PromotionController extends W_Controller {

    /**
     * Displays a link for promoting (or unpromoting) a content object
     *
     * @param $content XN_Content  The content object
     * @param $type string  The type of the object being promoted or unpromoted
     * @param $afterAction string  JavaScript to execute after promoting or unpromoting the object.
     */
    // type doesn't seemed to be used for anything? [Jon Aquino 2007-02-28]
    public function action_link($content, $type = null, $afterAction = null) {
        $this->content = $content;
        $this->action = is_null(XG_PromotionHelper::promotedOn($content)) ? 'promote' : 'remove';
        $this->setLinkTextAndClass($this->action);
        $this->type = is_null($type) ? mb_strtolower($content->type) : $type;
        $this->afterAction = $afterAction;
    }

    public function action_promote() {
        $src = isset($_GET['src']) ? $_GET['src'] : 'icon';
        $this->promoteOrRemove('promote', $src);
    }

    public function action_remove() {
        $src = isset($_GET['src']) ? $_GET['src'] : 'icon';
        $this->promoteOrRemove('remove', $src);
    }

    protected function setTextAndImgSrc($action) {
        switch ($action) {
            case 'promote':
                $this->iconText = xg_text('ACTION_BUTTON_FEATURE');
                $this->imgSrc = xg_cdn($this->_widget->buildResourceUrl('gfx/actionbar/promote-add.png'));
                $this->tooltip = xg_text('FEATURE_THIS_ON_YOUR');
                break;
            case 'remove':
                $this->iconText = xg_text('ACTION_BUTTON_REMOVE');
                $this->imgSrc = xg_cdn($this->_widget->buildResourceUrl('gfx/actionbar/promote-remove.png'));
                $this->tooltip = xg_text('REMOVE_THIS_FROM_YOUR');
                break;
        }
    }

    protected function setLinkTextAndClass($action) {
        switch ($action) {
            case 'promote':
                $this->linkText = xg_text('FEATURE_ON_HOME');
                $this->linkClass = 'feature-add';
                break;
            case 'remove':
                $this->linkText = xg_text('REMOVE_FROM_HOME');
                $this->linkClass = 'feature-remove';
                break;
        }
    }

    protected function promoteOrRemove($action, $src) {
        try {
            XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
            if (! XG_SecurityHelper::userIsAdmin()) {
                throw new Exception("Only the owner can $action!");
            }
            if (! isset($_POST['id'])) {
                throw new Exception("No content specified!");
            }
            $content = XN_Content::load($_POST['id']);
            if ($action == 'promote') {
                XG_PromotionHelper::promote($content);
                XG_PromotionHelper::addActivityLogItem($_POST['type'], $content);
            } elseif ($action == 'remove') {
                XG_PromotionHelper::remove($content);
            } else {
                throw new Exception("Unknown action: $action");
            }
            $content->save();
            $actionForSet = ($action == 'promote') ? 'remove' : 'promote';
            if ($src == 'link') {
                $this->setLinkTextAndClass($actionForSet);
            } else {
                $this->setTextAndImgSrc($actionForSet);
            }
            $this->message = $action == 'promote' ? xg_html('ITEM_WILL_APPEAR') : xg_html('ITEM_WILL_NO_LONGER_APPEAR');
        } catch (Exception $e) {
            $this->message = $e->getMessage();
        }
    }
}
