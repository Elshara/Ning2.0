<?php
/**
 * Logic for the three different types of modules: slideshow, thumbnails, albums
 */
abstract class Photo_EmbedType {

    /** Mapping from photoType to Photo_EmbedType. */
    protected static $embedTypes = array();

    /**
     * Returns the EmbedType specific to the given type.
     *
     * @param $embed XG_Embed  metadata for the module
     * @return Photo_EmbedType  logic for the given type
     */
    public static function get($embed) {
        $type = $embed->get('photoType');
        $className = 'Photo_' . ucfirst($type) . 'EmbedType';
        if (! self::$embedTypes[$type]) {
            self::$embedTypes[$type] = new $className;
        }
        return self::$embedTypes[$type];
    }

    /**
     * Returns the data for the module to display.
     *
     * @param $embed XG_Embed  the object containing metadata for the module
     * @param $columnCount integer  number of columns that this module spans (1, 2, or 3)
     * @return array  the data to render
     * @override
     */
    public abstract function getData($embed, $columnCount);

    /**
     * Returns HTML for the body and footer of the module.
     *
     * @param $embed XG_Embed containing the state of the module
     * @param $columnCount number of columns that this module spans (1, 2, or 3)
     * @param $maxEmbedWidth  the maximum width for <embed>s, in pixels
     * @param $data array  data for the module to display
     * @param $controller Photo_EmbedController  the controller, used to render partials
     */
    public function moduleBodyAndFooterHtml($embed, $columnCount, $maxEmbedWidth, $data, $controller) {
        ob_start();
        $this->outputModuleBodyAndFooter($embed, $columnCount, $maxEmbedWidth, $data, $controller);
        $moduleBodyAndFooterHtml = trim(ob_get_contents());
        ob_end_clean();
        return $moduleBodyAndFooterHtml;
    }

    /**
     * Outputs the body and footer of the module.
     *
     * @param $embed XG_Embed containing the state of the module
     * @param $columnCount number of columns that this module spans (1, 2, or 3)
     * @param $maxEmbedWidth  the maximum width for <embed>s, in pixels
     * @param $data array  data for the module to display
     * @param $controller Photo_EmbedController  the controller, used to render partials
     */
    protected abstract function outputModuleBodyAndFooter($embed, $columnCount, $maxEmbedWidth, $data, $controller);

    /**
     * Returns the URL for the Add Photos link or null if no link should be shown.
     *
     * @param   $embed      XG_Embed    Embed in question.
     * @param   $itemsExist boolean     true if at least one photo exists for the embed.
     * @return              string      URL for link, or null if link should not be shown.
     */
    protected function addPhotosUrl($embed, $itemsExist) {
        XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
        if (XG_SecurityHelper::currentUserCanSeeAddContentLink($embed, $itemsExist)) {
            return W_Cache::getWidget('photo')->buildUrl('photo', XG_MediaUploaderHelper::action());
        }
        return null;
    }

}

/**
 * Logic for the slideshow module.
 */
class Photo_SlideshowEmbedType extends Photo_AbstractPhotoEmbedType {

    /**
     * Returns the data for the module to display.
     *
     * @param $embed XG_Embed  the object containing metadata for the module
     * @param $columnCount integer  number of columns that this module spans (1, 2, or 3)
     * @return array  the data to render
     * @override
     */
    public function getData($embed, $columnCount) {
        list($feedUrl, $itemsExist) = Photo_SlideshowHelper::feedUrl($embed->get('photoSet'), $embed->getOwnerName(), true, $embed->get('random'));
        return array('feedUrl' => $feedUrl, 'items' => array(), 'itemsExist' => $itemsExist);
    }

