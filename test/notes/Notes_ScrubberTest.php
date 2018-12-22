<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
W_Cache::getWidget('notes')->includeFileOnce('/lib/helpers/Notes_Scrubber.php');

//
class Notes_ScrubberTest extends UnitTestCase {

    protected function assertEqualsOneOf($value, $choices) {
        foreach ($choices as $choice) {
            if ($value == $choice) {
                $this->pass("$value = $choice");
                return;
            }
        }
        $this->fail("$value is not one of [" .
                    implode(',', $choices) . "]");
    }

    public function testP2() {
        $scrubbed = xnhtmlentities(Notes_Scrubber::scrub('<p>'));
        $expected = array(xnhtmlentities('<p></p>'),
                          xnhtmlentities('<p>') . '&nbsp;' . xnhtmlentities('</p>'));
        $this->assertEqualsOneOf($scrubbed, $expected);
    }

    public function testP() {
        $html = '<p>how are you today?</p>';
        $this->assertEqual($html, Notes_Scrubber::scrub($html));
    }

    public function testList() {
        $html = '<ul><li>one</li><li>two</li></ul>';
        $expected = "<ul>\n<li>one</li>\n<li>two</li>\n</ul>";
        $this->assertEqual($expected, Notes_Scrubber::scrub($html));
    }

    public function testScript() {
        $html = '<script type="text/javascript>alert("naughty");</script><p>hello</p>';
        $expected = '<p>hello</p>';
        $this->assertEqual($expected, Notes_Scrubber::scrub($html));
    }

    public function testEvent() {
        $html = '<p onmouseover="javascript:blah">oops!</p>';
        $expected = '<p>oops!</p>';
        $this->assertEqual($expected, Notes_Scrubber::scrub($html));
    }

    public function testEmbed() {
        $html = '<object width="123" height="456"><param name="foo" value="bar"/><embed src="foo" type="bar" width="123" height="456"/></object>';
        $expected = '<object width="123" height="456"><param name="foo" value="bar"/>
<embed src="foo" type="bar" width="123" height="456" allowscriptaccess="never"/> <param name="allowscriptaccess" value="never"/></object>';
        $this->assertEqual($expected, Notes_Scrubber::scrub($html));
    }

    public function testEmbed2() {
        $this->assertEqual('<embed src="http://www.youtube.com/v/g6XLAX0Sovk" width="205" height="168" type="application/x-shockwave-flash" allowscriptaccess="never" wmode="transparent"/>',
                Notes_Scrubber::scrub('<EMBED src=http://www.youtube.com/v/g6XLAX0Sovk width=205 height=168 type=application/x-shockwave-flash allowscriptaccess="never" wmode="transparent"></EMBED>'));
    }

    public function testEmbedElementList() {
        $html = '<p>some junk</p><object width="425" height="350"><param name="movie" value="http://www.youtube.com/v/zmHm0rGns4I"></param><embed src="http://www.youtube.com/v/zmHm0rGns4I" type="application/x-shockwave-flash" width="425" height="350"></embed></object>';
        $expected = '<object width="425" height="350"><param name="movie" value="http://www.youtube.com/v/zmHm0rGns4I"/><embed src="http://www.youtube.com/v/zmHm0rGns4I" type="application/x-shockwave-flash" width="425" height="350" allowscriptaccess="never"/> <param name="allowscriptaccess" value="never"/></object>';
        $scrubbed = Notes_Scrubber::scrub($html,array('elements' => array('object','param','embed')));
        $this->assertEqual($expected, $scrubbed);
    }

    public function testJavascriptHref() {
        $html = '<a href="javascript:orange()">banana</a>';
        $expected = '<a>banana</a>';
        $this->assertEqual($expected, Notes_Scrubber::scrub($html));
    }

    public function testHttpHref() {
        $html = '<a href="http://somewhere">banana</a>';
        $this->assertEqual($html, Notes_Scrubber::scrub($html));
    }

    public function testHttpsHref() {
        $html = '<a href="https://somewhere">banana</a>';
        $this->assertEqual($html, Notes_Scrubber::scrub($html));
    }

    public function testFtpHref() {
        $html = '<a href="ftp://somewhere">banana</a>';
        $this->assertEqual($html, Notes_Scrubber::scrub($html));
    }

    public function testMailtoHref() {
        $html = '<a href="mailto:somewhere">banana</a>';
        $this->assertEqual($html, Notes_Scrubber::scrub($html));
    }

    public function testRtspHref() {
        $html = '<a href="rtsp://somewhere">banana</a>';
        $this->assertEqual($html, Notes_Scrubber::scrub($html));
    }

    public function testMmsHref() {
        $html = '<a href="mms://somewhere">banana</a>';
        $this->assertEqual($html, Notes_Scrubber::scrub($html));
    }


    public function testSchemelessHref() {
        $html = '<a href="somewhere">banana</a>';
        $this->assertEqual($html, Notes_Scrubber::scrub($html));
    }

    public function testJavascriptNullHref() {
        $html = '<a href="java'.chr(0).'script:orange()">banana</a>';
        $expected = array('<a href="java"></a>','<a>banana</a>');
        $this->assertEqualsOneOf(Notes_Scrubber::scrub($html), $expected);
    }

    public function testJavascriptSpaceHref() {
        $html = '<a href="java script:orange()">banana</a>';
        $expected = '<a>banana</a>';
        $this->assertEqual(Notes_Scrubber::scrub($html), $expected);
    }

