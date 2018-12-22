<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script src="/test/js/lib/Test/Builder.js"></script>
<script src="/test/js/lib/Test/More.js"></script>
<script src="/xn_resources/widgets/lib/js/jquery/jquery.js"></script>
<script src="/xn_resources/widgets/lib/js/dojo-adapter/src/core.js?x=<%= mt_rand() %>"></script>
<script src="/xn_resources/widgets/lib/js/dojo-adapter/src/lang.js?x=<%= mt_rand() %>"></script>
</head>
<body style="margin:0;padding:0;">
<pre id="test">
<script type="text/javascript">
    window.x$ = jQuery.noConflict(true)
    plan({ tests: 22 });

    var Car = function() {};
    var jonsCar = new Car();
    dojo.lang.extend(Car, { foo: function() { return 4; } });
    is(jonsCar.foo(), 4);

    var a = [1, 2, 3, 4, 5];
    isDeeply(dojo.lang.filter(a, function(x) { return x % 2 == 0}), [2, 4]);
    isDeeply(a, [1, 2, 3, 4, 5]);

    a = '';
    dojo.lang.forEach(['a', 'b', 'c'], function(x) { a += x; });
    is(a, 'abc');

    var add = dojo.lang.hitch({x: 4}, function(y) { return this.x + y; });
    is(add(3), 7);

    a = {};
    is(dojo.lang.inArray([{}, {}, {}], a), false);
    is(dojo.lang.inArray([{}, a, {}], a), true);
    is(dojo.lang.inArray(['a', 'b', 'c'], 'b'), true);
    is(dojo.lang.inArray(['a', 'b', 'c'], 'd'), false);

    is(dojo.lang.isEmpty({}), true);
    is(dojo.lang.isEmpty({a: 1}), false);
    is(dojo.lang.isEmpty([]), true);
    is(dojo.lang.isEmpty([1]), false);

    is(dojo.lang.isString(1), false);
    is(dojo.lang.isString(new Number(1)), false);
    is(dojo.lang.isString('hello'), true);
    is(dojo.lang.isString(new String('hello')), true);

    a = [1, 2, 3, 4, 5];
    isDeeply(dojo.lang.map(a, function(x) {return x*2; }), [2, 4, 6, 8, 10]);
    isDeeply(a, [1, 2, 3, 4, 5]);

    isDeeply(dojo.lang.map([{a: {b: 1}}, {c: {d: 2}}], function(x) {return x; }), [{a: {b: 1}}, {c: {d: 2}}]);

    isDeeply(dojo.lang.mixin({a: 'A', b: 'B'}, { b: 'B2', c: 'C'}), {a: 'A', b: 'B2', c: 'C'});
    isDeeply(dojo.lang.mixin({a: 'A', b: 'B'}, { b: 'B2', c: 'C'}, { c: 'C2', d: 'D'}), {a: 'A', b: 'B2', c: 'C2', d: 'D'});
</script>
</pre>
</body>
</html>
