<?php

class Music_ContentHelper {

    /**
     * Sorts the given objects according to the given attribute values.
     *
     * @param objects    The objects to sort; note that this array will be changed
     * @param attrValues The values to sort after
     * @param attrName   The name of the attribute
     */
    public static function sortByAttribute(&$objects, $attrValues, $attrName = 'id') {
        if (count($objects) > 0) {
            $comparator = new Track_ByAttributeComparator($attrName, $attrValues);
            usort($objects, array($comparator, 'compare'));
        }
    }

    /**
     * Adds an ID to a (timestamped) comma-delimited string of IDs e.g. 33333 t1153633114 1, 54321 t1153633114 5, 12345 t1153633114 3.
     * In this example, there are 3 IDs, each with a timestamp and a payload (a rating). The payload is optional.
     *
     * @param $id A content ID; can also be a screen name
     * @param $value A simple payload to associate with the ID e.g. a rating. Optional. Preferably a number; must not contain commas
     * @param $limit Maximum number of IDs allowed in the comma-delimited string (FIFO)
     * @param $allowDuplicates Whether duplicate IDs are allowed; if not, the previous ID will be erased and the new one added to the end
     */
    public static function add($id, $object, $attributeName, $value = null, $limit = null, $allowDuplicates = false, $newestFirst = true) {
        $x = $object instanceof W_Model ? $object : $object->my;
        if (! $allowDuplicates) {
            self::remove($id, $object, $attributeName);
        }
        $x->$attributeName = self::normalize($x->$attributeName);
        if ($limit && self::count($object, $attributeName) >= $limit) {
            $x->$attributeName = preg_replace('/, [^,]*$/u', '', $x->$attributeName, 1);
        }
        if ($newestFirst) {
            $x->$attributeName = ', ' . $id . ' t' . time() . ($value ? ' ' . $value : '') . $x->$attributeName;
            $x->$attributeName = self::clean($x->$attributeName);
        } else {
            $x->$attributeName = $x->$attributeName . ', ' . $id . ' t' . time() . ($value ? ' ' . $value : '');
            $x->$attributeName = self::clean($x->$attributeName);
        }
    }



    /**
     * Removes an ID from a (timestamped) comma-delimited string of IDs.
     */
    public static function remove($id, $object, $attributeName) {
        $x = $object instanceof W_Model ? $object : $object->my;
        $x->$attributeName = self::normalize($x->$attributeName);
        $x->$attributeName = preg_replace('/, ' . $id . '[^,]*/u', '', $x->$attributeName);
        $x->$attributeName = self::clean($x->$attributeName);
    }

    /**
     * Changes the (timestamped) comma-delimited string of IDs so that it only contains the specifieed new ids,
     * in the same order as these new ids. If an id is new, then it will be added (i.e. new timestamp). If it
     * is already in the attribute value, then the original timestamp will be preserved. If changedValues is
     * specified, then its values will be used for new and existing ids.
     */
    public static function changeTo($object, $attributeName, $changedIds, $changedValues, $limit = null) {
        $x = $object instanceof W_Model ? $object : $object->my;
        preg_match_all('/, ([^ ]+) t(\d+)/u', self::normalize($x->$attributeName), $matches, PREG_PATTERN_ORDER);

        $newAttrValue = "";
        for ($idx = 0; ($idx < count($changedIds)) && (!$limit || ($idx < $limit)); $idx++) {
            $id    = $changedIds[$idx];
            $value = $changedValues ? ' ' . $changedValues[$idx] : '';
            if (($matchIdx = array_search($id, $matches[1])) === false) {
                // new id
                $ts = time();
            } else {
                $ts = $matches[2][$matchIdx];
            }
            $newAttrValue = $newAttrValue . ', ' . $id . ' t' . $ts . $value;
        }
        $x->$attributeName = self::clean($newAttrValue);
    }

    /**
     * Returns the payload (e.g. a rating) for an ID in a (timestamped) comma-delimited string of IDs.
     * Payloads are optional.
     */
    public static function value($id, $object, $attributeName) {
        $x = $object instanceof W_Model ? $object : $object->my;
        return preg_match('/, ' . $id . ' t\d+ ([^,])*/u', self::normalize($x->$attributeName), $matches) ? $matches[1] : NULL;
    }

    /**
     * Returns the timestamp of the given ID if it exists in the (timestamped) comma-delimited string of IDs
     */
    public static function timestamp($id, $object, $attributeName) {
        $x = $object instanceof W_Model ? $object : $object->my;
        if (preg_match('/, ' . $id . ' t(\d+)/u', self::normalize($x->$attributeName), $matches)) {
            return $matches[1];
        } else {
            return null;
        }
    }

    /**
     * Returns the number of IDs in a (timestamped) comma-delimited string of IDs.
     */
    public static function count($object, $attributeName) {
        $x = $object instanceof W_Model ? $object : $object->my;
        return mb_substr_count(self::normalize(self::clean($x->$attributeName)), ',');
    }

    /**
     * Returns whether an ID exists in a (timestamped) comma-delimited string of IDs
     */
    public static function has($id, $object, $attributeName) {
        $x = $object instanceof W_Model ? $object : $object->my;
        return preg_match('/, ' . $id . ' t\d+/u', self::normalize($x->$attributeName), $matches);
    }

    /**
     * Returns an array of ids extracted from a (timestamped) comma-delimited string of IDs.
     */
    public static function ids($object, $attributeName) {
        $x = $object instanceof W_Model ? $object : $object->my;
        preg_match_all('/, ([^ ]+) /u', self::normalize($x->$attributeName), $matches, PREG_PATTERN_ORDER);
        return $matches[1];
    }

    /**
     * Returns an array of timestamps extracted from a (timestamped) comma-delimited string of IDs.
     * The order will be the same as the one returned by the ids function.
     */
    public static function timestamps($object, $attributeName) {
        $x = $object instanceof W_Model ? $object : $object->my;
        preg_match_all('/, ([^ ]+) t(\d+)/u', self::normalize($x->$attributeName), $matches, PREG_PATTERN_ORDER);
        return $matches[2];
    }

    
    private static function normalize($idList) {
        return $idList ? ', ' . $idList : $idList;
    }

    private static function clean($idList) {
        // Drop leading and trailing commas and spaces  [Jon Aquino 2006-07-22]
        return preg_replace('/^[, ]*/u', '', preg_replace('/[, ]*$/u', '', $idList));
    }


}


class Track_ByAttributeComparator {
    private $attrName;
    private $attrValues;
    public function __construct($attrName, $attrValues) {
        $this->attrName   = $attrName;
        $this->attrValues = $attrValues;
    }
    function compare($objA, $objB) {
        $attrName = $this->attrName;
        $idxA     = array_search($objA->$attrName, $this->attrValues);
        $idxB     = array_search($objB->$attrName, $this->attrValues);

        return $idxA == $idxB ? 0 : ($idxA < $idxB ? -1 : 1);
    }
}