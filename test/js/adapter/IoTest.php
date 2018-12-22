<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script src="/test/js/lib/Test/Builder.js"></script>
<script src="/test/js/lib/Test/More.js"></script>
<script src="/xn_resources/widgets/lib/js/jquery/jquery.js"></script>
<script src="/xn_resources/widgets/lib/js/dojo-adapter/src/core.js?x=<%= mt_rand() %>"></script>
<script src="/xn_resources/widgets/lib/js/dojo-adapter/src/io.js?x=<%= mt_rand() %>"></script>
</head>
<body style="margin:0;padding:0;">
<pre id="test">
<script type="text/javascript">
    window.x$ = jQuery.noConflict(true)
    plan({ tests: 1 });

    var xg = { token: 123 };
    var conf = {formNode: x$('<form action="http://foo.com" method="post"><input type="hidden" name="foo" value="bar" /></form>')[0]};
    dojo.io.prepareBind(conf);
    isDeeply(conf.content, {foo: 'bar', xg_token: '123'});
</script>
</pre>
</body>
</html>