    /**
     * Outputs the body and footer of the module.
     *
     * @param $embed XG_Embed containing the state of the module
     * @param $columnCount number of columns that this module spans (1, 2, or 3)
     * @param $maxEmbedWidth  the maximum width for <embed>s, in pixels
     * @param $data array  data for the module to display
     * @param $controller Photo_EmbedController  the controller, used to render partials
     * @override
     */
    protected function outputModuleBodyAndFooter($embed, $columnCount, $maxEmbedWidth, $data, $controller) {
        if (! $data['itemsExist'] && $embed->getType() == 'profiles') { return; }
        if ($data['itemsExist']) { ?>
            <div class="xg_module_body body_slideshow">
                <?php
                $fullsize_url = W_Cache::getWidget('photo')->buildUrl('photo','slideshow','?feed_url='.urlencode($data['feedUrl']));
                ob_start();
                $controller->renderPartial('fragment_slideshowPlayer', 'photo', array(
                        // Set wmode to opaque so action dialogs will appear above the player [Jon Aquino 2007-02-13]
                        'feed' => urlencode($data['feedUrl']),
                        'slideshow_width' => $maxEmbedWidth,
                        'slideshow_height' => round($maxEmbedWidth * 0.784),
                        'wmode' => 'transparent',
                        'layout' => 'within_app',
                        'fullsize_url'=>urlencode($fullsize_url.'&back_url='.urlencode((Photo_HttpHelper::currentUrl())))));
                $playerHtml = trim(ob_get_contents());
                ob_end_clean();
                ?>
                <p class="loading"><%= xg_html('LOADING') %></p>
                <input type="hidden" id="playerHtml" value="<?php echo xnhtmlentities(preg_replace('/\s+/u', ' ', $playerHtml)) ?>" />
            </div>
            <?php
            $controller->renderPartial('fragment_footer', 'embed', array('embed' => $embed, 'viewAllUrl' => $this->viewAllUrl($embed),
                                                                         'addPhotosUrl' => $this->addPhotosUrl($embed, $data['itemsExist'])));
            ?>
            <script src="<%= xg_cdn('/xn_resources/widgets/photo/js/photo/slideshow.js') %>" type="text/javascript"></script>
        <?php
        } else {
            echo $this->getNoItemsHtml($embed, $data);
        }
    }

}

/**
 * Logic for the thumbnails module.
 */
class Photo_ThumbnailsEmbedType extends Photo_AbstractPhotoEmbedType {

    /**
     * Returns the data for the module to display.
     *
     * @param $embed XG_Embed  the object containing metadata for the module
     * @param $columnCount integer  number of columns that this module spans (1, 2, or 3)
     * @return array  the data to render
     * @override
     */
    public function getData($embed, $columnCount) {
        $itemCount = $embed->get('photoNum') == 0 && $embed->isOwnedByCurrentUser() ? 1: $embed->get('photoNum');
        $maxNumPhotos = $itemCount * $this->rowSize($embed, $columnCount);
        if (! $maxNumPhotos) { return array('items' => array(), 'itemsExist' => false); }
        $photoSet = $embed->get('photoSet');
        if ($photoSet == 'for_contributor') {
            $photosData = Photo_PhotoHelper::getSortedPhotos(XN_Profile::current(), array('contributor' => $embed->getOwnerName()), Photo_PhotoHelper::getMostRecentSortingOrder(), 0, $maxNumPhotos);
        } elseif ($photoSet == 'all') {
            $photosData = Photo_PhotoHelper::getSortedPhotos(XN_Profile::current(), null, Photo_PhotoHelper::getMostRecentSortingOrder(), 0, $maxNumPhotos);
        } elseif ($photoSet == 'promoted') {
            $photosData = Photo_PhotoHelper::getSortedPhotos(XN_Profile::current(), array('promoted' => true), null, 0, $maxNumPhotos);
        } elseif ($photoSet == 'popular') {
            $photosData = Photo_PhotoHelper::getSortedPhotos(XN_Profile::current(), null, Photo_PhotoHelper::getMostPopularSortingOrder(), 0, $maxNumPhotos);
        } elseif ($photoSet == 'owner') {
            $photosData = Photo_PhotoHelper::getSortedPhotos(XN_Profile::current(), array('contributor' => XN_Application::load()->ownerName), null, 0, $maxNumPhotos);
        } else {
            $album = Photo_AlbumHelper::load(str_replace('album_','',$photoSet), false);
            if (! $album) {
                $photosData = array();
            } else {
                $photosData = Photo_PhotoHelper::getSpecificPhotos(XN_Profile::current(),Photo_ContentHelper::ids($album, 'photos'),null,0,$maxNumPhotos);
            }
        }
        return array('items' => $photosData['photos'], 'itemsExist' => $photosData['numPhotos']);
    }


