<?php
/**  $Id: class.php 3530 2006-08-15 14:48:07Z andrew $
 *
 *	Class for handling facebook apps interaction.
 *	Differ from XG_FacebookHelper because it's included from facebook apps handlers (and doesn't use
 *	WWF/Bazel codebase).
 *
 *	Facebook files layout:
 *		/xn_resources/ext/facebook/$APPTYPE			Static files generated by bazel.
 *		/lib/scripts/facebook/$APPTYPE				Facebook apps handlers
 *		/xn_private/main-private-configuration.xml	Configuration. File is read directly
 *
 *	Currently supports apps:
 *		photo
 *		video
 *		music
 *
 **/
ini_set('error_log', NF_APP_BASE . '/xn_private/xn_volatile/error.log');
require_once NF_APP_BASE . '/lib/ext/facebook/facebook.php';

class XG_FacebookApp {
    /**
     *  Reads configuration for appName.
     *  Reads all nodes from privateConfig with names matching /^facebook-$appType-(.*)/ and returns values as a hash.
     *
     *  @param      $appType   string		App type: photo|video|music
     *  @return     hash
     */
    public static function getConfig($appType) {
        $config = simplexml_load_file(NF_APP_BASE . '/xn_private/main-private-configuration.xml');
        $res = array();
        foreach($config->privateConfig->children() as $key=>$node) {
            if (preg_match('/^facebook-(\w+)-(.*)/u', $key, $m) && $m[1] == $appType) {
                $res[$m[2]] = "$node";
            }
        }
        return $res;
    }

    /**
     *  Runs the facebook app handler.
     *
     *  @param      $appType   string		App type: photo|video|music
     *  @return     void
     */
    public static function run($appType) {
        $selfUrl = "http://" . $_SERVER["SERVER_NAME"] . $_SERVER["PHP_SELF"];
        $config = XG_FacebookApp::getConfig($appType);

		// Use our mode first and then facebook method.
        $mode = $_REQUEST['xg_mode'] ? $_REQUEST['xg_mode'] : $_REQUEST['method'];
		switch($mode) {
			case 'attach_intf': // attachment interface
				// For unknown reasons msg attachments don't work if Facebook object is created.
				// input=hidden url is for the message attachments
    	    	// Attachments are more narrow than regular wide content, see http://wiki.developers.facebook.com/index.php/Message_attachments for details
    	        echo $config['attachmentFbml'];
				echo '<input type="hidden" name="url" value="' . htmlentities($selfUrl . '?xg_mode=attachment', ENT_QUOTES).'" />';
				break;
			case 'attachment': // attachment content
	            echo $config['attachmentFbml'];
    	        break;
			case 'publisher_getInterface': // publishing interface
				echo json_encode(array(
                    'method' => 'publisher_getInterface',
                    'content' => array( 'fbml' => $config['attachmentFbml'], 'publishEnabled' => true, 'commentEnabled' => true, ),
                ));
                break;
			case 'publisher_getFeedStory': // the content of a published story
				$comment = $_REQUEST['app_params']['comment_text'];
				$feed = array(
                    'template_id' => $config['feedStoryId'],
					'template_data' => array(
						'comment_block' => $comment
							? '<div style="margin-top: 6px;"><div style="margin-bottom:4px;font-weight:bold"><fb:name uid="'.$_REQUEST['fb_sig_user'].'" useyou="false" /> wrote</div>'.$comment.'</div>'
							: '',
						'embed' => $config['attachmentFbml'],
						'did' => $_REQUEST['xg_my'] ? 'posted' : 'shared',
						'recipient' => $_REQUEST['xg_my'] ? '' : ' with <fb:name uid="'.$_REQUEST['fb_sig_profile_user'].'" />',
					),
				);
                echo json_encode(array(
					'method' => 'publisher_getFeedStory',
					'content' => array( 'feed' => $feed, )
                ));
				break;
			default: // Canvas URL
				$facebook = new Facebook($config['apiKey'], $config['secret']);
        		$user = $facebook->require_login();
				echo '<center>', $config['appFbml'], '</center>';
				break;
		}
    }
}
?>
