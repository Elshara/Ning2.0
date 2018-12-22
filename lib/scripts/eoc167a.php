<?php
if (! XN_Profile::current()->isOwner()) { throw new Exception('Not allowed'); }
define('NF_APP_BASE',dirname(__FILE__));
define('NF_BASE_URL', '');
//define('W_INCLUDE_PREFIX', dirname(__FILE__));
require XN_INCLUDE_PREFIX . '/WWF/bot.php';
W_WidgetApp::includeFileOnce('/lib/XG_App.php');
XG_App::includeFileOnce('/lib/XG_Cache.php');
XG_App::includeFileOnce('/lib/XG_Query.php');
XG_App::includeFileOnce('/lib/XG_LangHelper.php');
XG_App::includeFileOnce('/lib/XG_Cache.php');
XG_App::includeFileOnce('/lib/XG_PagingList.php');
XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
call_user_func(array(W_Cache::getClass('app'), 'loadWidgets'));
echo '<pre>';
if ($_REQUEST['submit']) {
    foreach($_REQUEST['users'] as $u) {
            $o = User::load($u);
            $o->my->xg_index_status = 'unfinished';
            $o->save();
    }
    echo 'Done. <a href="?goback">go back</a>';
    exit;
}

function user2data($res, $users) {
    foreach($res as $user) {
        $q = $users->reqQuestions ? 0 : 1; // no questions => ok
        foreach($users->reqQuestions as $q) {
            $q += (int)('' != $user->my->$q);
        }
        $users->list[mb_strtolower($user->title)] = array(
            'author' => mb_strtolower($user->contributorName),
            'fullName' => $user->my->fullName,
            'prof_q' => $q,
            'status' => $user->my->xg_index_status,
        );
        $user->my = null;
        XN_Cache::_remove($user);
    }
}
if ( 1 || $_REQUEST['go'] ) {
    $info = XN_Query::create('Content_Count')->filter('owner')->rollup('type')->execute();
    $total = $info['User'];
    $users = new stdclass;

    XG_App::includeFileOnce('/widgets/profiles/lib/helpers/Profiles_ProfileQuestionHelper.php');
    $users->reqQuestions = array();
    foreach (Profiles_ProfileQuestionHelper::getQuestions(W_Cache::getWidget('profiles')) as $q) {
        if ($q['required']) {
            $users->reqQuestions[] = 'xg_profiles_answer_q'.$q['questionCounter'];
        }
    }

    $brokenUsers = array();
    //
    // Load all user objects
    //
    $start = microtime(true);
    AsyncURL::$maxConn = 10;
    for ($page = 50, $i = 0; $i<$total; $i+=$page) {
        $q = QueryAsync::create('Content')
            ->filter('owner')
            ->filter('type','=','User')
            ->begin($i)->end($i+$page)
            ->order('id','asc');
        $q->execute('user2data',$users);
    }
    QueryAsync::wait();
    printf("Loaded %d/%d users in %.4f sec. Memory: %d, max=%d<br>", count($users->list), $info['User'], microtime(true)-$start, memory_get_usage(),memory_get_peak_usage());

    //
    // Scan it
    //
    $users = $users->list;
    foreach($users as $u=>&$i) {
        if ($i['author'] != $u) $brokenUsers[$u] = 1;
    }

    //
    // Load members alias
    //
    $start = microtime(true);
    $ma = XN_ProfileSet::load('members');
    $members = array();
    for ($i = 0, $page = 100; $i<$ma->size;$i+=$page) {
        foreach ($ma->members($i, $i+$page) as $m) {
            $members[mb_strtolower($m)] = 1;
        }
    }
    printf("Loaded %d/%d members in %.4f sec. Max memory: %d<br>", count($members), $ma->size, microtime(true)-$start, memory_get_peak_usage());

    //
    // Scan it
    //
    $brokenUsers = array_flip( array_merge( array_keys($brokenUsers),
        array_diff( array_keys($users), array_keys($members) ) ,
        array_diff( array_keys($members), array_keys($users) )
    ) );
?>
<h1>Found <%=count($brokenUsers)%> broken user(s)</h1>
<style>
td.b{background-color:red}
</style>
<form method="post" action="" style="margin:0">
<input type="submit" name="submit" value="Mark as broken">
<table border=1>
<tr>
    <th><input class="checkbox" type="checkbox" name="toggle" onclick="var e = this.form.elements['users[]'];for(var i = 0;i<e.length;i++)e[i].checked=this.checked">
    <th>screenName
    <th>fullName
    <th>author
    <th>prof-questions
    <th>member
    <th>status
</tr>
<?php
    foreach(array_keys($brokenUsers) as $u) {
        echo "<tr>";
        echo "<td><input type=checkbox name=users[] value='$u'>";
        echo "<td>$u";
        echo "<td ", $users[$u]['fullName'] ? '>' : 'class=b>', $users[$u]['fullName'] ? $users[$u]['fullName'] : 'N/A';
        echo "<td ", $users[$u]['author'] == $u ? '>' : 'class=b>', $users[$u]['author'] ? $users[$u]['author'] : 'Mr Anonymous';
        echo "<td ", $users[$u]['prof_q'] ? '>' : 'class=b>', $users[$u]['prof_q'] ? 'YES' : 'NO';
        echo "<td ", (isset($members[$u]) && isset($users[$u])) ? '>' : 'class=b>', isset($members[$u]) ? 'YES' : 'NO';
        echo "<td>", $users[$u]['status'];
    }
?>
</table>
<input type="submit" name="submit" value="Mark as broken">
</form>
<?php
}



