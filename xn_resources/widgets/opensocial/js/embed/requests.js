dojo.provide('xg.opensocial.embed.requests');

dojo.require('xg.opensocial.embed.message');
dojo.require('xg.shared.util');

/**
 * Contains the implementation of opensocial.requestSendMessage. In particular, xg.opensocial.embed.requests.sendMessage
 * is registered as the function for opensocial.requestSendMessage.
 */

xg.opensocial.embed.requests = {
    appUrl: null,
    appTitle: null,
    viewerId: null,
    ownerId: null,
    recipients: null,
    computedRecipients: null,
    numUsers: null,
    message: null,
    callbackFunc: null,
    appdata: null,
    gadgetprefs: null,
    actualArgs: null,
    promptBeforeSend: false,
    alertAfterSend: false,
    /**
     * Validates the message to be sent
     * @return An object with three fields - 'status':boolean, 'code':string, 'message':string
     */
    validateMessage: function () {
        // EMAIL is supported (PRIVATE_MESSAGE, PUBLIC_MESSAGE, NOTIFICATION: not supported)
        var message = xg.opensocial.embed.requests.message;
        var recipients = xg.opensocial.embed.requests.recipients;
        var viewerId = xg.opensocial.embed.requests.viewerId;
        if (typeof message == 'object') {
            if (message.fields_['type'] && (message.fields_['type'] != 'email'))
                return { status: false, code: 'notImplemented', msg: xg.opensocial.nls.html('onlyEmailMsgSupported') };
            if (!message.fields_['body'] || (message.fields_['body'].length == 0)
                || !message.fields_['title'] || (message.fields_['title'].length == 0)
                || !message.fields_['type'])
                return { status: false, code: 'badRequest', msg: xg.opensocial.nls.html('msgExpectedToContain') };
        } else {
            return { status: false, code: 'badRequest', msg: xg.opensocial.nls.html('msgObjectExpected') };
        }
        if (('string' != typeof recipients) && !dojo.lang.isArray(recipients)) {
            return { status: false, code: 'notImplemented', msg: xg.opensocial.nls.html('recipientsShdBeStringOrArray') };
        } else {
            if (recipients == '') {
                return { status: false, code: 'badRequest', msg: xg.opensocial.nls.html('recipientsShdBeSpecified') };
            }
        }
        if (!viewerId || 'undefined' == typeof(viewerId)) 
            return { status: false, code: 'unauthorized', msg: xg.opensocial.nls.html('unauthorizedSender') };
            
        return { status: true, code: 'ok', msg: '' };
    },
    /**
     * opensocial.requestSendMessage is resgistered as sendMessage.
     * @param args array  arguments passed in by opensocial/js
     */
    sendMessageInit: function (args) {
        var appUrl       = args['appUrl'];
        var callbackName = args['callbackName'];
        var message      = args['message'];
        var recipients   = args['recipients'];
        var viewerId     = ning.CurrentProfile ? ning.CurrentProfile.id : undefined;
        var ownerId      = args['ownerId'];
    	var that = this;
    	
    	// save information in the object
        xg.opensocial.embed.requests.appUrl       = appUrl;
        xg.opensocial.embed.requests.viewerId     = viewerId;
        xg.opensocial.embed.requests.ownerId      = ownerId;
        xg.opensocial.embed.requests.recipients   = dojo.lang.isArray(recipients) ? recipients.join(',') : recipients;
        xg.opensocial.embed.requests.message      = message;
    	xg.opensocial.embed.requests.callbackFunc = function(retstat) { gadgets.rpc.call(that.f, callbackName, null, retstat ); };
        xg.opensocial.embed.requests.actualArgs   = args;
    	
        var msgValidRet = xg.opensocial.embed.requests.validateMessage();
        if (msgValidRet.code != 'ok') {
            xg.opensocial.embed.requests.sendAborted(msgValidRet);
            return;
        }

        var url = ning.CurrentApp.url+'/opensocial/message/getAppInfo?xg_token='+xg.token;
        url += '&appUrl='+appUrl;
        if (viewerId   && ('undefined' != typeof(viewerId)))   url += '&viewerId='+viewerId;
        if (ownerId    && ('undefined' != typeof(ownerId)))    url += '&ownerId='+ownerId;
        if (recipients && ('undefined' != typeof(recipients))) url += '&ids='+recipients;
        url += '&xn_out=json';
        xg.get(url, '', function(r, data) { xg.opensocial.embed.requests.handleAppInfoResponse(data); } );

    },
    /**
     * handle the appInfoResponse from server: get appTitle, can the user send the message, find the screen names of people to send message to, etc
     * @param data string   data returned from the AJAX call
     */
    handleAppInfoResponse: function(data) {
        var canSendMessages = data.appdata['canSendMessages'];
        if ('undefined' == typeof(canSendMessages)) canSendMessages = 1;
        
        var computedRecipients = [];
        for (key in data.people) {
            computedRecipients.push(data.people[key]['screenName']);
        }

        // save information in the object
        xg.opensocial.embed.requests.computedRecipients = computedRecipients;
        xg.opensocial.embed.requests.numUsers           = data.numUsers;
        xg.opensocial.embed.requests.appdata            = data.appdata;
        xg.opensocial.embed.requests.gadgetprefs        = data.gadgetprefs;
        xg.opensocial.embed.requests.appTitle           = data.gadgetprefs['title'];
        if ('undefined' == typeof(xg.opensocial.embed.requests.appTitle)) xg.opensocial.embed.requests.appTitle = xg.opensocial.nls.html('untitled');
        
        var appUrl     = xg.opensocial.embed.requests.appUrl;
        var appTitle   = xg.opensocial.embed.requests.appTitle;
        var ownerId    = xg.opensocial.embed.requests.ownerId;
        var viewerId   = xg.opensocial.embed.requests.viewerId;
        var recipients = xg.opensocial.embed.requests.recipients;

        var urlParams  = '?xg_token='+xg.token;
        urlParams += '&appUrl='+appUrl;
        if (viewerId   && ('undefined' != typeof(viewerId)))   urlParams += '&viewerId='+viewerId;
        if (ownerId    && ('undefined' != typeof(ownerId)))    urlParams += '&ownerId='+ownerId;
        urlParams += '&xn_out=json';

        var urlForUpdate = ning.CurrentApp.url+'/opensocial/message/rateLimitCheckAndUpdate'+urlParams;
        var urlForCheck  = ning.CurrentApp.url+'/opensocial/message/rateLimitCheck'+urlParams;

        if (computedRecipients.length == 0 && recipients != "OWNER" && recipients != "VIEWER" && recipients != "OWNER_FRIENDS" && recipients != "VIEWER_FRIENDS") {
            // No need to send the message - just 'log' ratelimit, and return
            xg.get(urlForUpdate, '', function(r, data) {
                                xg.opensocial.embed.requests.sendAborted({ status: false, code: 'unauthorized', msg: xg.opensocial.nls.html('unauthorizedRecipients') });
                            });
        } else if (canSendMessages == 0) {
            // No need to send the message - just 'log' ratelimit, and return
            xg.get(urlForUpdate, '', function(r, data) {
                                xg.opensocial.embed.requests.sendAborted({ status: false, code: 'forbidden', msg: xg.opensocial.nls.html('settingIsDontSendMessage') });
                            });
        } else {
	        xg.get(urlForCheck, '', function(r, data) {
                                xg.opensocial.embed.requests.handleRateLimitCheck(data);
                            });
		}
    },
    /**
     * handle the rate limit check - has the user exceeded the limit allowed for the day - if so, don't send the message
     * @param data string   data returned from the AJAX call
     */
	handleRateLimitCheck: function(data) {
        var rateLimitExceeded = data.rateLimitExceeded;
        var viewerId          = xg.opensocial.embed.requests.viewerId;
        
        var promptBeforeSending = xg.opensocial.embed.requests.appdata['promptBeforeSending'];
        if ('undefined' == typeof(promptBeforeSending)) promptBeforeSending = true;

		if (rateLimitExceeded) {
            xg.opensocial.embed.requests.sendAborted({ status: false, code: 'limitExceeded', msg: xg.opensocial.nls.html('rateLimitExceeded') });
        } else {
            var recipients         = xg.opensocial.embed.requests.recipients;
            var computedRecipients = xg.opensocial.embed.requests.computedRecipients;
            if (computedRecipients.length == 0) { // if VIEWER_FRIENDS or OWNER_FRIENDS has no entries!
                xg.opensocial.embed.requests.sendCompleted( { status: true, code: 'ok', msg: '' } );
            } else if (!promptBeforeSending) {
	            var passArgs = { appUrl: xg.opensocial.embed.requests.appUrl,
	                             viewerId: xg.opensocial.embed.requests.viewerId,
	                             ownerId: xg.opensocial.embed.requests.ownerId,
                                 ids: xg.opensocial.embed.requests.recipients,
	                             recipients: xg.opensocial.embed.requests.computedRecipients,
	                             numUsers: xg.opensocial.embed.requests.numUsers,
	                             msgType: 'requestSendMessage',
	                             message: xg.opensocial.embed.requests.message };
	            
	            xg.opensocial.embed.message.sendMessage(passArgs, xg.opensocial.embed.requests.sendCompleted);
            } else {
	            // confirm dialog with user if it is ok to send msg
	            xg.opensocial.embed.requests.confirmSendDialog();
            }
        }
	},
    /**
     * Dialog prompt for user prior to sending a message
     */
	confirmSendDialog: function() {
        var appUrl     = xg.opensocial.embed.requests.appUrl;
        var appTitle   = xg.opensocial.embed.requests.appTitle;
        var ownerId    = xg.opensocial.embed.requests.ownerId;
        var viewerId   = xg.opensocial.embed.requests.viewerId;
        var recipients = xg.opensocial.embed.requests.recipients;
        var message    = xg.opensocial.embed.requests.message;
        
        var showFriends = (recipients != 'VIEWER' && recipients != 'OWNER');

        var url = ning.CurrentApp.url+'/opensocial/message/sendMessageForm?xg_token='+xg.token;
        url    += '&random='+ new Date().getTime();
        url    += '&appUrl='+appUrl;
        url    += '&appTitle='+encodeURIComponent(appTitle);
        if (viewerId   && ('undefined' != typeof(viewerId)))   url += '&viewerId='+viewerId;
        if (ownerId    && ('undefined' != typeof(ownerId)))    url += '&ownerId='+ownerId;
        if (recipients && ('undefined' != typeof(recipients))) url += '&ids='+recipients;
        url    += '&msgType=requestSendMessage';
        url    += '&message='+encodeURIComponent(message.fields_['body']);
        url    += '&subject='+encodeURIComponent(message.fields_['title']);
        if (showFriends) url    += '&showFriends=1';

	    xg.index.quickadd.loadModule('sendMessageForm', url, 'xg.opensocial.embed.sendMessageForm', true);
	},
    /**
     * handle the case when the send message is aborted due to whatever reason
     * @param status object   status object for the failure
     */
    sendAborted: function(status) {
        if (xg.opensocial.embed.requests.alertAfterSend) {
            xg.shared.util.alert({title: xg.opensocial.nls.html('messageNotSent'),
                                  bodyHtml: xg.opensocial.nls.html('messageWasNotSent', status.msg)
                                 });
        } else {
            xg.opensocial.embed.requests.callbackFunc(status);
        }
    },
    /**
     * handle the case when the send message call completes
     * @param status object   status object for the completion
     */
    sendCompleted: function(status) {
        var message    = xg.opensocial.embed.requests.message;
        var recipients = xg.opensocial.embed.requests.recipients;

        if (xg.opensocial.embed.requests.alertAfterSend) {
	        xg.shared.util.alert({title: xg.opensocial.nls.html('messageSent'),
	                              bodyHtml: xg.opensocial.nls.html('followingMessageWasSent',recipients, message.fields_['title'], message.fields_['body'])
	                             });
        } else {
            xg.opensocial.embed.requests.callbackFunc(status);
        }
    },
    /**
     * register the function for opensocial.requestSendMessage
     */
    setup: function () {
        gadgets.rpc.register("requestSendMessage", xg.opensocial.embed.requests.sendMessageInit);
    }
};

xg.addOnRequire(function(){
xg.opensocial.embed.requests.setup();
});