    /**
     * Outputs the body and footer of the module.
     *
     * @param $embed XG_Embed containing the state of the module
     * @param $columnCount number of columns that this module spans (1, 2, or 3)
     * @param $maxEmbedWidth  the maximum width for <embed>s, in pixels
     * @param $data array  data for the module to display
     * @param $controller Photo_EmbedController  the controller, used to render partials
     * @override
     */
    protected function outputModuleBodyAndFooter($embed, $columnCount, $maxEmbedWidth, $data, $controller) {
        if (! $data['itemsExist'] && $embed->getType() == 'profiles') { return; }
        if ($embed->get('photoNum') == 0 && XG_SecurityHelper::userIsAdmin(XN_Profile::current())) {
            $controller->renderPartial('fragment_footer', 'embed', array('embed' => $embed, 'viewAllUrl' => null,
                                                                         'addPhotosUrl' => $this->addPhotosUrl($embed, $data['itemsExist']))); ?>
        <?php
        } elseif ($noItemsHtml = $this->getNoItemsHtml($embed, $data)) {
            echo $noItemsHtml;
        } elseif ($data['itemsExist']) {
            W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_HtmlHelper.php');?>
            <div class="xg_module_body body_list">
                <?php
                $thumbnailSize = $this->thumbnailSize($embed, $columnCount);
                foreach (array_chunk($data['items'], $this->rowSize($embed, $columnCount)) as $row) { ?>
                    <ul class="clist">
                        <?php
                        $column = 0;
                        foreach ($row as $photo) {
                            echo '<li>';
							W_Content::create($photo)->render('listItem', array_merge(array('photo' => $photo, 'column' => $column++, 'showCreator' => $this->shouldShowCreator($embed), 'thumbnailSize' => $thumbnailSize, 'useLightfont' => 1)));
                            echo '</li>';
                        } ?>
                    </ul>
                <?php
                } ?>
            </div>
            <?php
            $controller->renderPartial('fragment_footer', 'embed', array('embed' => $embed, 'viewAllUrl' => $this->viewAllUrl($embed),
                                                                         'addPhotosUrl' => $this->addPhotosUrl($embed, $data['itemsExist'])));
        }
    }

    /**
     * Returns the number of photos per row.
     *
     * @param $embed XG_Embed  the object containing metadata for the module
     * @param $columnCount number of columns that this module spans (1, 2, or 3)
     * @return integer  the size of each row
     */
    protected function rowSize($embed, $columnCount) {
        if ($embed->getType() == 'profiles' && $columnCount == 1) { return 1; }
        if ($embed->getType() == 'homepage' && $columnCount == 1) { return 1; }
        if ($embed->getType() == 'profiles' && $columnCount == 2) { return 4; }
        if ($embed->getType() == 'homepage' && $columnCount == 2) { return 3; }
        throw new Exception('Shouldn\'t get here: ' . $embed->getType() . ', ' . $columnCount . ' (1780916692)');
    }

    /**
     * Returns the maximum size for thumbnails
     *
     * @param $embed XG_Embed  the object containing metadata for the module
     * @param $columnCount number of columns that this module spans (1, 2, or 3)
     * @return integer  the size of each row
     */
    protected function thumbnailSize($embed, $columnCount) {
        if ($embed->getType() == 'profiles' && $columnCount == 1) { return 139; }
        if ($embed->getType() == 'homepage' && $columnCount == 1) { return 139; }
        if ($embed->getType() == 'profiles' && $columnCount == 2) { return 124; }
        if ($embed->getType() == 'homepage' && $columnCount == 2) { return 139; }
        throw new Exception('Shouldn\'t get here (248569975)');
    }

    /**
     * Returns whether to display the name of the photo creator.
     *
     * @param $embed XG_Embed  the object containing metadata for the module
     * @return boolean  whether to show the name of the contributor
     */
    protected function shouldShowCreator($embed) {
        return $embed->getType() != 'profiles';
    }

}

/**
 * Logic for the albums module.
 */
class Photo_AlbumsEmbedType extends Photo_EmbedType {

