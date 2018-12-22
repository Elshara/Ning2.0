<?php
// listColumnProper_iphone is called in the template rather than the action, to work with setCaching()
foreach ($this->listColumnProperArgs as $key => $value) { $this->{$key} = $value; }
$this->renderPartial('listColumnProper_iphone', 'friend');
// renderPartial() is more efficient than dispatch() [Jon Aquino 2008-01-22]