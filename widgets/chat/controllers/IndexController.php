<?php

class Chat_IndexController extends W_Controller {

    public function action_index() {
        $this->_widget->includeFileOnce('lib/helpers/Chat_ConnectionHelper.php');

        $this->chatServer = Chat_ConnectionHelper::getChatServer($this->_widget->config);
        $embedType = 'full';
        $this->moduleLocation = $embedType;
        $uriHash = hash('ripemd160', $_SERVER['REQUEST_URI']);
        $this->userOnlineStatus = Chat_ConnectionHelper::getUserOnlineStatus();
        $this->startChatUrl = $this->_buildUrl('index', 'startChat', array('embedType' => $embedType, 'xn_out' => 'json', 'r' => $uriHash, 'userOnlineStatus' => $this->userOnlineStatus));
    }

    public function action_read() {
        $this->_widget->includeFileOnce('lib/helpers/Chat_ConnectionHelper.php');
        $token = $_POST['t'];
        $this->cachedData = Chat_ConnectionHelper::retrieveDataFromCache($token);
    }

    /**
	 *  Returns the data for starting chat.
     *
     *  @param      $embedType   string    full|
     *  @return     void
     */
    public function action_startChat () { # void
        $this->_widget->includeFileOnce('lib/helpers/Chat_ConnectionHelper.php');
        $params = Chat_ConnectionHelper::getIFrameParamsAndPutDataInCache($this->_user, $_REQUEST['embedType'], $_REQUEST['r'], $_REQUEST['userOnlineStatus']);
        $this->token = $params['token'];
        $this->appSubdomain = $params['appSubdomain'];
        $this->appHost = $params['appHost'];
    }

}

?>
