<?php

$categories = XN_Query::create('Content')->filter('owner')->filter('type','eic','Category')->execute();
foreach ($categories as $category) {
    print("category->id:[" . $category->id . "] has [");
    $alternativeIds = explode(' ', $category->my->alternativeIds);
    print(count($alternativeIds) . "] alternativeIds<br />\n");
    if (count($alternativeIds) > 1) {
        $altIdHash = array();
        foreach ($alternativeIds as $altId) {
            $altIdHash[$altId] = 1;
        }
        foreach ($alternativeIds as $categoryId) {
            $topicsQuery = XN_Query::create('Content')->filter('owner')->filter('type','eic','Topic')->filter('my->categoryId','eic',$categoryId)->alwaysReturnTotalCount(true);
            $topics = $topicsQuery->execute();
            $numTopics = $topicsQuery->getTotalCount();
            print("&nbsp;&nbsp;&nbsp;&nbsp;alterativeId:[$categoryId] has [" . $numTopics . "] associated topics<br />\n");
            if (($numTopics > 0) && ($categoryId !== $category->id)) {
                foreach ($topics as $topic) {
                    print("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;topicId:[" . $topic->id . "]<br />\n");
                    $topic->my->categoryId = $category->id;
                    $topic->save();
                }
            }
            if (($categoryId !== $category->id) && ($categoryId !== 'null')) {
                unset($altIdHash[$categoryId]);
            }
        }
        print("new alternativeIds:[" . implode(' ', array_keys($altIdHash)) . "]<br />\n");
        $category->my->alternativeIds = implode(' ', array_keys($altIdHash));
        $category->save();
    }
}

 ?>
