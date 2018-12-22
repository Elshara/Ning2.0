<?php
// Renders a google map with the locations in the given photos.
//
// @param mapsKey   The Google Maps Key for the embedding page
// @param photos    The photos, some of which may not have locations
// @param mapWidth  The width of the google map
// @param mapHeight The height of the google map
// @param controls  (Optional) An array of controls; defaults to array('GSmallMapControl', 'GMapTypeControl')
// @param showThumb Whether to show a thumbnail of the photo in the marker; default is false
// @param clickAction 'callout', 'detailPage', or 'nothing'. Defaults to 'callout'.
//
// Use like this in a template:
//
// $this->renderPartial('fragment_locations',
//                      'photo',
//                      array('photos'       => $this->photosWithLocation,
//                            'mapsKey'      => XG_MapHelper::googleMapsApiKey(),
//                            'mapWidth'     => 220,
//                            'mapHeight'    => 220));

// Ning does not (yet) support the v2 API, so we're using JavaScript directly
// Also, since we're use PHP to build the JavaScript, the JavaScript is in the
// PHP file rather than in a .js file.
$this->_widget->includeFileOnce('/lib/helpers/Photo_HtmlHelper.php');
$clickAction = $clickAction ? $clickAction : 'callout';
?>
<div id="locationMap" style="width: <?php echo $mapWidth ?>px; height: <?php echo $mapHeight ?>px;"></div>
<?php /* TODO: Use XG_MapHelper::outputScriptTag() [Jon Aquino 2008-02-08] */ ?>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo $mapsKey ?>" type="text/javascript"></script>
<script language="JavaScript">
/* <![CDATA[ */
xg.addOnRequire(function() {
    if (GBrowserIsCompatible()) {
        var baseIcon = new GIcon();

        baseIcon.image            = "http://www.google.com/mapfiles/marker.png";
        baseIcon.shadow           = "http://www.google.com/mapfiles/shadow50.png";
        baseIcon.iconSize         = new GSize(20, 34);
        baseIcon.shadowSize       = new GSize(37, 34);
        baseIcon.iconAnchor       = new GPoint(9, 34);
        baseIcon.infoWindowAnchor = new GPoint(9, 2);
        baseIcon.infoShadowAnchor = new GPoint(18, 25);

        var map = new GMap2(dojo.byId('locationMap'));
        xg.photo.map = map;

        <?php
        if (!$controls) {
            $controls = array('GSmallMapControl', 'GMapTypeControl');
        }
        foreach ($controls as $control) {
        ?>
            map.addControl(new <?php echo $control ?>());
        <?php } ?>
        map.setCenter(new GLatLng(25, -40), 1);

        addMarker = function(map, latLng, text, id) {
            var marker = new GMarker(latLng);
            <?php
            if ($clickAction != 'nothing') { ?>
                GEvent.addListener(marker, "click", function() {
                    <?php
                    if ($clickAction == 'callout') { echo 'marker.openInfoWindowHtml(text);'; }
                    elseif ($clickAction == 'detailPage') { echo 'document.location.href = "/main/photo/show?id=" + id;'; } ?>
                });
            <?php
            } ?>
            map.addOverlay(marker);
        };

        <?php
        $photosWithLocation = array();
        foreach ($photos as $photo) {
            if ($photo->my->lat && (mb_strlen($photo->my->lat) > 0) &&
                $photo->my->lng && (mb_strlen($photo->my->lng) > 0)) {
                    $photosWithLocation[] = $photo;
                }
        }

        if (count($photosWithLocation) > 0) {
        ?>
        var bounds = new GLatLngBounds();
        var latLng;

        <?php
        $photo       = null;
        $photoUrl    = $this->_buildUrl('photo', 'show') . "?id=";
        $userUrl     = $this->_buildUrl('photo', 'listForContributor') . "?screenName=";
        $thumbWidth  = 92; // same size as on the n-column grid (for caching)
        $thumbHeight = 92;
        foreach ($photosWithLocation as $photo) {
            $photoTitle    = $photo->title && (mb_strlen($photo->title) > 0) ? xnhtmlentities($photo->title) : '[untitled]';
            $markerContent = "<div class='marker'>" .
                             "<a href='" . $photoUrl . $photo->id . "' alt='" . $photoTitle . "' title='" . $photoTitle . "'>" . $photoTitle . '</a><br/>';
            if ($showThumb) {
                $imgUrl    = null;
                $imgWidth  = null;
                $imgHeight = null;
                Photo_HtmlHelper::fitImageIntoThumb($photo, $thumbWidth, $thumbHeight, $imgUrl, $imgWidth, $imgHeight);

                $markerContent = $markerContent .
                                 "<img src='" . $imgUrl . "' alt='" . $photoTitle . "' title='" . $photoTitle . "' width='" . $imgWidth . "' height='" . $imgHeight . "'/><br/>";
            }
            $markerContent = $markerContent .
                              xg_html('BY_X', "<a href='" . $userUrl . xnhtmlentities($photo->contributorName) . "''>" . xnhtmlentities(Photo_FullNameHelper::fullName($photo->contributorName)) . '</a>');
            if ($photo->my->address) {
                $markerContent = $markerContent . '<br/>' . xg_html('LOCATION') . ' ' . xnhtmlentities($photo->my->address);
            }
            $markerContent = $markerContent . "</div>";
        ?>
        latLng = new GLatLng(<?php echo $photo->my->lat ?>, <?php echo $photo->my->lng ?>);
        bounds.extend(latLng);
        addMarker(map, latLng, "<?php echo $markerContent ?>", "<%= $photo->id %>");
        <?php } ?>
        <?php if (count($photosWithLocation) > 1) { ?>
            map.setZoom(map.getBoundsZoomLevel(bounds)-1);
        <?php } else { ?>
            map.setZoom(<?php echo $photo->my->locationInfo ?>);
        <?php } ?>

        map.setCenter(bounds.getCenter());
        <?php } ?>
    }
	dojo.addOnUnload(function() {
		if (GBrowserIsCompatible()) {
			GUnload();
		}
	});
});
/* ]]> */
</script>
