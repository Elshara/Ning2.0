<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script src="/test/js/lib/Test/Builder.js"></script>
<script src="/test/js/lib/Test/More.js"></script>
<script src="/xn_resources/widgets/lib/js/jquery/jquery.js"></script>
<script src="/xn_resources/widgets/lib/js/dojo-adapter/src/core.js?x=<%= mt_rand() %>"></script>
<script src="/xn_resources/widgets/lib/js/dojo-adapter/src/lang.js?x=<%= mt_rand() %>"></script>
<script src="/xn_resources/widgets/shared/js/topic.js?x=<%= mt_rand() %>"></script>
</head>
<body style="margin:0;padding:0;">
<pre id="test">
<script type="text/javascript">
    window.x$ = jQuery.noConflict(true)
    plan({ tests: 5 });

    var result = '';
    var listenerA = function(arg1, arg2) {
        result += 'event1 listenerA ' + arg1 + ' ' + arg2 + ' ';
    };
    xg.shared.topic.subscribe('event1', listenerA);
    xg.shared.topic.subscribe('event1', function(arg1, arg2) {
        result += 'event1 listenerB ' + arg1 + ' ' + arg2 + ' ';
    });
    xg.shared.topic.subscribe('event2', function(arg1, arg2) {
        result += 'event2 listenerC ' + arg1 + ' ' + arg2 + ' ';
    });
    xg.shared.topic.publish('event1', ['apple', 'banana']);
    is(result, 'event1 listenerA apple banana event1 listenerB apple banana ');
    result = '';
    xg.shared.topic.publish('event1', ['cat', 'dog']);
    is(result, 'event1 listenerA cat dog event1 listenerB cat dog ');
    result = '';
    xg.shared.topic.unsubscribe('event1', listenerA);
    xg.shared.topic.publish('event1', ['elephant', 'fox']);
    is(result, 'event1 listenerB elephant fox ');
    result = '';
    xg.shared.topic.publish('event2', ['red', 'yellow']);
    is(result, 'event2 listenerC red yellow ');
    result = '';
    xg.shared.topic.publish('event3', ['foo', 'bar']);
    is(result, '');

</script>
</pre>
</body>
</html>
