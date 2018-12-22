dojo.provide('xg.chat.ChatEmbed');
dojo.require('dojo.lfx.html');

dojo.widget.defineWidget('xg.chat.ChatEmbed', dojo.widget.HtmlWidget, {

    /** The URL to post to */
    _url: '<required>',
    
    /** Whether chat is enabled */
    _chatEnabled: 0,
    
    fillInTemplate: function(args, frag) {
        this.d = dojo.byId('xj_chat_disable');
        this.e = dojo.byId('xj_chat_enable');
        this.node = dojo.byId('xj_chat');
        xg.listen(this.d,'onclick',this,function(evt){
           xg.stop(evt);
           if (this.posting) return;
           this.setEnabled(0);
        });
        xg.listen(this.e,'onclick',this,function(evt) {
           xg.stop(evt);
           if (this.posting) return;
           this.setEnabled(1);
        });
        if (this._chatEnabled != 0) {
           dojo.html.show(this.d);
        } else {
           dojo.html.show(this.e);
        }      
    },
    /**
     * Executes the POST operation
     */
    setEnabled: function(value) {
        var self = this;
        self.posting = true;
        xg.post(this._url, {enabled: value}, function(e,data) {
            self.posting = false;
            dojo.html.show( value ? self.d : self.e );
            dojo.html.hide( value ? self.e : self.d );
            if (value) {
                var scripts = [];
                var html = data['data'].replace(/<script>((.|[\r\n])*?)<\/script>/gi, function($0,$1) { scripts.push($1); return '' });
                self.node.innerHTML = html;
                setTimeout(function(){ for(var i = 0;i<scripts.length;i++) eval(scripts[i]) },10);
            } else {
                self.node.innerHTML = '';
            }
        });
    }
});
