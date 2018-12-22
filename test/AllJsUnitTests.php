<!-- Haven't been able to get this to work yet. Running individual tests works though. [Jon Aquino 2008-08-25] -->

<html>
<head>
<script src="/test/js/lib/Test/Builder.js"></script>
<script src="/test/js/lib/Test/More.js"></script>
<script src="/test/js/lib/Test/Harness.js"></script>
<script src="/test/js/lib/Test/Harness/Browser.js"></script>
</head>
<body>
<?php
require $_SERVER['DOCUMENT_ROOT'] . '/test/XG_TestHelper.php';
$paths = array();
foreach (XG_TestHelper::globr($_SERVER['DOCUMENT_ROOT'] . '/test/js', '*Test.html') as $path) {
    $path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $path);
    $paths[] = "'$path'";
}
?>
<script>
  Test.Harness.Browser.runTests(<%= implode(',', $paths) %>);
</script>
</body>
</html>
