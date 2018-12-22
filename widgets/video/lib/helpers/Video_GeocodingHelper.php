<?php
/**
 * Deals with geocoding using the internal geocoding. Is used when Google's
 * Geocoder can't (or won't) geocode an address.
 *
 * <pre>
 *      $geocoderResult = Video_GeocodingHelper::geocode('1600 Pennsylvania Ave, Washington, DC');
 *      echo $geocoderResult['lat'];
 *      echo $geocoderResult['long']; 
 * </pre> 
 *
 * Borrowed from the eBay Motors - Mapped app (http>//ebaymotorsmapped.ning.com)
 */
class Video_GeocodingHelper {
    const GEO_URL = 'http://geoc0.ninginc.com:8080/geo';

    /**
     * Given an address or intersection, return its latitude and longitude. Examples of addresses:
     * <ul>
     * <li>1600 Pennsylvania Ave, Washington, DC</li>
     * <li>3601 Lyon Street, 94123</li>
     * <li>Angela and Simonton, Key West, FL</li>
     * </ul>
     *
     * @param $address An address or intersection, followed by city and state, or zip code.
     * @return An array that may contain 'lat', 'lng'
     */
    public static function geocode($address) {
        $url = self::GEO_URL . '?location=' . urlencode($address);
        $xml = @simplexml_load_file($url);
        if ($xml && $xml->result && $xml->result->latitude && $xml->result->longitude) {
            $lat = (float) $xml->result->latitude;
            $lng = (float) $xml->result->longitude;
            return array('lat' => $lat, 'lng' => $lng);
        } else {
            return array();
        }
    }
}
