<?php
class Photo_EmbedController extends W_Controller {
    protected function _before() {
        XG_App::includeFileOnce('/lib/XG_Embed.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_FullNameHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_HttpHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_EmbedType.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_UserHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_AlbumHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_SecurityHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_PhotoHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Photo_SlideshowHelper.php');
        Photo_HttpHelper::trimGetAndPostValues();
    }
    public function action_embed1($args) { $this->renderEmbed($args['embed'], 1, $args['maxEmbedWidth']); }
    public function action_embed2($args) { $this->renderEmbed($args['embed'], 2, $args['maxEmbedWidth']); }
    public function action_embed3($args) { $this->renderEmbed($args['embed'], 3, $args['maxEmbedWidth']); }

    /**
     * @param $maxEmbedWidth  the maximum width for <embed>s, in pixels
     */
    private function renderEmbed($embed, $columnCount, $maxEmbedWidth) {
        $this->typeOptions = array(array('label' => xg_text('PHOTO_SLIDESHOW'), 'value' => 'slideshow'), array('label' => xg_text('THUMBNAILS'), 'value' => 'thumbnails'), array('label' => xg_text('ALBUMS_NO_COLON'), 'value' => 'albums'));
        $this->numOptions = array();
        foreach (array(0,2,4,6,8,10) as $j) {
            $this->numOptions[] = array('label' => (string) $j, 'value' => (string) $j);
        }
        $this->setValuesUrl = $this->_buildUrl('embed', 'setValues', array('id' => $embed->getLocator(), 'xn_out' => 'json', 'maxEmbedWidth' => $maxEmbedWidth, 'columnCount' => $columnCount, 'sidebar' => XG_App::isSidebarRendering() ? '1' : '0'));
        $this->updateEmbedUrl = $this->_buildUrl('embed', 'updateEmbed', array('id' => $embed->getLocator(), 'xn_out' => 'json'));
        if ($embed->isOwnedByCurrentUser()) {
            if ($embed->getType() == 'profiles') {
                $this->photoSetOptions = array(array('label' => xg_text('I_HAVE_RECENTLY_ADDED'), 'value' => 'for_contributor'), array('label' => xg_text('FROM_THE_BAZEL'), 'value' => 'all'));
            } else {
                $this->photoSetOptions = array(array('label' => xg_text('RECENTLY_ADDED'), 'value' => 'all'), array('label' => xg_text('MOST_POPULAR'), 'value' => 'popular'), array('label' => xg_text('FEATURED'), 'value' => 'promoted'));
            }
            foreach(Photo_AlbumHelper::getAllAvailableAlbums($this->_user->screenName) as $id => $name) {
                $this->photoSetOptions[] = array('label' => $name, 'value' => 'album_' . $id);
            }
            $this->albumSetOptions = array(array('label' => xg_text('RECENTLY_ADDED'), 'value' => 'all'), array('label' => xg_text('MOST_POPULAR'), 'value' => 'popular'), array('label' => xg_text('FEATURED'), 'value' => 'promoted'));
        }
        $defaultSet = $embed->getType() == 'profiles' ? 'for_contributor' : 'all';
        $embed->set('photoSet', $embed->get('photoSet') ? $embed->get('photoSet') : $defaultSet);
        if (! $embed->get('albumSet')) { $embed->set('albumSet', 'all'); }
        if ($embed->get('photoNum') == null) { $embed->set('photoNum', 4); }
        $type = $embed->get('photoType');
        if (! $type) { $type = 'slideshow'; }
        if ($type == 'detail') { $type = 'thumbnails'; } // BAZ-6028 [Jon Aquino 2008-03-06]
        $embed->set('photoType', $type);
        $this->embed = $embed;
        if ($embed->get('photoNum') == 0 && $embed->getType() == 'profiles' && ! $this->embed->isOwnedByCurrentUser() && $type != 'slideshow') { return $this->renderNothing(); }
        $this->moduleBodyAndFooterHtml = $this->moduleBodyAndFooterHtml($embed, $columnCount, $maxEmbedWidth);
        $this->title = xg_text('PHOTOS');
        if ($embed->getType() == 'profiles') { $this->title = $this->embed->isOwnedByCurrentUser() ? xg_text('MY_PHOTOS') : xg_text('XS_PHOTOS', xg_username(XG_Cache::profiles($this->embed->getOwnerName()))); }
        if (trim($this->moduleBodyAndFooterHtml)) { $this->render('embed'); }
    }

