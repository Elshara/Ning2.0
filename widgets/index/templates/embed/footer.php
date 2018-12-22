<?php
XG_App::ningLoaderRequire('xg.index.embed.footer');
/* BAZ-1374 -- don't link to the creator's profile if the current user can't see it */
if ($this->displayFooter) {
    if (XG_App::everythingIsVisible() || ($this->_user->isLoggedIn() && User::isMember($this->_user))) {
        $creatorLink = xg_userlink($this->ownerProfile);
    } else {
        $creatorLink = xnhtmlentities(xg_username($this->ownerProfile));
    }
 ?>
    <div id="xg_foot">
        <p class="left">
            <?php if ($this->ningBranding) { ?>
                <%= xg_html('COPYRIGHT_CREATED_BY_WITH_NING', xg_date('Y'), $creatorLink) %>
                &nbsp; <a href="<%= $this->cloneLink %>"><%= xg_html('CREATE_YOUR_OWN_NETWORK') %></a>
            <?php } else { ?>
                <%= xg_html('COPYRIGHT_CREATED_BY', xg_date('Y'), $creatorLink); %>
            <?php } ?>
        </p>
        <?php if (! $this->hideLinks) { ?>
            <p class="right xg_lightfont">
                <a href="<%= xnhtmlentities($this->_widget->buildUrl('embeddable', 'list')) %>"><%= xg_html('BADGES') %></a> &nbsp;|&nbsp;
                <?php if (!XG_SecurityHelper::userIsAdmin()) { ?>
                    <a <%= XG_JoinPromptHelper::promptToJoin($this->_widget->buildUrl('index', 'report')) %>><%= xg_html('REPORT_AN_ISSUE') %></a> &nbsp;|&nbsp;
                <?php } else { ?>
                    <a href="http://help.ning.com/"><%= xg_html('NEED_HELP_TITLE_CASE') %></a> &nbsp;|&nbsp;
                <?php } ?>
                <a href="<%= xnhtmlentities($this->_widget->buildUrl('authorization', 'privacyPolicy', array('previousUrl' => XG_HttpHelper::currentUrl()))) %>"><%= xg_html('PRIVACY') %></a> &nbsp;|&nbsp;
                <a href="<%= xnhtmlentities($this->_widget->buildUrl('authorization', 'termsOfService', array('previousUrl' => XG_HttpHelper::currentUrl()))) %>"><%= xg_html('TERMS_OF_SERVICE') %></a>
            </p>
        <?php } ?>
    </div>
<?php } ?>

</div>
<?php $this->renderPartial('_noJavascript', 'embed'); ?>
<?php XG_App::ningLoaderRequire('xg.index.panel'); ?>
<?php XG_HtmlLayoutHelper::ningFooter();
$numInfo = XG_LanguageHelper::getNumberFormat();
?>
<script type="text/javascript">
    <?php /* TODO: Eliminate "global" - xg is already global [Jon Aquino 2008-04-28] */ ?>
    xg.token = '<%= XG_SecurityHelper::getCsrfToken() %>';
    xg.cdn = 'http://<%= XN_AtomHelper::HOST_APP('static') %>';
    xg.version = '<%= XG_Version::currentCodeVersion() %>';
    xg.global = xg.global||{}; xg.global.currentMozzle = '<?php $r = XG_App::getRequestedRoute(); echo $r['widgetName'] ?>';
    xg.global.userCanInvite = <%= (XG_App::canSeeInviteLinks($this->_user) ? 'true' : 'false') %>;
    xg.global.appIsLaunched = <%= (XG_App::appIsLaunched() ? 'true' : 'false') %>;
    xg.global.requestBase = '<%= NF_BASE_URL %>';
    xg.global.locale = '<%= XG_LOCALE %>';
	xg.num_thousand_sep = <%= json_encode($numInfo['thousand']) %>;
if ("undefined" != typeof dojo) {
    dojo.setModulePrefix('dojo', 'src');
    dojo.setModulePrefix('xg.custom.shared', '/xn_resources/instances/shared/js');
    dojo.setModulePrefix('xg.shared', '/xn_resources/widgets/shared/js');
    dojo.setModulePrefix('xg.index', '/xn_resources/widgets/index/js');
<?php foreach ($this->widgets as $widget) {
          if ($widget->dir == $widget->root) { ?>
              dojo.setModulePrefix('xg.<%= $widget->dir %>', '/xn_resources/widgets/<%= $widget->dir %>/js');
<?php     }
      } ?>
<?php if (XG_HtmlLayoutHelper::hasDojo()) { ?>
    <?php /* Install the bind handler before calling ning.loader.require (BAZ-7381) [Jon Aquino 2008-05-02] */ ?>
    dojo.event.connect('before', dojo.io, 'bind', function(request) {
        if (! request.content) { request.content = {}; }
        if (request.url && (''+request.url).match(/\/xn\//)) { return; } // BAZ-7688 [Jon Aquino 2008-05-21]
        request.content.xg_token = xg.token;
    });
<?php } ?>
    (function(){
        var cnt = 0, onLoadHandler = function() {
            <?php /* execute the code only after the second call to this function: first call from addOnLoad, second call from ning.loader.require.
                So the code is executed only when both of calls are completed. */?>
            if (cnt++ != 1) return;
            <?php /* xg.addOnRequire() collects functions to run after calling ning.loader.require  [Jon Aquino 2007-01-20] */ ?>
            <%= $this->parseWidgets ? 'xg.shared.util.parseWidgets();' : '' %>
            var addOnRequireFunctions = xg.addOnRequire.functions;
            xg.addOnRequire = function(f) { f(); };
            if (addOnRequireFunctions) { dojo.lang.forEach(addOnRequireFunctions, function(onRequire) { onRequire.apply(); }); }
        };
        dojo.addOnLoad(onLoadHandler);
        <?php /* Must define a callback; otherwise ning.loader.require will be synchronous [Jon Aquino 2007-04-24]*/ ?>
        ning.loader.require('<%= implode("', '", XG_App::$ningLoaderRequireArgs) %>', onLoadHandler);
    })();
}
</script>
<?php
    //a way to insert more javascript(or html) at the end of the page [BAZ-1438]
    echo $this->extraHtml;

    /* BAZ-1741: Debugging via config setting */
    if (preg_match('@query(?:;|$)@u', W_Cache::getWidget('admin')->config['debug'])) {
        XG_App::includeFileOnce('/lib/XG_DebugHelper.php');
        XG_DebugHelper::printDebug();
    }
    /* BAZ-2287: Timing info in the footer */
    if (defined(NF::NF_DISPLAY_TIMING)) {
        XG_App::includeFileOnce('/lib/XG_DebugHelper.php');
        XG_DebugHelper::printTimingPlaceholder();
    }
?>
<?php $this->_widget->includePlugin('pageEnd'); ?>
<script>
    if ("undefined" != typeof dojo && dojo.byId('baz8252')) {
        var marker = dojo.byId('baz8252').innerHTML;
        dojo.io.bind({
            url: '<%= $this->_widget->buildUrl('index', 'logBaz8252') %>',
            method: 'post',
            mimetype: 'text/json',
            encoding: 'utf-8',
            preventCache: true,
            content: { marker: marker }
        });
    }
</script>
<div id="xg_overlay" style="display:none">
<!--[if lte IE 6.5]><iframe></iframe><![endif]-->
</div>
</body>
</html>
