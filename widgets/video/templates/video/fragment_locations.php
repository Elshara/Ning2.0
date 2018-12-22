<?php
// Renders a google map with the locations in the given videos.
//
// @param mapsKey     The Google Maps Key for the embedding page
// @param videos      The videos with locations
// @param mapWidth    The width of the google map
// @param mapHeight   The height of the google map
// @param controls    (Optional) An array of controls; defaults to array('GSmallMapControl', 'GMapTypeControl')
// @param clickAction 'callout', 'detailPage', or 'nothing'. Defaults to 'callout'.
//
// Use like this in a template:
//
// $this->renderPartial('fragment_locations',
//                      'video',
//                      array('videos'       => $this->videos,
//                            'mapsKey'      => XG_MapHelper::googleMapsApiKey(),
//                            'mapWidth'     => 220,
//                            'mapHeight'    => 220));

// Ning does not (yet) support the v2 API, so we're using JavaScript directly
// Also, since we're use PHP to build the JavaScript, the JavaScript is in the
// PHP file rather than in a .js file.

$clickAction = $clickAction ? $clickAction : 'callout';
?>
<?php /* TODO: Use XG_MapHelper::outputScriptTag() [Jon Aquino 2008-02-08] */ ?>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo $mapsKey ?>" type="text/javascript"></script>
<div id="locationMap" style="width: <?php echo $mapWidth ?>px; height: <?php echo $mapHeight ?>px;"></div>

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
        xg.video.map = map;

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
                    elseif ($clickAction == 'detailPage') { echo 'document.location.href = "/main/video/show?id=" + id;'; } ?>
                });
            <?php
            } ?>
            map.addOverlay(marker);
        };

        <?php

        if (count($videos) > 0) { ?>
            var bounds = new GLatLngBounds();
            var latLng;

            <?php
            $video    = null;
            $videoUrl = $this->_buildUrl('video', 'show') . "?id=";
            foreach ($videos as $video) {
                $markerContent = "<div class='marker'>" .
                                 "<h1><a href='" . $videoUrl . $video->id . "'>" . xnhtmlentities(Video_HtmlHelper::excerpt($video->title, 50)) . '</a></h1>';
                if ($video->my->address) {
                    $markerContent = $markerContent . "<p class='address'>" . xnhtmlentities($video->my->address) . '</p>';
                }
                $markerContent .= '<p class="attribution">' . xg_html('BY_X', Video_HtmlHelper::linkedScreenName($video->contributorName, FALSE, FALSE)) . "</p></div>";
                ?>
                latLng = new GLatLng(<?php echo $video->my->lat ?>, <?php echo $video->my->lng ?>);
                bounds.extend(latLng);
                addMarker(map, latLng, "<%= str_replace('"', '\\"', $markerContent) %>", "<%= $video->id %>");
                <?php
            }
            if (count($videos) > 1) { ?>
                map.setZoom(map.getBoundsZoomLevel(bounds)-1);
            <?php
            } else { ?>
                map.setZoom(<?php echo $video->my->locationInfo ?>);
            <?php
            } ?>

            map.setCenter(bounds.getCenter());
        <?php
        } ?>
    }
	dojo.addOnUnload(function() {
		if (GBrowserIsCompatible()) {
			GUnload();
		}
	});
});

/* ]]> */
</script>