    public function testJavascriptNewlineHref() {
        $html = '<a href="java'."\n".'script:orange()">banana</a>';
        $expected = '<a>banana</a>';
        $this->assertEqual($expected, Notes_Scrubber::scrub($html));
    }

    public function testYouTube() {
        $html = '<object width="425" height="355"><param name="movie" value="http://www.youtube.com/v/3wFG5Re0LCA&hl=en"></param><param name="wmode" value="transparent"></param><embed src="http://www.youtube.com/v/3wFG5Re0LCA&hl=en" type="application/x-shockwave-flash" wmode="transparent" width="425" height="355"></embed></object>';
        $expected = '<object width="425" height="355"><param name="movie" value="http://www.youtube.com/v/3wFG5Re0LCA&amp;hl=en"/>
<param name="wmode" value="transparent"/>
<embed src="http://www.youtube.com/v/3wFG5Re0LCA&amp;hl=en" type="application/x-shockwave-flash" wmode="transparent" width="425" height="355" allowscriptaccess="never"/> <param name="allowscriptaccess" value="never"/></object>';
        $this->assertEqual($expected, Notes_Scrubber::scrub($html));
    }

    public function testGoogleVideo() {
        $html = '<embed style="width:400px; height:326px;" id="VideoPlayback" type="application/x-shockwave-flash" src="http://video.google.com/googleplayer.swf?docId=7830246530742207581"> </embed>';
        $expected = '<embed style="width:400px; height:326px;" id="VideoPlayback" type="application/x-shockwave-flash" src="http://video.google.com/googleplayer.swf?docId=7830246530742207581" allowscriptaccess="never"/>';
        $this->assertEqual($expected, Notes_Scrubber::scrub($html));
    }

    public function testIFilm() {
        $html = '<embed allowScriptAccess="never" width="448" height="365" src="http://www.ifilm.com/efp" quality="high" bgcolor="000000" name="efp" align="middle" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" flashvars="flvbaseclip=2760747" ></embed>';
        $expected = '<embed allowscriptaccess="never" width="448" height="365" src="http://www.ifilm.com/efp" quality="high" bgcolor="#000000" name="efp" align="middle" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" flashvars="flvbaseclip=2760747"/>';
        $this->assertEqual($expected, Notes_Scrubber::scrub($html));
    }

    public function testIFilm2() {
        $html = '<embed allowScriptAccess="never" width="448" height="365" src="http://www.ifilm.com/efp" quality="high" bgcolor="000000" name="efp" align="middle" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" flashvars="flvBaseClip=2759586" />';
        $expected = '<embed allowscriptaccess="never" width="448" height="365" src="http://www.ifilm.com/efp" quality="high" bgcolor="#000000" name="efp" align="middle" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" flashvars="flvBaseClip=2759586"/>';
        $this->assertEqual($expected, Notes_Scrubber::scrub($html));
    }

    public function testNingPlayer() {
        $html = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="450" height="390" id="test" align="middle" style="position:relative" > <param name="allowScriptAccess" value="always" /> <param name="movie" value="http://flvtest.ning.com/flvplayer.swf? &video_url=& &frame_url=& &autoload=& &autoplay=& &video_size=& &video_duration=& &rew_btn=on& &play_btn=on& &fwd_btn=on& &stop_btn=on& &controller_inside=& &autorewind=on& &seek_handler=on& &footer=on& &mute_btn=on& &volume_bar=on& &bgcolor=#E3E3E3& &player_url=http%3A%2F%2Fflvtest.ning.com%2Fflvplayer.swf& &brand_url=& "/> <param name="scale" value="noscale" /> <param name="quality" value="high" /> <param name="wmode" value="transparent" /> <embed src="http://flvtest.ning.com/flvplayer.swf? &video_url=& &frame_url=& &autoload=& &autoplay=& &video_size=& &video_duration=& &rew_btn=on& &play_btn=on& &fwd_btn=on& &stop_btn=on& &controller_inside=& &autorewind=on& &seek_handler=on& &footer=on& &mute_btn=on& &volume_bar=on& &bgcolor=#E3E3E3& &player_url=http%3A%2F%2Fflvtest.ning.com%2Fflvplayer.swf& &brand_url=& " quality="high" width="450" height="390" name="test" align="middle" scale="noscale" wmode="transparent" allowScriptAccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" style="position:relative" /> </object>';
        $expected = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="450" height="390" id="test" align="middle" style="position:relative"><param name="allowScriptAccess" value="never"/>
<param name="movie" value="http://flvtest.ning.com/flvplayer.swf? &amp;video_url=&amp; &amp;frame_url=&amp; &amp;autoload=&amp; &amp;autoplay=&amp; &amp;video_size=&amp; &amp;video_duration=&amp; &amp;rew_btn=on&amp; &amp;play_btn=on&amp; &amp;fwd_btn=on&amp; &amp;stop_btn=on&amp; &amp;controller_inside=&amp; &amp;autorewind=on&amp; &amp;seek_handler=on&amp; &amp;footer=on&amp; &amp;mute_btn=on&amp; &amp;volume_bar=on&amp; &amp;bgcolor=#E3E3E3&amp; &amp;player_url=http%3A%2F%2Fflvtest.ning.com%2Fflvplayer.swf&amp; &amp;brand_url=&amp; "/>
<param name="scale" value="noscale"/>
<param name="quality" value="high"/>
<param name="wmode" value="transparent"/>
<embed src="http://flvtest.ning.com/flvplayer.swf?%20&amp;video_url=&amp;%20&amp;frame_url=&amp;%20&amp;autoload=&amp;%20&amp;autoplay=&amp;%20&amp;video_size=&amp;%20&amp;video_duration=&amp;%20&amp;rew_btn=on&amp;%20&amp;play_btn=on&amp;%20&amp;fwd_btn=on&amp;%20&amp;stop_btn=on&amp;%20&amp;controller_inside=&amp;%20&amp;autorewind=on&amp;%20&amp;seek_handler=on&amp;%20&amp;footer=on&amp;%20&amp;mute_btn=on&amp;%20&amp;volume_bar=on&amp;%20&amp;bgcolor=#E3E3E3&amp;%20&amp;player_url=http%3A%2F%2Fflvtest.ning.com%2Fflvplayer.swf&amp;%20&amp;brand_url=&amp;" quality="high" width="450" height="390" name="test" align="middle" scale="noscale" wmode="transparent" allowscriptaccess="never" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" style="position:relative"/> </object>';

$this->assertEqual($expected, Notes_Scrubber::scrub($html));
    }