exit; // bye





/*
 *	Asyncronous content queries. Syntax:
 *		AsyncURL::$maxConn = 20; // change to match your purposes
 *		for($page = 100, $i = 0; $i<$total; $i+=$page) {
 *			$q = QueryAsync::create('Content')
 *				->filter('owner')
 *				->filter('type','=','User')
 *				->begin($i)
 *				->end($i+$page);
 *			$q->execute($my_callback, ..args..); // my_callback receives query result as first arg and the rest as other args
 *		}
 *		QueryAsync::wait();
 *		// voila!
 *
 */

class AsyncURL {
    protected static 	$mh = null, 		// CURL handle
                        $map = array(), 	// req => callback
                        $pool = array();	// queued queries

    // Maximum number of simultaneous connections
    public static $maxConn = 10;

    /**
     *  Executes asyncronous HTTP request.
     *
     *  @param      $req   		string|curl		URL (get) or CURL object.
     *  @param		$callback   callback		Receives ($curl, $headers, $content) when request is finished
     *  @return     void
     */
    public static function request ($req, $callback) { # void
        if (!self::$mh) {
            self::$mh = curl_multi_init();
            register_shutdown_function(__CLASS__.'::_shutdown');
        }
        if (count(self::$map) >= self::$maxConn) {
            #echo "pooling $req<br>";
            self::$pool[] = func_get_args();
        } else {
            self::_start(func_get_args());
        }
        self::execute(false);
    }

    /**
     *  Executes queued requests and fetches data for executing requests.
     *  Returns 0 if nothing to do and non-0 otherwise.
     *
     *  @return     int
     */
    public static function execute ($wait = true) {
        if (!self::$mh) {
            return 0;
        }
        $running = null;
        for ($i = 0; $i<2; $i++) { // for some reasons curl wants it to be called twice ...
            curl_multi_exec(self::$mh, $running);
            while($res = curl_multi_info_read(self::$mh)) {
                $ch = $res['handle'];

                list($header, $content) = explode("\r\n\r\n", curl_multi_getcontent($ch), 2);
                $headers = self::_parseHeaders($header);

                call_user_func(self::$map["$ch"], $ch, $headers, $content);
                unset(self::$map["$ch"]);

                curl_multi_remove_handle(self::$mh, $ch);
                curl_close($ch);
                #echo "removing $ch<br>";
                if (count(self::$pool)) {
                    self::_start(array_shift(self::$pool));
                    $i--;
                    continue 2;
                }
            }
            if (!$running) {
                return 0;
            }
        }
        if ($wait) {
            usleep(1000);
        }
        return $running;
    }

