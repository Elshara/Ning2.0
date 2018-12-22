<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script src="/test/js/lib/Test/Builder.js"></script>
<script src="/test/js/lib/Test/More.js"></script>
<script src="/xn_resources/widgets/lib/js/jquery/jquery.js"></script>
<script src="/xn_resources/widgets/lib/js/dojo-adapter/src/core.js?x=<%= mt_rand() %>"></script>
<script src="/xn_resources/widgets/lib/js/dojo-adapter/src/graphics.js?x=<%= mt_rand() %>"></script>
</head>
<body style="margin:0;padding:0;">
<pre id="test">
<script type="text/javascript">
    window.x$ = jQuery.noConflict(true)
    plan({ tests: 5 });

    is(dojo.graphics.color.extractRGB('red').toString(), [255, 0, 0].toString());
    is(dojo.graphics.color.extractRGB('foo').toString(), [255, 255, 255].toString());
    is(dojo.graphics.color.extractRGB('rgb(1, 2, 3)').toString(), [1, 2, 3].toString());
    is(dojo.graphics.color.extractRGB('#0000ff').toString(), [0, 0, 255].toString());
    is(dojo.graphics.color.extractRGB('00FF00').toString(), [0, 255, 0].toString());

</script>
</pre>
</body>
</html>
