<?php
// listColumnProper is called in the template rather than the action, to work with setCaching() [Jon Aquino 2008-01-22]
foreach ($this->listColumnProperArgs as $key => $value) { $this->{$key} = $value; }
$this->renderPartial('listColumnProper', 'friend');
// renderPartial() is more efficient than dispatch() [Jon Aquino 2008-01-22]
