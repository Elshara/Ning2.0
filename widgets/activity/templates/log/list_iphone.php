<?php XG_IPhoneHelper::header('activity', NULL, NULL, array('metaDescription' => $this->metaDescription, 'metaKeywords' => $this->metaKeywords)); ?>
<ul class="list activity">
<?php
XG_IPhoneHelper::previousPage($this->pageSize);
W_Cache::getWidget('activity')->includeFileOnce('/lib/helpers/Activity_LogHelperIPhone.php');
$i = 0;
foreach ($this->logItems as $item) {
	$text = Activity_LogHelperIPhone::renderItem($item);
	if ($text) {
		echo $text;
		if (++$i >= $this->pageSize) {
			break;
		}
	}
}
XG_IPhoneHelper::nextPage($this->showNextLink, $this->pageSize);
?> </ul> <?php
xg_footer(NULL,array('regularPage' => W_Cache::getWidget('main')->buildUrl('index','index')));
?>