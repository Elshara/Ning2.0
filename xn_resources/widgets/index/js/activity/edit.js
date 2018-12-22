dojo.provide('xg.index.activity.edit');

var form                = dojo.byId('activity_form');
var addMessageMode      = dojo.byId('add_message_input');
var addFactButton       = dojo.byId('add_message_fact_button');
var addMessageButton    = dojo.byId('add_custom_message_button');
var factSelector        = dojo.byId('fact_selector');
var customMessageField  = dojo.byId('customMessageBox');
var customMessageFlag   = dojo.byId('custom_message_flag');
var sendMessageField    = dojo.byId('activitymessage');

dojo.event.connect(addFactButton, 'onclick', function(){ 
    addMessageMode.value='true';
    if(factSelector.options[factSelector.selectedIndex].getAttribute('_html')){
        sendMessageField.value = factSelector.options[factSelector.selectedIndex].getAttribute('_html');
    } else {
        sendMessageField.value = factSelector.options[factSelector.selectedIndex].innerHTML;
    }
    form.submit(); 
    } );
    
dojo.event.connect(addMessageButton, 'onclick', function(){ 
    addMessageMode.value='true';
    customMessageFlag.value = 'true';
    sendMessageField.value = customMessageField.value;
    form.submit(); 
    } );
    
/**
 * An anchor tag that sends a friend request.
 */
dojo.widget.defineWidget('xg.index.activity.charCountDown', dojo.widget.HtmlWidget, {
    /** The username of the person */
    _limit: '<required>',
    _inputId: '<required>',

    fillInTemplate: function(args, frag) {
        this.innerHTML = this._limit;
        var textinput = document.getElementById(this._inputId);
        this.inputUpdated(textinput);
        dojo.event.connect(textinput, 'onkeyup', dojo.lang.hitch(this, function(evt) {
            this.inputUpdated(textinput);
        }));
    },

    inputUpdated: function(textinput) {
        var remaining = (this._limit - textinput.value.length)
        if(remaining<0) {
            textinput.value = textinput.value.substring(0, this._limit)
            remaining = 0
        }
        this.domNode.innerHTML = remaining
    }
});