    public function testUtf8() {
        $html = 'الصفحة الرئيسية';
        $expected = 'الصفحة الرئيسية';
        $this->assertEqual($expected, Notes_Scrubber::scrub($html));
    }


    public function testUtf8Again() {
        $html = 'الصفحة الرئيسية';
        $this->assertEqual($html, html_entity_decode(Notes_Scrubber::scrub($html), ENT_QUOTES, 'UTF-8'));
    }

    public function testSpecifyEncoding() {
        $html = 'الصفحة الرئيسية';
        $opts = array('encoding' => 'utf8');
        $this->assertEqual($html, html_entity_decode(Notes_Scrubber::scrub($html, $opts), ENT_QUOTES, 'UTF-8'));
    }

    public function testAlternateEncoding() {
        $html = 'Oh, wie ist das sch'.chr(246).'n!';
        // Results are always in UTF-8
        $expected = 'Oh, wie ist das schön!';
        $opts = array('encoding' => 'latin1');
        $scrubbed = Notes_Scrubber::scrub($html, $opts);
        $this->assertEqual($expected, $scrubbed);
    }
    public function testLongHtml() {
        $html = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec quam turpis, imperdiet a, egestas sed, euismod eget, lectus. Curabitur id libero id ipsum tempor lacinia. Aliquam nec turpis non elit varius consequat. Maecenas iaculis iaculis dolor. Nunc enim risus, semper vitae, ullamcorper nec, ornare at, augue. Nulla laoreet, massa congue tristique pellentesque, est lorem condimentum nulla, sed ultrices diam orci eu magna. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam consequat. Aenean ultrices vehicula mauris. Quisque vitae diam.';
        $expected = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec quam turpis, imperdiet a, egestas sed, euismod eget, lectus. Curabitur id libero id ipsum tempor lacinia. Aliquam nec turpis non elit varius consequat. Maecenas iaculis iaculis dolor. Nunc enim risus, semper vitae, ullamcorper nec, ornare at, augue. Nulla laoreet, massa congue tristique pellentesque, est lorem condimentum nulla, sed ultrices diam orci eu magna. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam consequat. Aenean ultrices vehicula mauris. Quisque vitae diam.';
        $this->assertEqual($expected, Notes_Scrubber::scrub($html));
    }

    public function testHtmlWithNewlines() {
        $html = 'Lorem ipsum
                 dolor sit
                 amet, consectetuer';
        $expected = 'Lorem ipsum dolor sit amet, consectetuer';
        $this->assertEqual($expected, Notes_Scrubber::scrub($html));
    }

    public function testPreWithNewlines() {
        $html = '<pre>Lorem ipsum
                 dolor sit
                 amet, consectetuer</pre>';
        $expected = "<pre>
Lorem ipsum
                 dolor sit
                 amet, consectetuer
</pre>";
        $this->assertEqual($expected, Notes_Scrubber::scrub($html));
    }

    public function testEmptyDiv() {
        // <div /> is treated as an open div tag in current browsers  [Jon Aquino 2006-09-06]
        $html = '<div class="foo"></div><img src="http://example.com"/><img src="http://example.com"/>';
        $expected = '<div class="foo"></div>'."\n".'<img src="http://example.com"/><img src="http://example.com"/>';
        $this->assertEqual($expected, Notes_Scrubber::scrub($html));
    }

