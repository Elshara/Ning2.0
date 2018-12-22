dojo.provide('xg.profiles.embed.blocking');

dojo.require('xg.shared.util');
dojo.require("dojo.lfx.*");

dojo.widget.defineWidget('xg.profiles.embed.blocking.BlockingLink', dojo.widget.HtmlWidget, {

    /* The URL to post to */
    _url: '<required>',
    /* Title of the confirm pop-up */
    _confirmTitle: '',
    /* Body (message) of the confirm pop-up */
    _confirmMessage: '',
    /* Is this a profile page?  Used to set styles  */
    _isProfilePage: false,

    fillInTemplate: function(args, frag) {
        this.block   = dojo.byId('xj_block_messages');
        this.unblock = dojo.byId('xj_unblock_messages');
        var self     = this;
        xg.listen(this.block,'onclick',this,function(evt) {
           xg.stop(evt);
           if (this.posting) return;
           this.confirmDialog(this.block,1);
        });
        xg.listen(this.unblock,'onclick',this,function(evt) {
           xg.stop(evt);
           if (this.posting) return;
           this.confirmDialog(this.unblock,0);
        });
    },
    setBlocked: function(node,value) {
        var self = this;
        self.posting = true;
        xg.post(node.getAttribute('_url'), {blocked: value}, function(e,data) {
            self.posting = false;
            if (data == 1) {
	            dojo.html.show( value ? self.unblock : self.block );
	            dojo.html.hide( value ? self.block : self.unblock );
            }
            self.unblock.className = "desc msgunblock";
            self.block.className = "desc msgblock";
        });
    },
    confirmDialog: function(node,value) {
        xg.shared.util.confirm({
            title: node.getAttribute('_confirmTitle'),
            bodyHtml: '<p>'+node.getAttribute('_confirmMessage')+'</p>',
            onOk: dojo.lang.hitch(this, function(event) {
                if (node.getAttribute('_isProfilePage')) {
                    node.className = "desc working disabled";
                } else {
                    node.className = "working smalldelete";
                }
                if (this.posting) { return; }
                this.setBlocked(node,value);
            }),
            okButtonText: node.getAttribute('_confirmOkButtonText')
        });
    }
});
