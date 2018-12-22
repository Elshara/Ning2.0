<?php
/**
 * Renders the common part of the make/edit album pages.
 *
 * @param $album  The album to edit (optional)
 * @param $albumPhotos  The photos in the album (optional)
 * @param $removedPhotoCount  The number of photos that have been removed from the album (optional)
 */
$numAlbumPages = 5;
$numRows = 5;
$numColumns = 4;

$slotWidth = 70;
$slotHeight = 70;
$insertDivWidth = 10;
$rowDistance = 10;

$this->renderPartial('fragment_photosRemovedMessage', 'album', array('numPhotos' => $removedPhotoCount, 'editForm' =>true)); ?>

<div class="xg_module">
    <div class="xg_module_body pad">
        <fieldset class="block left" style="margin-top:0">
            <div class="legend"><%= xg_html('CHOOSE_PHOTOS') %></div>
            <div class="album_head">
                <ul>
                    <li><%= xg_html('ADD_PHOTOS_BY_DRAGGING') %></li>
                    <li><%= xg_html('TITLE_YOUR_ALBUM') %></li>
                    <li><strong><%= xg_html('SAVE_YOUR_ALBUM') %></strong></li>
                </ul>
                <form id="searchForm">
                <dl>
                    <dt><%= xg_html('CHOOSE_FROM') %></dt>
                    <dd><label><input type="radio" class="radio" id="myPhotosRadioButton" checked="checked" name="photoOrigin" value="my" /><%= xg_html('MY_PHOTOS') %></label> <label><input type="radio" class="radio" id="allPhotosRadioButton" name="photoOrigin" value="all" /><%= xg_html('EVERYONES_PHOTOS') %></label></dd>
                    <dt><label for="photoTags"><%= xg_html('TAGGED') %></label></dt>
                    <dd><input class="textfield" id="photoTags" name="photoTags" type="text" /> <input type="submit" class="button" value="<%= xg_html('FILTER') %>" /></dd>
                </dl>
                </form>
            </div>
        </fieldset>

        <fieldset class="block right" style="margin-top:0">
            <div class="legend"><%= xg_html('YOUR_ALBUM') %></div>
            <div class="album_head">
                <p class="album_cover left" style="background-image:url()"><span id="albumCover"><%= xg_html('DRAG_ALBUM_COVER_HERE') %></span></p>
                <p class="right">
                    <label for="albumTitle"><%= xg_html('ALBUM_TITLE') %></label><br />
                    <input id="albumTitle" type="text" class="textfield" size="28" style="width:215px" value="<?php if ($album) { echo xnhtmlentities($album->title); } ?>" />
                    <br />
                    <label for="albumDescription"><%= xg_html('DESCRIPTION_COLON') %></label><br />
                    <textarea id="albumDescription" cols="25" rows="4" style="width:215px;"><?php if ($album) { echo xnhtmlentities($album->description); } ?></textarea>
                </p>
            </div>
        </fieldset>




        <div class="clear">
            <div class="block left">
                <div id="availablePhotos" class="album_well">
                    <?php
                    for ($rowIdx = 0; $rowIdx < $numRows; $rowIdx++) { ?>
                        <?php for ($columnIdx = 0; $columnIdx < $numColumns; $columnIdx++) { ?><div class="available"></div><?php } ?>
                    <?php
                    } ?>
                </div>
                <ul class="nobullets">
                    <li class="left"><a id="olderAvailablePhotos" href="#">&#171; <%= xg_html('OLDER_PHOTOS') %></a></li>
                    <li class="right"><a id="newerAvailablePhotos" href="#"><%= xg_html('MORE_RECENT_PHOTOS') %>&nbsp;&#187;</a></li>
                </ul>
            </div>

            <div class="block right">
                <div id="album" class="album_well">
                    <?php
                    $slotIdx = 0;
                    for ($pageIdx = 0; $pageIdx < $numAlbumPages; $pageIdx++) { ?>
                        <div id="albumPage<?php echo $pageIdx ?>"<?php if ($pageIdx > 0) { ?>  style="display: none;"<?php } ?>>
                            <p class="instruction"><%= xg_html('DRAG_PHOTOS_HERE') %></p>
                            <?php
                            for ($rowIdx = 0; $rowIdx < $numRows; $rowIdx++) {
                                for ($columnIdx = 0; $columnIdx < $numColumns; $columnIdx++) { ?>
                                    <div class="target">
                                        <div class="insertSlot" id="insertSlot<?php echo $slotIdx ?>"></div>
                                        <div class="slot" id="slot<?php echo $slotIdx ?>"></div>
                                    </div>
                                    <?php
                                    $slotIdx++;
                                }
                            } ?>
                        </div>
                    <?php
                    } ?>
                </div>
                <ul class="nobullets">
                    <li class="left"><a id="prevPage" href="javascript:void(0)">&#171; <%= xg_html('PREVIOUS_ALBUM_PAGE') %></a></li>
                    <li class="right"><a id="nextPage" href="javascript:void(0)"><%= xg_html('NEXT_ALBUM_PAGE') %>&nbsp;&#187;</a></li>
                </ul>
            </div>
        </div>

        <p class="buttongroup">
            <input class="button button-primary" id="submitAlbumButton" type="button" value="<%= xg_html('SAVE') %>" />
            <input class="button" id="cancelAlbumButton" type="button" value="<%= xg_html('CANCEL') %>" />
        </p>
    </div>
