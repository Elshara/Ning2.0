<?php
if(isset($_GET['favoritesOf'])){
    $screenname = $_GET['favoritesOf'];
}else{
    $screenname = $_GET['screenName'];
}
//the last page(if coming from inside) IE consider the swf file as the referrer [Zuardi Feb-27-2006]
if(!$this->parentPage){
    if(!$_GET['back_url']){
        $referrer = $_SERVER['HTTP_REFERER'];
    } else {
        $referrer = $_GET['back_url'];
    }
}
if(preg_match("/".$_SERVER['HTTP_HOST']."/" ,$referrer)){
    $internal_referrer = true;
}
$albumId = $_GET['albumId'];
if(!$_GET['feed_url']) {
    $feed_url = urlencode($this->_buildUrl('photo',$this->action, '?sort='.$_GET['sort'].'&screenName='.$screenname.'&id='.$albumId.'&tag='.$_GET['tag'].'&useTags='.$this->useTags));
} else {
    $feed_url = urlencode(XG_HttpHelper::addParameter($_GET['feed_url'], 'useTags', $this->useTags));
} ?>

<?php xg_header(W_Cache::current('W_Widget')->dir, $title = $this->pageTitle, null, array('displayHeader' => FALSE)); ?>

<style type="text/css" media="screen">
    body {
        background-color:#000!important;
        background-image:none!important;
    }
    #xg,
    #xg #xg_body {
        background-color:#222!important;
        background-image:none!important;
    }
    #xg #xg_foot {
        background-color:#333!important;
        background-image:none!important;
        color:#ccc!important;
    }
    body, #xg_body h1, #xg_body a, #xg_foot a {
        color: #ccc!important;
    }
</style>

<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
            <h1 style="margin-bottom:0.2em"><%= xnhtmlentities($title) %></h1>
            <p style="margin-bottom:2em"><strong>&#171; <a href="<%= xnhtmlentities(($internal_referrer)?$referrer:$this->parentPage) %>"><%= xnhtmlentities($this->parentLinkText) %></a></strong></p>
        </div>
        <div class="xg_4col first-child" style="text-align:center">
            <?php
            ob_start();
            $this->renderPartial('fragment_slideshowPlayer','photo', array(
                'feed' => $feed_url.urlencode('&fullscreen=true'),
                'layout'=> 'fullscreen',
                'title'=> $this->pageTitle,
                'start'=> $_GET['start']
            ));
            $playerHtml = trim(ob_get_contents());
            ob_end_clean();
            ?>
            <p class="loading"><%= xg_html('LOADING') %></p>
            <input type="hidden" id="playerHtml" value="<?php echo xnhtmlentities(preg_replace('/\s+/u', ' ', $playerHtml)) ?>" />
        </div>
    </div>
</div>
<?php xg_footer('<script src="' . xg_cdn('/xn_resources/widgets/photo/js/photo/slideshow.js') . '" type="text/javascript"></script>'); ?>
