<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script src="/test/js/lib/Test/Builder.js"></script>
<script src="/test/js/lib/Test/More.js"></script>
<script src="/xn_resources/widgets/lib/js/jquery/jquery.js"></script>
<script src="/xn_resources/widgets/lib/js/dojo-adapter/src/core.js?x=<%= mt_rand() %>"></script>
<script src="/xn_resources/widgets/lib/js/dojo-adapter/src/graphics.js?x=<%= mt_rand() %>"></script>
<script src="/xn_resources/widgets/lib/js/dojo-adapter/src/lang.js?x=<%= mt_rand() %>"></script>
<script src="/xn_resources/widgets/lib/js/dojo-adapter/src/html.js?x=<%= mt_rand() %>"></script>
</head>
<body style="margin:0;padding:0;">
<pre id="test">
<script type="text/javascript">
    window.x$ = jQuery.noConflict(true)
    plan({ tests: 96 });

    var x = x$('<div class="foo"></div>')[0];
    dojo.html.addClass(x, 'bar')
    is(x.className, 'foo bar');

    x = x$('<div class="foo"></div>')[0];
    dojo.html.addClass(x, 'foo')
    is(x.className, 'foo');

    x = x$('<div class=" foo "></div>')[0];
    dojo.html.removeClass(x, ' bar ')
    is(x.className, 'foo');
    dojo.html.removeClass(x, ' foo ')
    is(x.className, '');

    x = x$('<div class="foo bar"></div>')[0];
    is(dojo.html.hasClass(x, 'foo'), true);
    is(dojo.html.hasClass(x, 'ghi'), false);

    is(dojo.html.createNodesFromText('<span></span><span></span>   ').length, 2);

    x = x$('<div foo="bar"></div>')[0];
    is(dojo.html.getAttribute(x, 'foo'), 'bar');

    x = x$('<div class="foo bar"></div>')[0];
    is(dojo.html.getClass(x), 'foo bar');

    x = x$('<div><span class="foo"></span><span class="foo"></span><pre class="foo"></pre><pre></pre></div>')[0];
    is(dojo.html.getElementsByClass('foo', x).length, 3);
    is(dojo.html.getElementsByClass('foo', x, 'span').length, 2);

    x = x$('<span class="a"><span class="b"><pre class="c"><b class="d"></b></pre><span><span>')[0];
    is(dojo.html.getFirstAncestorByTag(x$('.d', x)[0], 'SPAN').className, 'b');
    is(dojo.html.getFirstAncestorByTag(x$('.d', x)[0], 'em'), null);
    is(dojo.html.getAncestorsByTag(x$('.d', x)[0], 'EM', false).length, 0);
    is(dojo.html.getAncestorsByTag(x$('.d', x)[0], 'SPAN', false).length, 2);
    is(dojo.html.getAncestorsByTag(x$('.d', x)[0], 'SPAN', true).className, 'b');
    is(dojo.html.getAncestors(x$('.d', x)[0], function(node) { return node.tagName == 'EM' }, false).length, 0);
    is(dojo.html.getAncestors(x$('.d', x)[0], function(node) { return node.tagName == 'SPAN' }, false).length, 2);
    is(dojo.html.getAncestors(x$('.d', x)[0], function(node) { return node.tagName == 'SPAN' }, true).className, 'b');

    x = x$('<div></div>')[0];
    dojo.html.hide(x);
    is(x.style.display, 'none');
    dojo.html.show(x);
    is(x.style.display, 'block');

    x = x$('<div style="display: inline"></div>')[0];
    dojo.html.hide(x);
    is(x.style.display, 'none');
    dojo.html.show(x);
    is(x.style.display, 'inline');

    x = x$('<div></div>')[0];
    dojo.html.setDisplay(x, false);
    is(x.style.display, 'none');
    dojo.html.setDisplay(x, true);
    is(x.style.display, 'block');

    x = x$('<div></div>')[0];
    dojo.html.setVisibility(x, false);
    is(x.style.visibility, 'hidden');
    dojo.html.setVisibility(x, true);
    is(x.style.visibility, 'visible');

    x = x$('<div style="display: inline"></div>')[0];
    dojo.html.setShowing(x, false);
    is(x.style.display, 'none');
    dojo.html.setShowing(x, true);
    is(x.style.display, 'inline');

    x = x$('<div style="display: inline"></div>')[0];
    dojo.html.toggleShowing(x);
    is(x.style.display, 'none');
    dojo.html.toggleShowing(x);
    is(x.style.display, 'inline');

    x = x$('<div><span class="a"></span><span class="b"></span></div>')[0];
    dojo.html.insertAfter(x$('<span class="c"></span>')[0], x$('.a', x)[0]);
    is(x$('span', x)[0].className, 'a');
    is(x$('span', x)[1].className, 'c');
    is(x$('span', x)[2].className, 'b');

    x = x$('<div><span class="a"></span><span class="b"></span></div>')[0];
    dojo.html.insertBefore(x$('<span class="c"></span>')[0], x$('.a', x)[0]);
    is(x$('span', x)[0].className, 'c');
    is(x$('span', x)[1].className, 'a');
    is(x$('span', x)[2].className, 'b');

    x = x$('<div></div>')[0];
    dojo.html.hide(x);
    is(dojo.html.isDisplayed(x), false);
    dojo.html.show(x);
    is(dojo.html.isDisplayed(x), true);

    x = x$('<div></div>')[0];
    dojo.html.hide(x);
    is(dojo.html.isShowing(x), false);
    dojo.html.show(x);
    is(dojo.html.isShowing(x), true);

    x = x$('<div><span></span></div>')[0];
    is(x$('span', x).length, 1);
    dojo.html.removeNode(x$('span', x)[0]);
    is(x$('span', x).length, 0);

    x = x$('<div><span>hello</span> world</div>')[0];
    is(dojo.html.renderedTextContent(x), 'hello world');

    x = x$('<div class="foo bar"></div>')[0];
    dojo.html.setClass(x, 'a b');
    is(x.className, 'a b');

    x = x$('<div></div>')[0];
    is(x$(x).css('opacity'), 1);
    dojo.html.setOpacity(x, 0);
    is(x$(x).css('opacity'), 0);

    x = x$('<div></div>')[0];
    dojo.html.setStyle(x, 'z-index', 5);
    is(x.style.zIndex, 5);

    x = x$('<div></div>')[0];
    dojo.html.setStyle(x, 'opacity', 1);
    is(x.style.opacity, 1); // BAZ-9321 [Jon Aquino 2008-08-27]

    x = x$('<div><span></span><pre></pre><b></b></div>')[0];
    is(dojo.html.firstElement(x).tagName, 'SPAN');
    is(dojo.html.firstElement(x, 'PRE').tagName, 'PRE');
    is(dojo.html.firstElement(x, 'em'), null);

    x = x$('<div><span class="a"></span><span class="b"></span></div>')[0];
    dojo.html.insertAtPosition(x$('<span class="c"></span>')[0], x, 'last');
    is(x$('span', x)[0].className, 'a');
    is(x$('span', x)[1].className, 'b');
    is(x$('span', x)[2].className, 'c');

    x = x$('<div><span class="a"></span><span class="b"></span></div>')[0];
    dojo.html.insertAtPosition(x$('<span class="c"></span>')[0], x, 'first');
    is(x$('span', x)[0].className, 'c');
    is(x$('span', x)[1].className, 'a');
    is(x$('span', x)[2].className, 'b');

    x = x$('<div></div>')[0];
    dojo.html.insertAtPosition(x$('<span class="c"></span>')[0], x, 'last');
    is(x$('span', x).length, 1);
    is(x$('span', x)[0].className, 'c');

    x = x$('<div></div>')[0];
    dojo.html.insertAtPosition(x$('<span class="c"></span>')[0], x, 'first');
    is(x$('span', x).length, 1);
    is(x$('span', x)[0].className, 'c');

    x = x$('<div><span class="a"></span><span class="b"></span></div>')[0];
    dojo.html.prependChild(x$('<span class="c"></span>')[0], x);
    is(x$('span', x)[0].className, 'c');
    is(x$('span', x)[1].className, 'a');
    is(x$('span', x)[2].className, 'b');

    x = x$('<div></div>')[0];
    dojo.html.prependChild(x$('<span class="c"></span>')[0], x);
    is(x$('span', x).length, 1);
    is(x$('span', x)[0].className, 'c');

    x = x$('<div><span class="a"></span><pre class="b"></pre><span class="c"></span></div>')[0];
    is(dojo.html.nextElement(null), null);
    is(dojo.html.nextElement(x$('.a', x)[0]).className, 'b');
    is(dojo.html.nextElement(x$('.a', x)[0], 'span').className, 'c');
    is(dojo.html.nextElement(x$('.a', x)[0], 'em'), null);
    is(dojo.html.nextElement(x$('.c', x)[0]), null);
    is(dojo.html.nextElement(x$('.c', x)[0], 'span'), null);

    x = x$('<div><span class="a"></span><pre class="b"></pre><span class="c"></span></div>')[0];
    is(dojo.html.prevElement(null), null);
    is(dojo.html.prevElement(x$('.c', x)[0]).className, 'b');
    is(dojo.html.prevElement(x$('.c', x)[0], 'span').className, 'a');
    is(dojo.html.prevElement(x$('.c', x)[0], 'em'), null);
    is(dojo.html.prevElement(x$('.a', x)[0]), null);
    is(dojo.html.prevElement(x$('.a', x)[0], 'span'), null);

    x = x$('<div></div>')[0];
    dojo.html.removeChildren(x);
    is(x$('span', x).length, 0);

    x = x$('<div><span></span><span></span><span></span></div>')[0];
    dojo.html.removeChildren(x);
    is(x$('span', x).length, 0);

    x = x$('<div style="display: inline"></div>')[0];
    is(dojo.html.getComputedStyle(x, 'display'), 'inline');

    x = x$('<div style="display: inline"></div>')[0];
    is(dojo.html.getStyle(x, 'display'), 'inline');

    x = x$('<div style="background-color:red"><div style="background-color:rgb(0,128,0)"><div class="a" style="background-color:blue"></div></div></div>')[0];
    is(dojo.html.getBackgroundColor(x$('.a', x)[0]).toString(), [0, 0, 255].toString());

    x = x$('<div style="background-color:red"><div style="background-color:rgb(0,128,0)"><div class="a" style="background-color:transparent"></div></div></div>')[0];
    is(dojo.html.getBackgroundColor(x$('.a', x)[0]).toString(), [0, 128, 0].toString());

    x = x$('<div style="background-color:red"><div style="background-color:rgba(0, 0, 0, 0)"><div class="a" style="background-color:transparent"></div></div></div>')[0];
    is(dojo.html.getBackgroundColor(x$('.a', x)[0]).toString(), [255, 0, 0].toString());

    x = x$('<div style="position:absolute;left:1000px;top:1001px"><div class="a" style="position:absolute;left:1000px;top:1001px">A</div></div>').appendTo(document.body)[0];
    is(dojo.html.getAbsoluteX(x$('.a', x)[0], true), 2000);
    is(dojo.html.getAbsolutePosition(x$('.a', x)[0], true).toString(), [2000, 2002].toString());
    x$(x).remove();

    x = x$('<div style="padding-top: 19px"></div>')[0];
    dojo.html.setStyleAttributes(x, " padding-left: 20px; padding-right: 21px; ");
    is(x$(x).css('padding-top'), '19px');
    is(x$(x).css('padding-left'), '20px');
    is(x$(x).css('padding-right'), '21px');

    x = x$('<div style="border: 6px solid; padding: 3px; margin: 2px; background-color: red; width: 100px; height: 101px;"></div>').appendTo(document.body)[0];
    is(dojo.html.getBorderBoxWidth(x), 118);
    is(dojo.html.getBorderBoxHeight(x), 119);
    x$(x).remove();

    x = x$('<div style="padding: 1px 2px 3px 4px;"></div>')[0];
    is(dojo.html.getPaddingWidth(x), 6);
    is(dojo.html.getPaddingHeight(x), 4);

    x = x$('<div style="padding-right: 1px"></div>')[0];
    is(dojo.html.getPaddingWidth(x), 1);
    is(dojo.html.getPaddingHeight(x), 0);

</script>
</pre>
</body>
</html>
