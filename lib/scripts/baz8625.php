<?php
// set BAZ-8625 for details.
$q = XN_Query::create('Content')
	->filter('owner')
	->filter('type','=','User')
	->filter('createdDate','>','2008-07-25T06:00:00Z')
	->filter(XN_Filter::any(
		XN_Filter('my.xg_index_status','=',null),
		XN_Filter('my.xg_index_status','=','')
	))
	->end(1)
	->alwaysReturnTotalCount(TRUE);
$q->execute();
echo $q->getTotalCount(),"\n";
?>
