<?php /* Is this file still used? [Jon Aquino 2008-01-11] */ ?>
<?php xg_header('profile', $this->pageTitle, null) ?>
<?php XG_App::ningLoaderRequire('xg.profiles.embed.chatterwall','xg.shared.SimpleToolbar');
    $isMyPage = $this->profile->screenName == XN_Profile::current()->screenName;
?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_colgroup">
            <div class="xg_3col first-child">
                <h1 id="chatterwall"><%= xnhtmlentities($this->pageTitle) %></h1>
                <div class="easyclear">
                    <ul class="backlink navigation">
                        <?php $msg = ($isMyPage ? 'BACK_TO_MY_PAGE' : 'BACK_TO_USERNAMES_PAGE');
                            $msg = xg_html($msg, xnhtmlentities(xg_username($this->profile))); ?>
                        <li><a href="<%= User::quickProfileUrl($this->profile->screenName) %>"><%= $msg %></a></li>
                    </ul>
                </div>

                <div class="xg_module">
                    <?php $this->renderPartial('fragment_chatter_head', 'chatter', array('numComments' => $this->commentInfo['numComments'])) ?>
                    <div class="xg_module_body">
                        <?php $this->renderPartial('fragment_chatter_add','chatter',array('profile' => $this->profile, 'showCommentUrl' =>  $this->showCommentUrl)) ?>
                    </div>
                    <div class="xg_module_body">
                        <?php $this->renderPartial('fragment_chatter_list', 'chatter',array('chatters' => $this->commentInfo['comments'],
                                'isMyPage' => $isMyPage, 'friendStatus' => $this->friendStatus)); ?>
                        <?php
                            XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
                            XG_PaginationHelper::outputPaginationProper($this->_buildUrl('comment','list',$this->paginationTargetParams), 'page', $this->page, $this->numPages);
                        ?>
                    </div>
                </div>
            </div>
            <div class="xg_1col">
                <div class="xg_1col first-child"><?php xg_sidebar($this); ?></div>
            </div>
        </div>
    </div>
</div>
<?php xg_footer(); ?>