    public function testBAZ2274a() {
        $this->assertEqual('<a style="display:block"></a>', Notes_Scrubber::scrub('<a style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<abbr style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<acronym style="display:block">'));
        $this->assertEqual('<address style="display:block"></address>', Notes_Scrubber::scrub('<address style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<applet style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<b style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<bdo style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<big style="display:block">'));
        $this->assertEqual('<blockquote style="display:block"></blockquote>', Notes_Scrubber::scrub('<blockquote style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<body style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<button style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<caption style="display:block">'));
        $this->assertEqual('<center style="display:block"></center>', Notes_Scrubber::scrub('<center style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<cite style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<code style="display:block">'));
        $this->assertEqual('<table>
<colgroup style="display:block"></colgroup>
</table>', Notes_Scrubber::scrub('<colgroup style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<dd style="display:block">'));
        $this->assertEqual('<del style="display:block"></del>', Notes_Scrubber::scrub('<del style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<dfn style="display:block">'));
        $this->assertEqual('<ul style="display:block"></ul>', Notes_Scrubber::scrub('<dir style="display:block">'));
        $this->assertEqual('<div style="display:block"></div>', Notes_Scrubber::scrub('<div style="display:block">'));
        $this->assertEqual('<dl style="display:block"></dl>', Notes_Scrubber::scrub('<dl style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<dt style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<em style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<fieldset style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<font style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<form style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<frameset style="display:block">'));
        $this->assertEqual('<h1 style="display:block"></h1>', Notes_Scrubber::scrub('<h1 style="display:block">'));
        $this->assertEqual('<h2 style="display:block"></h2>', Notes_Scrubber::scrub('<h2 style="display:block">'));
        $this->assertEqual('<h3 style="display:block"></h3>', Notes_Scrubber::scrub('<h3 style="display:block">'));
        $this->assertEqual('<h4 style="display:block"></h4>', Notes_Scrubber::scrub('<h4 style="display:block">'));
        $this->assertEqual('<h5 style="display:block"></h5>', Notes_Scrubber::scrub('<h5 style="display:block">'));
        $this->assertEqual('<h6 style="display:block"></h6>', Notes_Scrubber::scrub('<h6 style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<head style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<html style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<i style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<iframe style="display:block">'));
        $this->assertEqual('<ins style="display:block"></ins>', Notes_Scrubber::scrub('<ins style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<kbd style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<label style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<legend style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<li style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<map style="display:block">'));
        $this->assertEqual('<ul style="display:block"></ul>', Notes_Scrubber::scrub('<menu style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<noframes style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<noscript style="display:block">'));
        $this->assertEqual('<object style="display:block"><param name="allowscriptaccess" value="never"/></object>', Notes_Scrubber::scrub('<object style="display:block">'));
        $this->assertEqual('<ol style="display:block"></ol>', Notes_Scrubber::scrub('<ol style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<optgroup style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<option style="display:block">'));
        $this->assertEqualsOneOf(xnhtmlentities(Notes_Scrubber::scrub('<p>')),
                                 array(xnhtmlentities('<p></p>'),
                                       xnhtmlentities('<p>') . '&nbsp;' . xnhtmlentities('</p>')));
        $this->assertEqual('<pre style="display:block">
</pre>', Notes_Scrubber::scrub('<pre style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<q style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<s style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<samp style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<script style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<select style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<small style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<span style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<strike style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<strong style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<style style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<sub style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<sup style="display:block">'));
        $this->assertEqual('<table style="display:block"></table>', Notes_Scrubber::scrub('<table style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<tbody style="display:block">'));
        $this->assertEqual('<table>
<tr>
<td style="display:block"></td>
</tr>
</table>', Notes_Scrubber::scrub('<td style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<textarea style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<tfoot style="display:block">'));
        $this->assertEqual('<table>
<tr>
<th style="display:block"></th>
</tr>
</table>', Notes_Scrubber::scrub('<th style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<thead style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<title style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<tr style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<tt style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<u style="display:block">'));
        $this->assertEqual('<ul style="display:block"></ul>', Notes_Scrubber::scrub('<ul style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<var style="display:block">'));
    }

    public function testBAZ2274b() {
        $this->assertEqual('', Notes_Scrubber::scrub('<area style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<base style="display:block">'));
        $this->assertEqual('<basefont/>', Notes_Scrubber::scrub('<basefont style="display:block">'));
        $this->assertEqual('<br style="display:block"/>', Notes_Scrubber::scrub('<br style="display:block">'));
        $this->assertEqual('<table>
<col style="display:block"/></table>', Notes_Scrubber::scrub('<col style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<frame style="display:block">'));
        $this->assertEqual('<hr style="display:block"/>', Notes_Scrubber::scrub('<hr style="display:block">'));
        $this->assertEqual('<img style="display:block"/>', Notes_Scrubber::scrub('<img style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<input type="text">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<isindex style="display:block">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<link src="http://example.com">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<meta http-equiv="Expires" content="Wed, 21 Jun 2006 14:25:27 GMT">'));
        $this->assertEqual('', Notes_Scrubber::scrub('<param style="display:block">'));
    }

    public function testPHO487() {
        $html = 'Marcos! Marcos! Marcos! Marcos!';
        $scrubbed = Notes_Scrubber::scrub($html);
        $this->assertEqual($html, $scrubbed);
    }

    public function testAssortedLengths() {
        // The range must start with highest numbers first and not start with
        // anything bigger than 62 in order to tickle the bug [ David Sklar 2006-09-28 ]
        foreach (range(62,0) as $i) {
            $html = $this->randomString($i);
            $scrubbed = Notes_Scrubber::scrub($html);
            $this->assertEqual($html, $scrubbed);
        }
    }

    public function testClosingHtml() {
        $html = '<html>foo</html>';
        $scrubbed = Notes_Scrubber::scrub($html);
        $this->assertEqual('foo', $scrubbed);
    }

    public function testClosingHtmlDuplicate() {
        $html = '<html>foo</html>bar</html>';
        $scrubbed = Notes_Scrubber::scrub($html);
        $this->assertEqual('foobar', $scrubbed);
    }

    protected function randomString($len) {
        $s = '';
        for ($i = 0; $i < $len; $i++) {
            $s .= chr(65 + mt_rand(0, 25));
        }
        return $s;
    }

    /* NING-4775: HTML Scrubber: Missing double-quote causes entire comment to be blank */
    public function testNing4775() {
        $text=<<<TEXT
Swapping out the photos section for a videos section within a group is not easy to do today but there are really easy ways to pull in and share videos within your group site. And we'll be making the ability to choose features for your group - like videos or photos or both - easier around the new year. Now what you can do today: Start by <a href="http://videos.ning.com> creating a videos site</a> on Ning. Once you've created a videos site and uploaded the videos that you want to share, copy the "embed" code - which is on the video detail page where you play a video - and paste it into the front HTML Editor section or any discussion forum topic or comment on your group. You'll have your videos right there in your group site. And you can make groups and video sites either private or public so you won't lose any of that functionality by adding videos to your group site. Here's some examples of groups with videos in them: http://veronicamars.ning.com http://lebowski.ning.com http://lhs1996.ning.com Hopefully, this is helpful. Please don't hesitate to drop us another note and we can walk you through this in more detail. And it will be easier in a few months!
TEXT;
        $expected=<<<TEXT
Swapping out the photos section for a videos section within a group is not easy to do today but there are really easy ways to pull in and share videos within your group site. And we'll be making the ability to choose features for your group - like videos or photos or both - easier around the new year. Now what you can do today: Start by <a href="http://videos.ning.com">creating a videos site</a> on Ning. Once you've created a videos site and uploaded the videos that you want to share, copy the "embed" code - which is on the video detail page where you play a video - and paste it into the front HTML Editor section or any discussion forum topic or comment on your group. You'll have your videos right there in your group site. And you can make groups and video sites either private or public so you won't lose any of that functionality by adding videos to your group site. Here's some examples of groups with videos in them: http://veronicamars.ning.com http://lebowski.ning.com http://lhs1996.ning.com Hopefully, this is helpful. Please don't hesitate to drop us another note and we can walk you through this in more detail. And it will be easier in a few months!
TEXT;
        $scrubbed = Notes_Scrubber::scrub($text);
        $this->assertEqual($expected, $scrubbed);
    }

    public function testNing4775Small() {
        $text= '<a href="http://whoops>i forgot</a>';
        $expected = '<a href="http://whoops">i forgot</a>';
        $scrubbed = Notes_Scrubber::scrub($text);
        $this->assertEqual($expected, $scrubbed);
    }

    public function testAllowScript() {
        $html = '<a href="http://somewhere">click</a> and <script>alert("foo");</script> and <script src="http://zip/here"></script>';
        $expected ='<a href="http://somewhere">click</a> and <script>
//<![CDATA[
alert("foo");
//]]>
</script>and <script src="http://zip/here">
</script>';
        $defaultScrubbed = Notes_Scrubber::scrub($html);
        $scriptScrubbed = Notes_Scrubber::scrub($html, array('additionalElements' => array('script' => array('src'))));
        $this->assertEqual($scriptScrubbed, $expected);
        $this->assertEqual('<a href="http://somewhere">click</a> and and', $defaultScrubbed);
    }

    public function testTable() {
        $html = '<table><tr><th>name</th><th>age</th></tr><tr><td>alice</td><td>12</td></tr><tr><td>lewis</td><td>50</td></tr></table>';
        $expected = '<table>
<tr>
<th>name</th>
<th>age</th>
</tr>
<tr>
<td>alice</td>
<td>12</td>
</tr>
<tr>
<td>lewis</td>
<td>50</td>
</tr>
</table>';
        $scrubbed = Notes_Scrubber::scrub($html);
        $this->assertEqual($expected, $scrubbed);
    }

    public function testNing5975YubNub() {
        // Characterization test - checks that the output is the same as the output before the fix [Jon Aquino 2007-08-21]
        $html = <<<EOD
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>YubNub - YubNub.org</title>
  <link href="/stylesheets/yubnub.css" media="screen" rel="Stylesheet" type="text/css" />
  <script type="text/javascript">
    function focus(){document.input_box.command.focus();}
  </script>
</head>
<body onload="focus()">
<center>
<img alt="Yubnub" src="/images/yubnub.png" />
<div style="margin-top:30px;margin-bottom:30px">
  <form action="/parser/parse" method="get" name="input_box">
  <input type="text" name="command" size="55" value=""/>
  <input type="submit" value="Enter" />
</form>
</div>
<table>
  <tr><td>Some examples of what you can do with YubNub:</td></tr>
  <tr><td>
    <table style="border-top:thin solid #60A63A;border-bottom:thin solid #60A63A;">
      <tr>
        <td>
          <a href="/parser/parse?command=gim+porsche+911">gim porsche 911</a>
        </td>
        <td class="hint">
          Do a Google Image search for Porsche 911's.
        </td>
        <td>
          <a href="/parser/parse?command=create">create</a>
        </td>
        <td class="hint">
          Create a new command<span style="vertical-align: super; font-size: x-small; color: red;">Cool!</span>
        </td>
      </tr>
      <tr>
        <td>
          <a href="/parser/parse?command=random+100">random 100</a>
        </td>
        <td class="hint">
          Pick a random number between 1 and 100.
        </td>
        <td>
          <a href="/parser/parse?command=ls">ls</a>
        </td>
        <td class="hint">
          List all available commands.
        </td>
      </tr>
      <tr>
        <td>
          <a href="/parser/parse?command=man+random">man random</a>
        </td>
        <td class="hint">
          Display help for the "random" command.
        </td>
        <td colspan="2">
          <a href="/parser/parse?command=xe+-amount+100+-from+USD+-to+EUR">xe -amount 100 -from USD -to EUR</a>
        </td>
      </tr>
    </table>
  </td></tr>
  <tr><td style="padding-top:20px">Popular Searches</td></tr>
  <tr><td>
    <table class="hint" width="100%" style="border-top:thin solid #60A63A;border-bottom:thin solid #60A63A;">
      <tr><td>Google</td><td>g</td><td>Google News</td><td>gnews</td><td>Yahoo!</td><td>y</td></tr>
      <tr><td>Wikipedia</td><td>wp</td><td>Technorati</td><td>tec</td><td>Amazon</td><td>am</td></tr>
      <tr><td>CNN</td><td>cnn</td><td>Weather for zip code</td><td>weather</td><td>eBay</td><td>ebay</td></tr>
      <tr><td>AllMusic.com</td><td>allmusic</td><td>del.icio.us tag</td><td>deli</td><td>Flickr</td><td>flk</td></tr>
      <tr><td>ESPN</td><td>espn</td><td>Yahoo! Stock Quote</td><td>stock</td><td>Dictionary (Answers.com)</td><td>a</td></tr>
    </table>
  </td></tr>
  <tr><td><center><a class="hint" href="/documentation/jeremys_picks">More commands...</a></center></td></tr>
  <tr><td><center><a class="hint" href="/parser/parse?command=ge">Yet more commands...</a></center></td></tr>
</table>
</center>
<center>
<div class="footer">
  <a href="http://jonaquino.blogspot.com/2005/06/yubnub-my-entry-for-rails-day-24-hour.html">What Is This Thing?</a> |
  <a href="/parser/parse?command=ls">List All Commands (ls)</a>
  <a href="http://yubnub.org/commands.xml"><img src="/images/xml_button.gif" alt="Subscribe to RSS feed"></a> |
  <a href="/parser/parse?command=create">Create a New Command</a> |
  <a href="/documentation/describe_installation">Installing YubNub</a> |
  <a href="/documentation/describe_advanced_syntax">Advanced Syntax</a>
  <br />
  <a href="/kernel/most_used_commands">Most-Used Commands</a> |
  <a href="/parser/parse?command=ge">Golden Eggs (Notable Commands)</a>
  <a href="http://yubnub.org/golden_eggs.xml"><img src="/images/xml_button.gif" alt="Subscribe to RSS feed"/></a>  |
  <a href="/documentation/jeremys_picks">Jeremy's Picks</a>
  <br />
  <a href="http://groups.google.com/group/YubNub">Community (Google Group)</a> |
  <a href="http://www.editthis.info/yubnub/">Wiki</a> |
  <a href="http://yubnub.blogspot.com/">YubNub Blog</a> |
  <a href="/parser/parse?command=tec+yubnub">Press (Technorati)</a> |
  <a href="/documentation/display_acknowledgements">Acknowledgements</a> |
  <a href="mailto:jonathan.aquino@gmail.com">Typos? Spam? Contact Jon</a>
</div>
</center>
</body>
</html>
EOD;
        $expected = <<<EOD
<center><img alt="Yubnub" src="/images/yubnub.png"/>
<div style="margin-top:30px;margin-bottom:30px">

</div>
<table>
<tr>
<td>Some examples of what you can do with YubNub:</td>
</tr>
<tr>
<td>
<table style="border-top:thin solid #60A63A;border-bottom:thin solid #60A63A;">
<tr>
<td><a href="/parser/parse?command=gim+porsche+911">gim porsche 911</a></td>
<td class="hint">Do a Google Image search for Porsche 911's.</td>
<td><a href="/parser/parse?command=create">create</a></td>
<td class="hint">Create a new command<span style="vertical-align: super; font-size: x-small; color: red;">Cool!</span></td>
</tr>
<tr>
<td><a href="/parser/parse?command=random+100">random 100</a></td>
<td class="hint">Pick a random number between 1 and 100.</td>
<td><a href="/parser/parse?command=ls">ls</a></td>
<td class="hint">List all available commands.</td>
</tr>
<tr>
<td><a href="/parser/parse?command=man+random">man random</a></td>
<td class="hint">Display help for the "random" command.</td>
<td colspan="2"><a href="/parser/parse?command=xe+-amount+100+-from+USD+-to+EUR">xe -amount 100 -from USD -to EUR</a></td>
</tr>
</table>
</td>
</tr>
<tr>
<td style="padding-top:20px">Popular Searches</td>
</tr>
<tr>
<td>
<table class="hint" width="100%" style="border-top:thin solid #60A63A;border-bottom:thin solid #60A63A;">
<tr>
<td>Google</td>
<td>g</td>
<td>Google News</td>
<td>gnews</td>
<td>Yahoo!</td>
<td>y</td>
</tr>
<tr>
<td>Wikipedia</td>
<td>wp</td>
<td>Technorati</td>
<td>tec</td>
<td>Amazon</td>
<td>am</td>
</tr>
<tr>
<td>CNN</td>
<td>cnn</td>
<td>Weather for zip code</td>
<td>weather</td>
<td>eBay</td>
<td>ebay</td>
</tr>
<tr>
<td>AllMusic.com</td>
<td>allmusic</td>
<td>del.icio.us tag</td>
<td>deli</td>
<td>Flickr</td>
<td>flk</td>
</tr>
<tr>
<td>ESPN</td>
<td>espn</td>
<td>Yahoo! Stock Quote</td>
<td>stock</td>
<td>Dictionary (Answers.com)</td>
<td>a</td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<center><a class="hint" href="/documentation/jeremys_picks">More commands...</a></center>
</td>
</tr>
<tr>
<td>
<center><a class="hint" href="/parser/parse?command=ge">Yet more commands...</a></center>
</td>
</tr>
</table>
</center>
<center>
<div class="footer"><a href="http://jonaquino.blogspot.com/2005/06/yubnub-my-entry-for-rails-day-24-hour.html">What Is This Thing?</a> | <a href="/parser/parse?command=ls">List All Commands (ls)</a> <a href="http://yubnub.org/commands.xml"><img src="/images/xml_button.gif" alt="Subscribe to RSS feed"/></a> | <a href="/parser/parse?command=create">Create a New Command</a> | <a href="/documentation/describe_installation">Installing YubNub</a> | <a href="/documentation/describe_advanced_syntax">Advanced Syntax</a><br/>
<a href="/kernel/most_used_commands">Most-Used Commands</a> | <a href="/parser/parse?command=ge">Golden Eggs (Notable Commands)</a> <a href="http://yubnub.org/golden_eggs.xml"><img src="/images/xml_button.gif" alt="Subscribe to RSS feed"/></a> | <a href="/documentation/jeremys_picks">Jeremy's Picks</a><br/>
<a href="http://groups.google.com/group/YubNub">Community (Google Group)</a> | <a href="http://www.editthis.info/yubnub/">Wiki</a> | <a href="http://yubnub.blogspot.com/">YubNub Blog</a> | <a href="/parser/parse?command=tec+yubnub">Press (Technorati)</a> | <a href="/documentation/display_acknowledgements">Acknowledgements</a> | <a href="mailto:jonathan.aquino@gmail.com">Typos? Spam? Contact Jon</a></div>
</center>
EOD;
        $scrubbed = Notes_Scrubber::scrub(trim($html));
        // echo '<pre>' . xnhtmlentities($scrubbed) . '</pre>';
        $this->assertEqual(trim($expected), trim($scrubbed));
    }

    public function testNing5975Google() {
        // Characterization test - checks that the output is the same as the output before the fix [Jon Aquino 2007-08-21]
        $html = <<<EOD
<html><head><meta http-equiv="content-type" content="text/html; charset=ISO-8859-1"><title>Google</title><style><!--
body,td,a,p,.h{font-family:arial,sans-serif}
.h{font-size:20px}
.h{color:#3366cc}
.q{color:#00c}
.ts td{padding:0}.ts{border-collapse:collapse}--></style>
<script>
<!--
window.google={kEI:"NlbLRta6ApL6ggOckYTFBg",kEXPI:"17259",kHL:"en"};function sf(){document.f.q.focus();}
// -->
</script>
</head><body bgcolor=#ffffff text=#000000 link=#0000cc vlink=#551a8b alink=#ff0000 onload="sf();if(document.images){new Image().src='/images/nav_logo3.png'}" topmargin=3 marginheight=3><div align=right id=guser style="font-size:84%;padding:0 0 4px" width=100%><nobr><a href="/url?sa=p&pref=ig&pval=3&q=http://www.google.ca/ig%3Fhl%3Den&usg=AFQjCNH9TTed08sJL_DKraFsuSMDFvW1gw">iGoogle</a> | <a href="https://www.google.com/accounts/Login?continue=http://www.google.ca/&hl=en">Sign in</a></nobr></div><center><br clear=all id=lgpd><img alt="Google" height=110 src="/intl/en_ca/images/logo.gif" width=276><br><br><form action="/search" name=f><style>#lgpd{display:none}</style><script defer><!--
//-->
</script><table border=0 cellspacing=0 cellpadding=4><tr><td nowrap><font size=-1><b>Web</b>&nbsp;&nbsp;&nbsp;&nbsp;<a class=q href="http://images.google.ca/imghp?ie=ISO-8859-1&oe=ISO-8859-1&hl=en&tab=wi">Images</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class=q href="http://groups.google.ca/grphp?ie=ISO-8859-1&oe=ISO-8859-1&hl=en&tab=wg">Groups</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class=q href="http://news.google.ca/nwshp?ie=ISO-8859-1&oe=ISO-8859-1&hl=en&tab=wn">News</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class=q href="http://maps.google.ca/maps?ie=ISO-8859-1&oe=ISO-8859-1&hl=en&tab=wl">Maps</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class=q href="http://scholar.google.com/schhp?ie=ISO-8859-1&oe=ISO-8859-1&hl=en&tab=ws">Scholar</a>&nbsp;&nbsp;&nbsp;&nbsp;<b><a href="/intl/en/options/" class=q>more&nbsp;&raquo;</a></b></font></td></tr></table><table cellpadding=0 cellspacing=0><tr valign=top><td width=25%>&nbsp;</td><td align=center nowrap><input name=hl type=hidden value=en><input type=hidden name=ie value="ISO-8859-1"><input maxlength=2048 name=q size=55 title="Google Search" value=""><br><input name=btnG type=submit value="Google Search"><input name=btnI type=submit value="I'm Feeling Lucky"></td><td nowrap width=25%><font size=-2>&nbsp;&nbsp;<a href=/advanced_search?hl=en>Advanced Search</a><br>&nbsp;&nbsp;<a href=/preferences?hl=en>Preferences</a><br>&nbsp;&nbsp;<a href=/language_tools?hl=en>Language Tools</a></font></td></tr><tr><td align=center colspan=3><font size=-1>Search: <input id=all type=radio name=meta value="" checked><label for=all> the web </label><input id=cty type=radio name=meta value="cr=countryCA"><label for=cty> pages from Canada </label></font></td></tr></table></form><br><font size=-1>Google.ca offered in: <a href="http://www.google.ca/fr">Français</a> </font><br><br><br><font size=-1><a href="/intl/en/ads/">Advertising&nbsp;Programs</a> - <a href="/services/">Business Solutions</a> - <a href="/intl/en/about.html">About Google</a> - <a href=http://www.google.com/ncr>Go to Google.com</a></font><p><font size=-2>&copy;2007 Google</font></p></center></body></html>
EOD;
        $expected = <<<EOD
<div align="right" id="guser" style="font-size:84%;padding:0 0 4px"></div>
<center><br clear="all" id="lgpd"/>
<img alt="Google" height="110" src="/intl/en_ca/images/logo.gif" width="276"/><br/>
<br/>

<br/>
<font size="-1">Google.ca offered in: <a href="http://www.google.ca/fr">Français</a></font><br/>
<br/>
<br/>
<font size="-1"><a href="/intl/en/ads/">Advertising Programs</a> - <a href="/services/">Business Solutions</a> - <a href="/intl/en/about.html">About Google</a> - <a href="http://www.google.com/ncr">Go to Google.com</a></font>
<p><font size="-2">©2007 Google</font></p>
</center>
EOD;
        $scrubbed = Notes_Scrubber::scrub(trim($html));
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/a.txt', $scrubbed);
        $this->assertEqual(trim($expected), trim($scrubbed));
    }

    public function testNing6123a() {
        $s = "<img style=\"xss:expression(document.write('WE SHOULD USE THIS SOFTWARE!!!')\"/>";
        $this->assertEqual(Notes_Scrubber::scrub($s), '<img/>');
        $s = "<img style=\"xss:expression(alert(document.cookie))\"/>";
        $this->assertEqual(Notes_Scrubber::scrub($s), '<img/>');
        $s = "<img style=\"xss:expression(alert('FIX THIS CODE'))\"/>";
        $this->assertEqual(Notes_Scrubber::scrub($s), '<img/>');
        $s = '<img src="vbscript:msgbox(&quot;2222S&quot;)"/>';
        $this->assertEqual(Notes_Scrubber::scrub($s), '<img/>');
        $s = "<img src=\"jav%0Dascript:alert('123');\"/>";
        $this->assertEqual(Notes_Scrubber::scrub($s), '<img/>');
        $s = "<img src=\"jav%09ascript:alert('123');\"/>";
        $this->assertEqual(Notes_Scrubber::scrub($s), '<img/>');
        $s = "<img src=\"jav%20ascript:alert('123');\"/>";
        $this->assertEqual(Notes_Scrubber::scrub($s), '<img/>');
        $s = "<img src=\"foo.gif\"/>";
        $this->assertEqual(Notes_Scrubber::scrub($s), $s);
        $s = "<img src=\"http://somewhere/foo.gif\"/>";
        $this->assertEqual(Notes_Scrubber::scrub($s), $s);
        $s = "<img src=\"https://somewhere/foo.gif\"/>";
        $this->assertEqual(Notes_Scrubber::scrub($s), $s);
    }


    public function testNing6123b() {
        // From http://feedparser.org/docs/html-sanitization.html
        $s = "<span style=\"background: url(javascript:window.location='http://example.org/')\">nasty tricks</span>";
        $this->assertEqual(Notes_Scrubber::scrub($s), '<span>nasty tricks</span>');

        $s = "<span style=\"any: expression(window.location='http://example.org/')\">nasty tricks</span>";
        $this->assertEqual(Notes_Scrubber::scrub($s), '<span>nasty tricks</span>');

        $s = '<span style="&#97;&#110;&#121;&#58;&#32;&#101;&#120;&#112;&#114;&#101;&#115;&#115;&#105;&#111;&#110;&#40;&#119;&#105;&#110;&#100;&#111;&#119;&#46;&#108;&#111;&#99;&#97;&#116;&#105;&#111;&#110;&#61;&#39;&#104;&#116;&#116;&#112;&#58;&#47;&#47;&#101;&#120;&#97;&#109;&#112;&#108;&#101;&#46;&#111;&#114;&#103;&#47;&#39;&#41;">foo</span>';
        $this->assertEqual(Notes_Scrubber::scrub($s), '<span>foo</span>');

        $s = '<span style="&#x61;&#x6e;&#x79;&#x3a;&#x20;&#x65;&#x78;&#x70;&#x72;&#x65;&#x73;&#x73;&#x69;&#x6f;&#x6e;&#x28;&#x77;&#x69;&#x6e;&#x64;&#x6f;&#x77;&#x2e;&#x6c;&#x6f;&#x63;&#x61;&#x74;&#x69;&#x6f;&#x6e;&#x3d;&#x27;&#x68;&#x74;&#x74;&#x70;&#x3a;&#x2f;&#x2f;&#x65;&#x78;&#x61;&#x6d;&#x70;&#x6c;&#x65;&#x2e;&#x6f;&#x72;&#x67;&#x2f;&#x27;&#x29;">foo</span>';
        $this->assertEqual(Notes_Scrubber::scrub($s), '<span>foo</span>');
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