</div>

<?php XG_App::ningLoaderRequire('xg.photo.album.edit'); ?>
<script type="text/javascript">
/* <![CDATA[ */
xg.addOnRequire(function() {

    dojo.event.connect(dojo.byId('cancelAlbumButton'), 'onclick', dojo.lang.hitch(this, function(event) {
        dojo.event.browser.stopEvent(event);
        document.location.href =  '<?php echo $this->_buildUrl("album", "listForOwner", array("screenName" => $this->_user->screenName)) ?>';
    }));

    var albumEditor = new xg.photo.album.edit.AlbumEditor(dojo.byId('availablePhotos'),
            dojo.byId('album'),
            <?php echo $numAlbumPages ?>, <?php echo $numRows ?>, <?php echo $numColumns ?>,
            <?php echo $slotWidth ?>, <?php echo $slotHeight ?>, <?php echo $insertDivWidth ?>, <?php echo $rowDistance ?>,
            <?php if ($album) { echo "'" . $album->id . "'"; } else { echo "null"; } ?>,
            dojo.byId('albumTitle'),
            dojo.byId('albumDescription'),
            dojo.byId('albumCover'),
            dojo.byId('submitAlbumButton'),
            '<?php echo $this->_buildUrl("album", "saveAlbum") ?>',
            '<?php echo $this->_buildUrl("album", "listForOwner", array("screenName" => $this->_user->screenName)) ?>');
    <?php
    $this->_widget->includeFileOnce('/lib/helpers/Photo_HtmlHelper.php');
    if ($album) {
        foreach ($albumPhotos as $photo) {
            Photo_HtmlHelper::getImageUrlAndDimensions($photo, $imgUrl, $width, $height);
            $imgUrl = Photo_HtmlHelper::addParamToUrl($imgUrl, 'width', $slotWidth);
            $imgUrl = Photo_HtmlHelper::addParamToUrl($imgUrl, 'height', $slotHeight);
            if ($album->my->coverPhotoId == $photo->id) {
                $coverPhoto  = $photo;
                $coverImgUrl = $imgUrl;
            } ?>
            albumEditor.addNewPhoto(albumEditor.createImageObj('<?php echo $photo->id ?>',
                    '<?php echo $imgUrl ?>',
                    '<?php echo xnhtmlentities($photo->title) ?>',
                    <?php echo $slotWidth ?>,
                    <?php echo $slotHeight ?>));
        <?php
        }
        if ($coverPhoto) { ?>
            albumEditor.setCoverImg('<?php echo $coverPhoto->id ?>',
                    albumEditor.createImageObj('<?php echo $coverPhoto->id ?>',
                        '<?php echo $coverImgUrl ?>',
                        '<?php echo xnhtmlentities($coverPhoto->title) ?>',
                        <?php echo $slotWidth ?>,
                        <?php echo $slotHeight ?>));
        <?php
        }
    } ?>
    albumEditor.setPrevPageLink(dojo.byId('prevPage'));
    albumEditor.setNextPageLink(dojo.byId('nextPage'));
    var availableHandler = new xg.photo.album.edit.AvailablePhotosHandler('<?php echo $this->_buildUrl("album", "getAvailablePhotos") ?>',
            dojo.byId('searchForm'),
            dojo.byId('myPhotosRadioButton'),
            dojo.byId('allPhotosRadioButton'),
            dojo.byId('photoTags'),
            dojo.byId('newerAvailablePhotos'),
            dojo.byId('olderAvailablePhotos'),
            albumEditor);

    // initial loading of available photos
    availableHandler.doSearch(1, albumEditor);
});
/* ]]> */
</script>