dojo.require('xg.shared.util');

dojo.provide('xg.video.video.ShowEmbedToggle');
dojo.widget.defineWidget('xg.video.video.ShowEmbedToggle', dojo.widget.HtmlWidget, {
    /** The embed code for display and copying */
    _embedCode: '',
    /** The url for the video detail page */
    _directURL: '',
    /** The link text that appears when the embed is togged closed  */
    _toOpenText: '',
    /** The link text that appears when the embed is togged open  */
    _toCloseText: '',
    /** The url to post this video on Myspace  */
    _myspacePostUrl: '',
    /** The url to post this video on Myspace  */
    _facebookPostUrl: '',
    /** The url of a custom css to be applied to the clearspring menu*/
    _othersCustomCSS:'',
    /** if clearspring should be disabled */
    _disableOthers: false,
    /** which widget class/template Clearspring should use to share this instance*/
    _widgetId:'',
    /** the flashvars string in json format*/
    _config:'',
    /** if the embed player is from Ning */
    _isNing: 0,
    fillInTemplate: function(args, frag) {
        var a = this.getFragNodeRef(frag);
        var embedBlock = dojo.html.createNodesFromText(dojo.string.trim(' \
        <small class="showembed showembed-wide" style="display:none;"> '+
        ( (this._isNing) ? ' \
            <ul class="services-hoz"> \
                <li><a href="'+ this._myspacePostUrl +'" class="desc service-myspace" target="_blank" id="myspacesharelink">' + xg.video.nls.text('addToMyspace') + '</a></li>\
                <li><a href="'+ this._facebookPostUrl +'" class="desc service-facebook" target="_blank" id="facebooksharelink">'+ xg.video.nls.text('shareOnFacebook') +'</a></li>'+
                ((!this._disableOthers) ? '<li><a href="#" class="desc service-other" id="servicesOther">'+ xg.video.nls.text('addToOthers') +'</a></li>' : '') +' \
            </ul> ' : '') +' \
            <div id="csLaunchpadTarget"></div> \
            <label for="directurl">' + xg.video.nls.text('directLink') + '</label><br/>\
            <input type="text" value="" class="textfield wide" id="directurl"/><br/>\
            <label for="embedcode">' + xg.video.nls.text('embedHTMLCode') + '</label> \
            <br/> \
            <input id="embedcode" class="textfield wide" type="text"/> \
        </small>'))[0];
        var br = dojo.html.createNodesFromText(dojo.string.trim('<br/>'))[0];
        if (a.nextSibling) {
            // attach embed after the next link in the list so we don't hide the "Hide embed code" link
            dojo.html.insertAfter(embedBlock,a.nextSibling);
        } else {
            // if there are no links we'll attach to the parentNode of a
            a.parentNode.appendChild(embedBlock);
        }
        var inputs = embedBlock.getElementsByTagName('input');
        if(this._isNing) {
            var myspacelink =  document.getElementById('myspacesharelink');
            var facebooklink = document.getElementById('facebooksharelink');
            var clearspringlink = document.getElementById('servicesOther');
        }
        var urlField = inputs[0];
        var embedField = inputs[1];
        urlField.value = this._directURL;
        embedField.value = this._embedCode;
        xg.shared.util.selectOnClick(urlField);
        xg.shared.util.selectOnClick(embedField);
        dojo.event.connect(a, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            dojo.style.toggleShowing(embedBlock);
            if (dojo.style.isShowing(embedBlock)) {
                a.innerHTML = this._toCloseText; 
            } else {
                a.innerHTML = this._toOpenText;
            }
            if (this._isNing && !this._disableOthers){
                csLaunchpad = $Launchpad.CreateMenu({
                  actionElement: 'servicesOther',
                  servicesInclude: [
                      "blogger",
                      "friendster",
                      "google",
                      "freewebs",
                      "live",
                      "livejournal",
                      "piczo",
                      "netvibes",
                      "tagged",
                      "typepad",
                      "vox",
                      "xanga",
                      "pageflakes",
                      "myyearbook",
                      "perfspot"
                  ], 
                  customCSS: this._othersCustomCSS, 
                  wid: this._widgetId,
                  config: dojo.json.evalJson(this._config),
                  targetElement: "csLaunchpadTarget"
                });
            }
            
        }));
        if(this._isNing) {
            if (this._myspacePostUrl.length < 1) myspacelink.style.display = 'none';
            dojo.event.connect(facebooklink, 'onclick', dojo.lang.hitch(this, function(event){
                dojo.event.browser.stopEvent(event);
                window.open(this._facebookPostUrl,'sharer','toolbar=0,status=0,width=626,height=436');
                return false;
            }));
        }
    }
});

