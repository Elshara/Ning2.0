dojo.provide('xg.index.membership.CheckForMembers');
dojo.require('xg.index.membership.list');

/**
 * @todo refactor into shared widget with isValid() method to determine if it
 *       needs to display the dialog or continue.
 */
dojo.widget.defineWidget('CheckForMembers', dojo.widget.HtmlWidget, {
    /**
     * Error message to display to the user
     */
    _errorMsg: '',

    /**
     * The type of submission this should have or a callback
     */
    _type: '',

    /**
     * Initialize check for selected users.
     */
    fillInTemplate: function(args, frag) {
        var a = this.getFragNodeRef(frag);
        dojo.event.connect(a, 'onclick', dojo.lang.hitch(this, function(event) {
            if (xg.index.membership.list.selectedUsernames().length == 0) {
                xg.shared.util.alert(this._errorMsg);
            } else {
                this['action_' + this._type]();
            }
        }));
    },

    _submitWithOp: function(op) {
        xg.index.membership.list.submitWithOp(op);
    },

    action_promote: function(){
        this._submitWithOp('promote');
    },
    action_demote: function() {
        this._submitWithOp('demote');
    },
    action_ban: function() {
        dojo.byId('xg_member_form').submit();
    },
    action_resendInvite:function() {
        this._submitWithOp('resend');
    },
    action_cancelInvite: function() {
        this._submitWithOp('cancel');
    },
    action_acceptMember: function() {
        this._submitWithOp('accept');
    },
    action_declineMember: function() {
        this._submitWithOp('decline');
    }
});

