<?php

XG_App::includeFileOnce('/lib/XG_Embed.php');

class Chat_EmbedController extends W_Controller {

    public function action_embed1($args) { $this->renderEmbed($args['embed'], $args['maxEmbedWidth'],1); }
    public function action_embed2($args) { $this->renderEmbed($args['embed'], $args['maxEmbedWidth'],2); }
    public function action_embed3($args) { $this->renderEmbed($args['embed'], $args['maxEmbedWidth'],3); }
    private function renderEmbed($embed, $maxEmbedWidth, $embedColumns) {
	$this->_widget->includeFileOnce('lib/helpers/Chat_ConnectionHelper.php');
        $this->title = xg_html('CHAT');

	$this->chatServer = Chat_ConnectionHelper::getChatServer($this->_widget->config);

        // this logic is a bit fragile.. is there a better way?
        if ($embedColumns == 1) {
                if ($maxEmbedWidth === 220) { //the left column
                        $this->moduleLocation = 'left';
                } else { //the right column
                        $this->moduleLocation = 'right';
                }
        } else {
                $this->moduleLocation = 'middle';
        }

        $uriHash = hash('ripemd160', $_SERVER['REQUEST_URI']);

        $this->userOnlineStatus = Chat_ConnectionHelper::getUserOnlineStatus();
        $this->startChatUrl = $this->_buildUrl('index', 'startChat', array('embedType' => $this->moduleLocation, 'xn_out' => 'json', 'r' => $uriHash, 'userOnlineStatus' => $this->userOnlineStatus));
        $this->embedId = $embed->getLocator();
        $this->chatStatus = $embed->get('chatstatus');
        $this->url = xnhtmlentities($this->_buildUrl('embed', 'setValues',
						array('id' => $this->embedId,'xn_out'=>'json','moduleLocation'=>$this->moduleLocation, 'sidebar' => XG_App::isSidebarRendering() ? '1' : '0')));
        $this->render('embed');
    }

    public function action_setValues() {
        $embed = XG_Embed::load($_GET['id']);
        $this->moduleLocation = $_GET['moduleLocation'] ? $_GET['moduleLocation'] : 'middle';
        $embed->set('chatstatus', $_POST['enabled'] ? 'enabled' : 'disabled');
        $this->done = 1;
        if ($_POST['enabled']) {
            ob_start();
            $this->_widget->includeFileOnce('lib/helpers/Chat_ConnectionHelper.php');
            $this->chatServer = Chat_ConnectionHelper::getChatServer($this->_widget->config);
            $this->startChatUrl = $this->_buildUrl('index', 'startChat', array('embedType' => $this->moduleLocation, 'xn_out' => 'json'));
            $this->renderPartial('chat','embed');
            $this->data = ob_get_clean();
        }

        // invalidate all caches
        if ($_GET['sidebar']) {
            XG_App::includeFileOnce('/lib/XG_Cache.php');
            XG_Query::invalidateCache(XG_Cache::INVALIDATE_ALL);
        }
    }
}
