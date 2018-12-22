<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script src="/test/js/lib/Test/Builder.js"></script>
<script src="/test/js/lib/Test/More.js"></script>
<script src="/xn_resources/widgets/lib/js/jquery/jquery.js"></script>
<script src="/xn_resources/widgets/lib/js/dojo-adapter/src/core.js?x=<%= mt_rand() %>"></script>
<script src="/xn_resources/widgets/lib/js/dojo-adapter/src/lang.js?x=<%= mt_rand() %>"></script>
<script src="/xn_resources/widgets/shared/js/util.js?x=<%= mt_rand() %>"></script>
</head>
<body style="margin:0;padding:0;">
<pre id="test">
<script type="text/javascript">
    window.x$ = jQuery.noConflict(true)
    plan({ tests: 22 });

    /** xg.shared.util.removeParameter */
    var url = 'http://foo.com/bar/';
    is(xg.shared.util.removeParameter(url, 'bar'), url);
    url = 'http://foo.com/bar/?a=1&b=2&c=d';
    is(xg.shared.util.removeParameter(url, 'd'), url);
    is(xg.shared.util.removeParameter(url, 'a'), 'http://foo.com/bar/?b=2&c=d');
    is(xg.shared.util.removeParameter(xg.shared.util.removeParameter(url, 'c'), 'a'), 'http://foo.com/bar/?b=2');
    url = 'http://foo.com/bar?a=1';
    is(xg.shared.util.removeParameter(url, 'b'), url);
    is(xg.shared.util.removeParameter(url, 'a'), 'http://foo.com/bar');
    url = 'http://foo.com/bar?a=1&a=2&a=3&b=1';
    is(xg.shared.util.removeParameter(url, 'b'), 'http://foo.com/bar?a=1&a=2&a=3');
    is(xg.shared.util.removeParameter(url, 'a'), 'http://foo.com/bar?b=1');

    /** xg.shared.util.parseFormattedNumber */
    is(xg.shared.util.parseFormattedNumber('1'), 1);
    is(xg.shared.util.parseFormattedNumber('1.1'), 11);
    is(xg.shared.util.parseFormattedNumber('1,234'), 1234);
    is(xg.shared.util.parseFormattedNumber('5,123,444'), 5123444);
    is(xg.shared.util.parseFormattedNumber('5,123,444.00'), 512344400);

    /** xg.shared.util.formatNumber */
    is(xg.shared.util.formatNumber(1, ','), '1');
    is(xg.shared.util.formatNumber(999, ','), '999');
    is(xg.shared.util.formatNumber(-456, ','), '-456');
    is(xg.shared.util.formatNumber(1000, ','), '1,000');
    is(xg.shared.util.formatNumber(1000, '@'), '1@000');
    is(xg.shared.util.formatNumber(1234567, ','), '1,234,567');
    is(xg.shared.util.formatNumber(12345678, ','), '12,345,678');
    is(xg.shared.util.formatNumber(123456789, ','), '123,456,789');
    is(xg.shared.util.formatNumber(-12345678, ','), '-12,345,678');

</script>
</pre>
</body>
</html>