    /**
     * Returns the data for the module to display.
     *
     * @param $embed XG_Embed  the object containing metadata for the module
     * @param $columnCount integer  number of columns that this module spans (1, 2, or 3)
     * @return array  the data to render
     * @override
     */
    public function getData($embed, $columnCount) {
        $itemCount = $embed->get('photoNum') == 0 && $embed->isOwnedByCurrentUser() ? 1: $embed->get('photoNum');
        $maxNumAlbums = $itemCount * $this->rowSize($embed, $columnCount);
        if (! $maxNumAlbums) { return array('items' => array(), 'itemsExist' => false); }
        $albumSet = $embed->get('albumSet');
        $filters = array();
        if ($embed->getType() === 'profiles') {
            $filters['owner'] = $embed->getOwnerName();
        }
        if ($albumSet == 'all') {
            $albumData = Photo_AlbumHelper::getSortedAlbums($filters, Photo_AlbumHelper::getMostRecentSortingOrder(), 0, $maxNumAlbums);
        } elseif ($albumSet == 'promoted') {
            $filters['promoted'] = true;
            $albumData = Photo_AlbumHelper::getSortedAlbums($filters, null, 0, $maxNumAlbums);
        } elseif ($albumSet == 'popular') {
            $albumData = Photo_AlbumHelper::getSortedAlbums($filters, Photo_AlbumHelper::getMostViewsSortingOrder(), 0, $maxNumAlbums);
        }
        return array('items' => $albumData['albums'], 'itemsExist' => $albumData['numAlbums']);
    }

    /**
     * Outputs the body and footer of the module.
     *
     * @param $embed XG_Embed containing the state of the module
     * @param $columnCount number of columns that this module spans (1, 2, or 3)
     * @param $maxEmbedWidth  the maximum width for <embed>s, in pixels
     * @param $data array  data for the module to display
     * @param $controller Photo_EmbedController  the controller, used to render partials
     * @override
     */
    protected function outputModuleBodyAndFooter($embed, $columnCount, $maxEmbedWidth, $data, $controller) {
        if ($embed->get('photoNum') == 0 && XG_SecurityHelper::userIsAdmin(XN_Profile::current())) {
            $controller->renderPartial('fragment_footer', 'embed', array('embed' => $embed, 'viewAllUrl' => null,
                                                                         'addPhotosUrl' => $this->addPhotosUrl($embed, $data['itemsExist'])));
        } elseif (! $data['itemsExist'] && $embed->get('albumSet') == 'promoted' && $embed->isOwnedByCurrentUser()) { ?>
            <div class="xg_module_body">
                <h3><%= xg_html('THERE_ARE_NO_FEATURED_ALBUMS') %></h3>
                <?php if (XG_SecurityHelper::userIsAdmin()) { ?>
                    <p><%= xg_html('START_FEATURING_X_CLICK_Y', 'href="' . xnhtmlentities(W_Cache::getWidget('main')->buildRelativeUrl('admin','featuring')) .'"') %></p>
                <?php } ?>
            </div>
        <?php
        } elseif (! $data['itemsExist'] && $embed->isOwnedByCurrentUser()) { ?>
            <div class="xg_module_body">
                <p><a href="<%= xnhtmlentities(W_Cache::getWidget('photo')->buildUrl('album', 'new')) %>" class="desc add"><%= xg_html('ADD_AN_ALBUM') %></a></p>
            </div>
        <?php
        } elseif ($data['itemsExist']) {
            W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_HtmlHelper.php');
            W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_AlbumHelper.php');
            $coverPhotos = Photo_AlbumHelper::getCoverPhotos($data['items']); ?>
            <div class="xg_module_body body_albums">
                <?php
                $i = 0;
                $thumbnailSize = $this->thumbnailSize($embed, $columnCount);
                foreach (array_chunk($data['items'], $this->rowSize($embed, $columnCount)) as $row) { ?>
                    <ul class="clist">
                        <?php
                        $column = 0;
                        foreach ($row as $album) {
                            echo '<li>';
							W_Content::create($album)->render('listItem', array_merge(array('album' => $album, 'i' => $i++, 'coverPhotos' => $coverPhotos, 'column' => $column++, 'showCreator' => $this->shouldShowCreator($embed), 'thumbnailSize' => $thumbnailSize, 'useLightfont' => 1)));
                            echo '</li>';
                        } ?>
                    </ul>
                <?php
                } ?>
            </div>
            <?php
            $controller->renderPartial('fragment_footer', 'embed', array('embed' => $embed, 'viewAllUrl' => $this->viewAllUrl($embed),
                                                                         'addPhotosUrl' => $this->addPhotosUrl($embed, $data['itemsExist'])));
        }
    }

    /**
     * Returns the number of albums per row.
     *
     * @param $embed XG_Embed  the object containing metadata for the module
     * @param $columnCount number of columns that this module spans (1, 2, or 3)
     * @return integer  the size of each row
     */
    protected function rowSize($embed, $columnCount) {
        return $columnCount == 2 ? 3 : 1;
    }

