<?php
$pubDate = strtotime($this->logItems[0]->createdDate);

$json = new NF_JSON(); 
    //_numoptionsjson="<%= xnhtmlentities($json->encode($this->num_options))%
    // $value = array('foo', 'bar', array(1, 2, 'baz'), array(3, array(4)));
$dataArray = array();
//      $output = $json->encode($value);

//array_push($dataArray, array('count', sizeof($this->logItems)));
//array_push($dataArray, 'count' => sizeof($this->logItems));
$dataArray['total'] = sizeof($this->logItems);
$dataArray['values'] = array();
    foreach($this->logItems as $item){
    	$jsonItem = array();
        ob_start();
        $this->renderPartial('fragment_logItem', 'log', array('item' => $item, 'isProfile' => false , 'fmt' => 'rss', ));
        $htmldescription = trim(ob_get_contents());
        ob_end_clean();
        $itemDate = strtotime($item->createdDate);
        $jsonItem['created'] = date('r',$itemDate);
     	$jsonItem['id'] = $item->id;
        $jsonItem['title'] = xg_xmlentities($item->title);
        $jsonItem['link'] = $item->my->link;
        $jsonItem['description'] = xnhtmlentities($htmldescription);
        array_push($dataArray['values'], $jsonItem);
            
    }
echo $json->encode($dataArray);

?>