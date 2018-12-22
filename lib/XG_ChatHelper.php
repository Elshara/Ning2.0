<?php

/**
 * Base functions used by chat loader modules to obtain information, properties, etc
 */
class XG_ChatHelper {

	public static function getChatServer($app) {
		//for now just return this value 
		//there's an instance override of the chat server, 
		//useful to change a single network to a new chat server if necessary
		//this value would something like chat01.ningim.com:8080
		//todo hash appID to obtain only one of X available servers
                return "chat01.ningim.com:8080";
	}

}