    /**
     * Returns the maximum size for thumbnails
     *
     * @param $embed XG_Embed  the object containing metadata for the module
     * @param $columnCount number of columns that this module spans (1, 2, or 3)
     * @return integer  the size of each row
     */
    protected function thumbnailSize($embed, $columnCount) {
        if ($embed->getType() == 'profiles' && $columnCount == 1) { return 165; }
        if ($embed->getType() == 'homepage' && $columnCount == 1) { return 165; }
        if ($embed->getType() == 'profiles' && $columnCount == 2) { return 165; }
        if ($embed->getType() == 'homepage' && $columnCount == 2) { return 139; }
        throw new Exception('Shouldn\'t get here (49014731)');
    }

    /**
     * Returns whether to display the name of the album creator.
     *
     * @param $embed XG_Embed  the object containing metadata for the module
     * @return boolean  whether to show the name of the contributor
     */
    protected function shouldShowCreator($embed) {
        return $embed->getType() != 'profiles';
    }

    /**
     * Returns the URL for the View All link
     *
     * @param $embed XG_Embed  the object containing metadata for the module
     * @return string  the URL
     */
    protected function viewAllUrl($embed) {
        return $embed->getType() == 'profiles' ? W_Cache::getWidget('photo')->buildUrl('album', 'listForOwner', array('screenName' => $embed->getOwnerName())) : W_Cache::getWidget('photo')->buildUrl('album', 'list');
    }

}


/**
 * Logic for embed types that display Photos.
 */
abstract class Photo_AbstractPhotoEmbedType extends Photo_EmbedType {

    /**
     * Returns HTML if no data exists.
     *
     * @param $embed XG_Embed containing the state of the module
     * @param $data array  data for the module to display
     * @return string  HTML indicating that no data exists, or possibly an empty string
     */
    protected function getNoItemsHtml($embed, $data) {
        ob_start();
        if (! $data['itemsExist'] && $embed->get('photoSet') == 'for_contributor' && $embed->isOwnedByCurrentUser()) { ?>
            <div class="xg_module_body">
                <h3><%= xg_html('YOU_HAVE_NOT_ADDED_PHOTOS') %></h3>
                <p><%= xg_html('ADD_PHOTOS_AND_SHARE') %></p>
                <p><a <%= XG_JoinPromptHelper::promptToJoin(W_Cache::getWidget('photo')->buildUrl('photo', XG_MediaUploaderHelper::action())) %> class="desc add"><%= xg_html('ADD_PHOTOS') %></a></p>
            </div>
        <?php
        } elseif (! $data['itemsExist'] && $embed->get('photoSet') == 'promoted' && $embed->isOwnedByCurrentUser()) { ?>
            <div class="xg_module_body">
                <h3><%= xg_html('THERE_ARE_NO_FEATURED_X', mb_strtolower('PHOTOS')) %></h3>
                <?php if (XG_SecurityHelper::userIsAdmin()) { ?>
                    <p><%= xg_html('START_FEATURING_X_CLICK_Y', 'href="' . xnhtmlentities(W_Cache::getWidget('main')->buildRelativeUrl('admin','featuring')) .'"') %></p>
                <?php } ?>
            </div>
        <?php
        } elseif (! $data['itemsExist'] && $embed->isOwnedByCurrentUser()) { ?>
            <div class="xg_module_body">
                <p><a <%= XG_JoinPromptHelper::promptToJoin(W_Cache::getWidget('photo')->buildUrl('photo', XG_MediaUploaderHelper::action())) %> class="desc add"><%= xg_html('ADD_PHOTOS') %></a></p>
            </div>
        <?php
        }
        $noItemsHtml = trim(ob_get_contents());
        ob_end_clean();
        return $noItemsHtml;
    }

    /**
     * Returns the URL for the View All link
     *
     * @param $embed XG_Embed  the object containing metadata for the module
     * @return string  the URL
     */
    protected function viewAllUrl($embed) {
        return $embed->getType() == 'profiles' ? W_Cache::getWidget('photo')->buildUrl('photo', 'listForContributor', array('screenName' => $embed->getOwnerName())) : W_Cache::getWidget('photo')->buildUrl('photo', 'list');
    }

}
