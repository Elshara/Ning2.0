<pre>
<?php
/**
 * Lists all Shapes in the current network. Called by diffShapes.php.
 */

function compareByName($a, $b) {
    if ($a->name == $b->name) { return 0; }
    return ($a->name < $b->name) ? -1 : 1;
}

function sortByName($array) {
    usort($array, 'compareByName');
    return $array;
}

function debugStringForShape($shape) {
    $str = "XN_Shape:\n";
    $str .= '  name [' . $shape->name . "]\n";
    $str .= '  mimetype [' . $shape->mimetype . "]\n";
    $str .= '  searchable [' . ($shape->searchable ? 'true' : 'false') . "]\n";
    $str .= "  attributes:\n";
    if (! $_GET['skipAttributes']) {
        foreach (sortByName($shape->attributes) as $attribute) {
            if (in_array($attribute->name, array('my.test', 'my.testUrl'))) { continue; }
            $str .= '    ' . debugStringForAttribute($attribute) . "\n";
        }
    }
    return $str;
}

function debugStringForAttribute($attribute) {
    $str = $attribute->name . '=' . $attribute->type;
    if ($attribute->indexing !== 'ignored') { $str .= ', ' . $attribute->indexing; }
    return $str;
}

$shapes = XN_Shape::load();
foreach (sortByName($shapes) as $shape) {
        echo debugStringForShape($shape);
}