    /**
     * Update the module body and footer HTML - only called for Frink drop updates
     *
     * Expected GET variables:
     *     - id - the embed instance ID, used to retrieve the module data
     *     - xn_out - must be set to 'json'
     *
     * Expected POST variables:
     *     - maxEmbedWidth - the maximum width for embeds in the current container
     *     - columnCount - the number of columns this module spans (1, 2, or 3)
     *
     * @return string   JSON string containing the new module body and footer
     *                  or an error message if the module body and footer could
     *                  not be updated
     */
    public function action_updateEmbed() {
        try {
            $embed = XG_Embed::load($_GET['id']);
            if (! $embed->isOwnedByCurrentUser()) { throw new Exception('Not embed owned.'); }
            $this->moduleBodyAndFooterHtml = self::moduleBodyAndFooterHtml($embed, $_POST['columnCount'], $_POST['maxEmbedWidth']);
        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }
    }

    /**
     * Expected GET variables:
     *     - id - the embed instance ID, used to retrieve the module data
     *
     * Expected POST variables:
     *     - columnCount - number of columns that this module spans (1, 2, or 3)
     *     - maxEmbedWidth - the maximum width for <embed>s, in pixels
     *     - photoSet - the photo set from which to display thumbnails
     *     - albumSet - the album set from which to display thumbnails
     *     - photoNum - the number of rows to display
     *     - random - for the slideshow, whether to display random photos
     *     - photoType - displaying slideshow, thumbnails, or albums
     */
    public function action_setValues() {
        $embed = XG_Embed::load($_GET['id']);
        if (! $embed->isOwnedByCurrentUser()) { throw new Exception('Not embed owner.'); }
        $embed->set('photoSet', $_POST['photoSet']);
        $embed->set('albumSet', $_POST['albumSet']);
        $embed->set('photoNum', $_POST['num']);
        $embed->set('random', $_POST['random']);
        $embed->set('photoType', $_POST['type']);
        $columnCount = XG_Embed::getValueFromPostGet('columnCount');
        $maxEmbedWidth = XG_Embed::getValueFromPostGet('maxEmbedWidth');

        $this->moduleBodyAndFooterHtml = self::moduleBodyAndFooterHtml($embed, $columnCount, $maxEmbedWidth);

        // invalidate admin sidebar if necessary
        if ($_GET['sidebar']) {
            XG_App::includeFileOnce('/lib/XG_LayoutHelper.php');
            XG_LayoutHelper::invalidateAdminSidebarCache();
        }
    }

    /**
     * Returns HTML for the body and footer of the Photos module.
     *
     * @param $embed XG_Embed containing the state of the Photos module
     * @param $columnCount number of columns that this module spans (1, 2, or 3)
     * @param $maxEmbedWidth  the maximum width for <embed>s, in pixels
     * @return string  the HTML, or an empty string if nothing should be displayed
     */
    private function moduleBodyAndFooterHtml($embed, $columnCount, $maxEmbedWidth) {
        $data = Photo_EmbedType::get($embed)->getData($embed, $columnCount);
        if (! $data['itemsExist'] && $embed->getType() == 'profiles' && !$embed->isOwnedByCurrentUser()) { return null; }
        XG_Cache::profiles($data['items']); // BAZ-4889: preload user info
        return Photo_EmbedType::get($embed)->moduleBodyAndFooterHtml($embed, $columnCount, $maxEmbedWidth, $data, $this);
    }

    public function action_error() {
        $this->render('blank');
    }

    /**
     * Include the rendering of a partial template in the output render flow.
     * This function can be called four ways, depending on which of
     * controller and args you want to specify:
     * renderPartial('template')
     * renderPartial('template','controller')
     * renderPartial('template','controller',array('arg' => 'value')
     * renderPartial('template',array('arg' => 'value'))
     *
     * @param string $template The name of the template to render
     * @param string|array $controllerOrArgs optional If a string, the name of the template's
     * controller. If an array, arguments to pass to the partial template
     * @param array $args optional Arguments to pass to the partial template
     */
    public function renderPartial($template, $controller = null, $args = null) {
        return parent::renderPartial($template, $controller, $args);
    }
}
