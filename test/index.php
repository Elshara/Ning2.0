<?php if (! XN_Profile::current()->isOwner()) { die("App Owner Only"); } ?>

<h2>Tests</h2>

<ul>
<li><a href='AllUnitTests.php'>Run ALL Unit Tests</a></li>
<li><a href='GroupedUnitTests.php'>Run GROUPS of Unit Tests</a></li>
<?php
foreach (glob('./*Test.php') as $testFile) {
    $link = xnhtmlentities($testFile);
    $name = xnhtmlentities(preg_replace('@^\./(.*)\Test.php$@','$1 Test',$testFile));
    echo "<li><a href='$link'>$name</a></li>\n";
}
foreach (glob('./*', GLOB_ONLYDIR) as $dirName) {
    echo "<br/><br/><h2>$dirName</h2><br/>";
    foreach (glob("./$dirName/*Test.php") as $testFile) {
        $link = xnhtmlentities($testFile);
        $name = xnhtmlentities(preg_replace('@^\./(.*)\Test.php$@','$1 Test',$testFile));
        echo "<li><a href='$link'>$name</a></li>\n";
    }
}
?>
</ul>

