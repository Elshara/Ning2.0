<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script src="/test/js/lib/Test/Builder.js"></script>
<script src="/test/js/lib/Test/More.js"></script>
<script src="/xn_resources/widgets/lib/js/jquery/jquery.js"></script>
<script src="/xn_resources/widgets/lib/js/dojo-adapter/src/core.js?x=<%= mt_rand() %>"></script>
<script src="/xn_resources/widgets/lib/js/dojo-adapter/src/lang.js?x=<%= mt_rand() %>"></script>
<script src="/xn_resources/widgets/shared/js/util.js?x=<%= mt_rand() %>"></script>
<script src="/xn_resources/widgets/shared/js/CountUpdater.js?x=<%= mt_rand() %>"></script>
</head>
<body style="margin:0;padding:0;">
<div style="display:none;">
    <span class="xj_count_foo xj_count_foo_0">nada</span>
    <span class="xj_count_foo xj_count_foo_n">text before <span class="xj_count">0</span> text after</span>
</div>
<pre id="test">
<script type="text/javascript">
    window.x$ = jQuery.noConflict(true)
    plan({ tests: 15 });

    /** xg.shared.CountUpdater.set */
    is(xg.shared.CountUpdater._getCurrentValue('foo'), 0);
    // negative values are converted to abs(value)
    xg.shared.CountUpdater.set('foo', -1);
    is(xg.shared.CountUpdater._getCurrentValue('foo'), 1);
    is(x$('span.xj_count').html(), '-1');
    xg.shared.CountUpdater.set('foo', 999);
    is(xg.shared.CountUpdater._getCurrentValue('foo'), 999);
    xg.shared.CountUpdater.set('foo', 1000);
    is(x$('span.xj_count').html(), '1,000');
    xg.shared.CountUpdater.decrement('foo', 1);
    is(xg.shared.CountUpdater._getCurrentValue('foo'), 999);
    is(x$('span.xj_count').html(), '999');
    xg.shared.CountUpdater.increment('foo', 235);
    is(xg.shared.CountUpdater._getCurrentValue('foo'), 1234);
    is(x$('span.xj_count').html(), '1,234');
    xg.shared.CountUpdater.increment('foo', 99000);
    is(xg.shared.CountUpdater._getCurrentValue('foo'), 100234);
    is(x$('span.xj_count').html(), '100,234');
    xg.shared.CountUpdater.increment('foo', 900000);
    is(xg.shared.CountUpdater._getCurrentValue('foo'), 1000234);
    is(x$('span.xj_count').html(), '1,000,234');
    xg.shared.CountUpdater.decrement('foo', 1000235);
    is(xg.shared.CountUpdater._getCurrentValue('foo'), 1);
    is(x$('span.xj_count').html(), '-1');

</script>
</pre>
</body>
</html>
