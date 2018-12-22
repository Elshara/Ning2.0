<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/test/TestGroupRunner.php';

class WebTestGroupRunner extends TestGroupRunner {
    public function __construct($files = null) {
        if ($_GET['q']) {
            $files = self::glob_files('./' . $_GET['q']);
            $files = array_merge($files, self::glob_files('./*/' . $_GET['q']));
        }
        if (is_null($files)) {
        	$files = array();
        	// get the web test cases
            $files = array_merge($files, self::glob_files('./*Test.php'));
            $files = array_merge($files, self::glob_files('./*/*Test.php'));
        	// get the cmdline test cases
            $files = array_merge($files, self::glob_files('./*Test.php',true));
            $files = array_merge($files, self::glob_files('./*/*Test.php',true));
        }
        $this->files[] = 'TestObjectsDeletedTest.php';
        foreach ($files as $file) {
            if (basename($file) == 'TestObjectsDeletedTest.php') { continue; }
            if (strpos(basename($file), 'Abstract') !== false) { continue; }
            $this->files[] = preg_replace('@^./@','',$file);
        }
    }

    public function run() {
    	if (! XN_Profile::current()->isOwner()) {
            // Some tests assume that you are the app owner [Jon Aquino 2007-09-21]
            XN_Profile::signOut();
            header('Location: http://' . $_SERVER['HTTP_HOST'] . '/main/authorization/signIn?target=' . xnhtmlentities(urlencode(self::currentUrl())));
            exit;
        }

        XN_Content::create('TestStartMarker')->save();
 ?>
<html>
<head>
    <title>All Unit Tests</title>
    <style>
    * {
      margin: 0;
      padding: 0;
    }
    body {
      font:76%/140% Verdana,Arial,Helvetica,sans-serif;
    }
    h1 {
      font-size: 1em;
      margin-top: 1em;
    }
    div.processing {
      font-size: 1.5em;
      margin-bottom: 2.5em;
    }
    div.success,
    div.failure {
      font-weight: bold;
      font-size: 3em;
      line-height: 3em;
      text-align: center;
      color: white;
    }
    div.success {
      background: #2aff2a;
    }
    div.failure {
      background: #711c1c;
    }
    div.success span,
    div.failure span {
      font-size: 2em;
    }
    #xn_bar {
      display: none;
    }
    </style>
</head>
<body>
<ning:ningbar/>
<div id="status" class="processing"><img style="width: 30px; height: 30px;" src="/xn_resources/widgets/index/gfx/spinner.gif" alt=""> <span>Running tests...</span></div>
<div id="failures"></div>
<div id="successes"></div>
<script>
    var failedTestCount = 0;
    var filenames = [];
    <?php
    foreach ($this->files as $testFile) {
        echo 'filenames.push("' . $testFile . '");';
    } ?>
    var testCount = filenames.length;
    var i = 0;
    var testRunner = {
        runNextTest: function() {
            if (filenames.length == 0) {
                if (failedTestCount == 0) {
                    dojo.byId('status').innerHTML = '<span>&#10004;</span> All tests passed';
                    dojo.byId('status').className = 'success';
                    document.title += ' PASSED';
                } else {
                    dojo.byId('status').innerHTML = '<span>&#10008;</span> ' + failedTestCount + ' ' + (failedTestCount == 1 ? 'test' : 'tests') + ' failed';
                    dojo.byId('status').className = 'failure';
                    document.title += ' ' + failedTestCount +' FAILED';
                }
                return;
            }
            i++;
            var filename = filenames.pop();
            dojo.byId('status').getElementsByTagName('span')[0].innerHTML = 'Running test ' + i + ' / ' + testCount + ': <a href="/test/' + filename + '">' + filename.replace(/.php/, '') + '</a>';
            document.title = i + ' / ' + testCount + ' Tests';
            var start = window.location.href.replace(/.*start=/, '');
            if (start && i < start) {
                testRunner.runNextTest();
                return;
            }
            dojo.io.bind({
                url: '/test/' + filename + '?json=yes',
                preventCache: true,
                encoding: 'utf-8',
                mimetype: 'text/javascript',
                load: dojo.lang.hitch(this, function(type, data, event){
                    if (! data) {
                        this.requestErrorFailure(event, filename);
                    } else if (! data.success) {
                        failedTestCount++;
                        var container = dojo.byId('failures');
                        dojo.dom.insertAtPosition(dojo.html.createNodesFromText(data.html)[0], container, 'first');
                        this.insertFileAndAdvance(container, filename);
                    } else {
                        var container = dojo.byId('successes');
                        dojo.dom.insertAtPosition(dojo.html.createNodesFromText(data.html)[0], container, 'first');
                        this.insertFileAndAdvance(container, filename);
                    }
                }),
                error: dojo.lang.hitch(this, function(type, data, event) {
                        this.requestErrorFailure(event, filename);
                })
            });
        },
        requestErrorFailure: function(event, filename) {
            failedTestCount++;
            var container = dojo.byId('failures');
            dojo.dom.insertAtPosition(dojo.html.createNodesFromText('<div style="border: 4px dotted red; background: #fcfca7; padding: 1em;">' + event.responseText + '</div>')[0], container, 'first');
            this.insertFileAndAdvance(container, filename);
        },
        insertFileAndAdvance: function(container, filename) {
            dojo.dom.insertAtPosition(dojo.html.createNodesFromText('<h1><a href="/test/' + filename + '">' + filename + '</a></h1>')[0], container, 'first');
            // Delay for 2 seconds, to allow the server to "rest", to minimize the likelihood of timeouts [Jon Aquino 2008-02-21]
            setTimeout(dojo.lang.hitch(this, this.runNextTest), 2000);
        }
    };
    testRunner.runNextTest();
</script>
</body>
</html>
<?php
    }
    protected static function currentUrl() {
        return str_replace('/index.php', '', 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    }
}