    // shutdown handler. waits until pending requests finish
    public static function _shutdown() { # void
        while (self::execute()) 1;
        if (self::$mh) {
            curl_multi_close(self::$mh);
        }
    }

    // fire request
    protected static function _start($args) { # void
        if (is_string($ch = $args[0])) {
            $ch = curl_init($ch);
        }
        #echo "<b>starting</b> $ch<br>";
        self::$map["$ch"] = $args[1];
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_multi_add_handle(self::$mh, $ch);
    }

    protected static function _parseHeaders($hdrString) { # hash
        $headers = array();
        foreach(explode("\r\n", $hdrString) as $h) {
            $h = explode(':', $h, 2);
            if (2 != count($h)) {
                continue;
            }
            $n = mb_strtolower(trim($h[0]));
            $v = trim($h[1]);

            if (isset($headers[$n])) {
                if (!is_array($headers[$n])) {
                    $headers[$n] = array($headers[$n]);
                }
                $headers[$n][] = $v;
            } else {
                $headers[$n] = $v;
            }
        }
        return $headers;
    }
}

class RESTAsync extends XN_REST {
    // do not call it directly!
    public static function getCurl($method, $url, &$body = null) {
        $requestHeaders = array();
        $requestHeaders['Expect'] = '';
        $urlInfo = self::prepareUrl($url);

        $requestHeaders['X-Ning-RequestToken'] = XN_REST::$SECURITY_TOKEN;
        $slot = $urlInfo['slot'];
        $url  = $urlInfo['url'];
        if (isset($urlInfo['host'])) {
            $requestHeaders['Host'] = $urlInfo['host'];
        }

        // Based on the prepared URL and slot, make sure the slot is initialized
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, 'XN-REST 0.2');
        curl_setopt($ch, CURLOPT_TIMEOUT, self::getRequestTimeout());

        // Clean out the headers and set the URL
        curl_setopt($ch, CURLOPT_HTTPHEADER, array());
        curl_setopt($ch, CURLOPT_URL, $url);

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        } elseif ($method == 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        } else {
            // Setting the method to GET and then to the custom method
            // Ensures that any Content-Length and Content-Type headers from
            // a previous POST or PUT are removed.
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            // Without a "Connection: close" on a HEAD request, curl_exec()
            // waits (after receiving the response) instead of returning
            // [ David Sklar 2006-10-10 ]
            if ($method == 'HEAD') {
                $requestHeaders['Connection'] = 'close';
            }
        }

        // Allow for multipart content
        if (is_array($body) || mb_strlen($body)) {
      /* CURLOPT_POST is set only when there's a body to handle
       * responses with no body (NING-7074) */
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        $combinedRequestHeaders = array();
        foreach ($requestHeaders as $header => $value) {
            $combinedRequestHeaders[] = "$header: $value";
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $combinedRequestHeaders);
        return $ch;
   }
}
class QueryAsync extends XN_Query {
    protected static $map = array();
    protected $subject;

    // Must be used instead of XN_Query::create()
    public static function create($subject) {
        $q = new QueryAsync($subject);
        $q->subject = $subject;
        return $q;
    }
    public function wait() { # void
        while(AsyncURL::execute()) 1;
    }

    /**
     *  Executes query.
     *
     *  @param      $callback   callback    Callback to process query results
     *  @param		...			list		Extra arguments to pass to callback (after the query results)
     *  @return     void
     */
    public function execute($callback /*, ... */){
        $this->callback = func_get_args();
        return $this->_executeQueryAsync(NULL);
    }

