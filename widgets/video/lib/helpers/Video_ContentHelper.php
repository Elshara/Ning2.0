<?php

/**
 * Useful functions for working with queries and content objects. Includes a set of functions for working with timestamped comma-delimited strings.
 *
 * @see tests/ContentHelperTest.php
 */
class Video_ContentHelper {

    /**
     * Adds an ID to a (timestamped) comma-delimited string of IDs e.g. 33333 t1153633114 1, 54321 t1153633114 5, 12345 t1153633114 3.
     * In this example, there are 3 IDs, each with a timestamp and a payload (a rating). The payload is optional.
     *
     * @param $id A content ID; can also be a screen name
     * @param $value A simple payload to associate with the ID e.g. a rating. Optional. Preferably a number; must not contain commas
     * @param $limit Maximum number of IDs allowed in the comma-delimited string (FIFO)
     * @param $allowDuplicates Whether duplicate IDs are allowed; if not, the previous ID will be erased and the new one added to the end
     */
    public static function add($id, $object, $attributeName, $value=NULL, $limit=NULL, $allowDuplicates=FALSE) {
        $x = $object instanceof W_Model ? $object : $object->my;
        if (! $allowDuplicates) {
            self::remove($id, $object, $attributeName);
        }
        $x->$attributeName = self::normalize($x->$attributeName);
        if ($limit && self::count($object, $attributeName) >= $limit) {
            $x->$attributeName = preg_replace('/, [^,]*$/u', '', $x->$attributeName, 1);
        }
        // Newest first  [Jon Aquino 2006-07-22]
        $x->$attributeName = ', ' . $id . ' t' . time() . ($value ? ' ' . $value : '') . $x->$attributeName;
        $x->$attributeName = self::clean($x->$attributeName);
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
     * Returns the payload (e.g. a rating) for an ID in a (timestamped) comma-delimited string of IDs.
     * Payloads are optional.
     */
    public static function value($id, $object, $attributeName) {
        $x = $object instanceof W_Model ? $object : $object->my;
        return preg_match('/, ' . $id . ' t\d+ ([^,])*/u', self::normalize($x->$attributeName), $matches) ? $matches[1] : NULL;
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

    /**
     * Sorts the given objects according to the given attribute values.
     *
     * @param objects    The objects to sort; note that this array will be changed
     * @param attrValues The values to sort after
     * @param attrName   The name of the attribute
     */
    public static function sortByAttribute(&$objects, $attrValues, $attrName = 'id') {
        if (count($objects) > 0) {
            $comparator = new Video_ByAttributeComparator($attrName, $attrValues);
            usort($objects, array($comparator, 'compare'));
        }
    }



    private static function normalize($idList) {
        return $idList ? ', ' . $idList : $idList;
    }



    private static function clean($idList) {
        // Drop leading and trailing commas and spaces  [Jon Aquino 2006-07-22]
        return preg_replace('/^[, ]*/u', '', preg_replace('/[, ]*$/u', '', $idList));
    }



    /**
     * Works around the 100-result limit. Do not use this for queries that return
     * thousands of results!
     */
    public static function executeQueryWithoutLimit($query, $limit = 10000) {
        $query->alwaysReturnTotalCount(true);
        $resultArrays = array();
        do {
            $query->begin($start = count($resultArrays) == 0 ? 0 : $query->getResultTo());
            $query->end(min($start + 100, $limit));
            $resultArrays[] = $query->execute();
        } while ($query->getResultTo() < min($query->getTotalCount(), $limit));
        return self::concatenate($resultArrays);
    }



    public static function concatenate($arrays) {
        $newArray = array();
        foreach($arrays as $array) {
            foreach($array as $item) {
                $newArray[] = $item;
            }
        }
        return $newArray;
    }

    /**
     * @return null no content object with that id exists
     */
    public static function findByID($type, $id, $useWContent=TRUE) {
        $results = XG_Cache::content(array($id => $id));
        $content = $results[$id];
        if (is_null($content)) { throw new Exception("Shouldn't get here"); }
        $content = $useWContent ? W_Content::create($content) : $content;
        if ($content->type != $type) { throw new Exception("Shouldn't get here"); }
        return $content;
    }

}


class Video_ByAttributeComparator {
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
