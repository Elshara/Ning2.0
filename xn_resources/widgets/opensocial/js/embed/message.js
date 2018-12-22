dojo.provide('xg.opensocial.embed.message');

/**
 * Contains the actual action of sending the message
 */
xg.opensocial.embed.message = {
    /**
     * @param recipients string            comma-separated list of screen-names
     * @param message OpenSocial.Message   mail message
     * @param msgType string               type of request [requestSendMessage, requestShareApp]
     * @param callbackName function        function to call after the function completes execution
     */
    sendMessage: function (args, callbackName) {
        var message = args['message'];
        var ownerId = args['ownerId'];
        var viewerId = args['viewerId'];

        var url = ning.CurrentApp.url+'/opensocial/message/sendQuick?xg_token='+xg.token;
        url += '&appUrl='+args['appUrl'];
        if (viewerId   && ('undefined' != typeof(viewerId)))   url += '&viewerId='+viewerId;
        if (ownerId    && ('undefined' != typeof(ownerId)))    url += '&ownerId='+ownerId;
        url += '&msgType='+args['msgType'];

        var content = { friendSet: 'ALL_FRIENDS', numFriends: args['numUsers'], ids: args['ids'], subject: message.fields_['title'], message: message.fields_['body'] };

        xg.post(url, content, function(r,d) { callbackName( { status: true, code: 'ok', msg: '' } ); } );
    }
};