    // internal
    protected static function _execQuery($query, $url) {
        $ch = RESTAsync::getCurl('GET', $url);
        self::$map["$ch"] = $query;
        AsyncURL::request($ch, __CLASS__.'::_asyncCallback');
    }
    // internal
    public static function _asyncCallback($ch, $headers, $content) { # void
        $query = self::$map["$ch"];
        unset(self::$map["$ch"]);

        $func = $query->callback[0];
        $query->callback[0] = $query->_executeQueryAsync((string)$content);
        call_user_func_array($func, $query->callback);
        $query->callback = NULL;
    }

    protected function _executeQueryAsync($content){
        try {
            // Contact queries go against a different endpoint and do not return
            // atom feeds, so they have to be handled differently than the other
            // kinds of queries
            if (strcasecmp($this->subject,'Contact') == 0) {
                // Build the URL
                $url = $this->_toContactEndpoint();
                // Retrieve the URL
                if (NULL === $content) {
                    return self::_execQuery($this, $url);
                }
                $x = XN_AtomHelper::XPath($content);
                // NING-3099: Check for error in the response body [ David Sklar 2006-09-29 ]
                if (! is_null($error = $x->textContent('/errors/element/error', null, true))) {
                    throw new Exception($error);
                }

                // set totalCount, resultFrom, resultTo
                $this->totalCount = (integer) $x->textContent('/contacts/total');
                $this->resultFrom = (integer) $x->textContent('/contacts/begin');
                $this->resultTo   = (integer) $x->textContent('/contacts/end');
                // Turn the results into an array of XN_Contact objects
                return XN_Contact::_loadFromRestFeed($x);
            } else {
                $version = ($this->subject == 'Search') ? '1.1' : '1.0';
                if (NULL === $content) {
                    return self::_execQuery($this, $this->_toAtomEndpoint());
                }
                $x = XN_AtomHelper::XPath($content, $version);
                $this->totalCount = (integer) $x->textContent('/atom:feed/xn:size');
                $this->resultFrom = (integer) $this->begin;
                if ($this->end == 0) {
                    $this->resultTo = $this->totalCount;
                } else {
                    $this->resultTo   = min((integer) $this->end, $this->totalCount);
                }
                if ($this->returnIds == 'true') {
                    return self::_atomFeedToIDs($x);
                }
                else if (strcasecmp($this->subject, self::SUBJECT_CONTENT)==0) {
                    return XN_Atomhelper::loadFromAtomFeed($x, 'XN_Content', false);
                }
                else if (strcasecmp($this->subject, self::SUBJECT_TAG)==0) {
                    return XN_AtomHelper::loadFromAtomFeed($x, 'XN_Tag', false);
                }
                else if ((strcasecmp($this->subject, self::SUBJECT_TAG_VALUECOUNT)==0)||
                (strcasecmp($this->subject, self::SUBJECT_CONTENT_COUNT)==0)) {
                    return self::_loadRollupFromAtomFeed($x);
                }
                else if (strcasecmp($this->subject, self::SUBJECT_SEARCH)==0) {
                    return XN_AtomHelper::loadFromAtomFeed($x, 'XN_SearchResult', false, false);
                }
                else if (strcasecmp($this->subject, self::SUBJECT_INVITATION)==0) {
                    return XN_AtomHelper::loadFromAtomFeed($x, 'XN_Invitation', false, false);
                }
            }
        } catch (XN_Exception $ex) {
            // NING-3304: Queries against single IDs that don't find anything return
            // a 404; in that case, we should just return an empty result set [ David Sklar 2006-10-05 ]
        // NING-5887: Except that search queries return a 404 if the searcher core is missing
            // [ David Sklar 2007-08-03]
            if (($ex->getCode() == 404) && (strcasecmp($this->subject, self::SUBJECT_SEARCH) != 0)) {
                return array();
            } else {
                throw XN_Exception::reformat("Failed query:\n".$this->debugString(), $ex);
            }
        } catch (Exception $ex) {
            throw XN_Exception::reformat("Failed query:\n".$this->debugString(), $ex);
        }
        return 'x';
    }
}
