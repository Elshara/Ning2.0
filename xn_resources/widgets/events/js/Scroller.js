dojo.provide('xg.events.Scroller');
/**
 * Provides support for a viewport. Keeps track of the loaded data, and
 * lets you move the viewport back and forth, loading data when required.
 *
 * URL Input:
 * 	current:	seq-id
 * 	direction:	backward|forward
 * URL Output:
 * 	data:		[ [html],... ]
 * 	more:		seq-id | (empty)
 */
dojo.widget.defineWidget('xg.events.Scroller', dojo.widget.HtmlWidget, {
    // Required attrs
    _buttonContainer: '', // DOM-ID of the div containing "next" and "prev"
    _nextButton: '',	// DOM-ID of the "next" control
    _prevButton: '',	// DOM-ID of the "prev" control
    _nextSeqId: '',		// next seqId, or empty value
    _prevSeqId: '',		// prev seqId, or empty value
    _url: '',			// URL for fetching data
    // Optional attrs
    _scrollBy: 1,		// The number of items to shift per click
    _threshold: 0,		// If we are closer to the end of loaded list than this value, do the preload. 0 - disables pre-loading

    // Internal attrs
    container: null,	// content container (contains the list of _childTag's)
    position: 0,		// current pointer in container (_showItems are displayed)
    start: 0,			//
    end: 0,				//
    showItems: 0,		// How many items to show (pageSize)
    activeReq: [],

    fillInTemplate: function(args, frag) {
        this.container	= this.getFragNodeRef(frag);
        // remove all text nodes
        var ch = this.container.childNodes;
        for(var i = ch.length-1;i>=0;i--) {
            if (ch[i].nodeType == 3) { this.container.removeChild(ch[i]) }
        }
        this.position	= 0;
        this.start		= 0;
        this.end 		= ch.length;
        this.showItems 	= ch.length;
        dojo.event.connect(dojo.byId(this._prevButton), 'onclick', dojo.lang.hitch(this, this.onPrev));
        dojo.event.connect(dojo.byId(this._nextButton), 'onclick', dojo.lang.hitch(this, this.onNext));
        this.updateButtons();
    },
    updateItems: function(shift) {
        var c = this.container.childNodes, offset = -this.start;
        var i, j, len = c.length;
        for (i = 0;i<this.showItems;i++) {
            j = i + this.position + offset;
			if (j >= 0 && j<len) dojo.html.hide(c[j]);
        }
        this.position += shift;
        for (i = 0;i<this.showItems;i++) {
            j = i + this.position + offset;
			if (j >= 0 && j<len) { dojo.html.show(c[j]); c[j].style.display = ''; }
        }
        this.updateButtons();
    },
    scroll: function(forward, delta) {
        if (delta >= 0) {
            this.updateItems((forward?1:-1) * this._scrollBy);
            if (delta < this._threshold) { this.request(forward,0); }
        } else {
            this.request(forward,1);
        }
    },
    updateButtons: function() {
        var show = false;
        if (this.position > this.start || this._prevSeqId) {
            dojo.html.show(this._prevButton);
            show = true;
        } else {
            dojo.html.hide(this._prevButton);
        }
        if (this.position+this.showItems < this.end || this._nextSeqId) {
            dojo.html.show(this._nextButton);
            show = true;
        } else {
            dojo.html.hide(this._nextButton);
        }
        if (show) {
            dojo.html.show(this._buttonContainer);
        } else {
            dojo.html.hide(this._buttonContainer);
        }
        //this.dumpState();
    },
    request: function(forward, updatePos) {
        if (this.activeReq[forward]) {
            return;//this.activeReq.abort(); !! - if request was made too much time ago, cancel it...
        }
        var content = forward	? {xn_out:'json',direction:'forward',current:this._nextSeqId}
                                : {xn_out:'json',direction:'backward',current:this._prevSeqId};
        if (!content.current) { return; }
        this.activeReq[forward] = dojo.io.bind({
            url: this._url,
            method: 'post',
            mimetype: 'text/javascript',
            preventCache: true,
            encoding: 'utf-8',
            content: content,
            load: dojo.lang.hitch(this,  function(type, js, event) {
                var c = this.container;
                if (forward) {
                    this._nextSeqId = js.more;
                    this.end 		+= js.data.length;
                    for(var i = 0;i<js.data.length;i++) { c.appendChild(this.nodeFromText(js.data[i])) }
                } else {
                    this._prevSeqId = js.more;
                    this.start 		-= js.data.length;
                    for(var i = js.data.length-1;i>=0;i--) { c.insertBefore(this.nodeFromText(js.data[i]), c.firstChild) }
                }
                this.activeReq[forward] = null;
                if (updatePos) {
                    forward ? this.onNext() : this.onPrev();
                }
                //this.dumpState();
            })
        });
    },
    nodeFromText: function(text) {
        var div	= document.createElement('DIV'); 	div.innerHTML = text;
        var node = div.firstChild; 					dojo.html.hide(node);
        return node;
    },
    onPrev: function(event) {
        if (event) dojo.event.browser.stopEvent(event);
        this.scroll(0, this.position - this._scrollBy - this.start);
    },
    onNext: function(event) {
        if (event) dojo.event.browser.stopEvent(event);
        this.scroll(1, this.end - (this.position + this._scrollBy + this.showItems));
    }/*,
    dumpState: function() {
        var html = '';
        html += "["+(this.start)+"-"+(this.end-1)+"], prev="+this._prevSeqId+",next="+this._nextSeqId+",pos="+this.position+"<br>";
        var c = this.container.childNodes, offset = -this.start;
        for(var i = this.start;i<this.end;i++) {
            if (i >= this.position && (i<this.position+this.showItems))
                html += "<font color=red>";
            html += c[i+offset].innerHTML+"<br>";
            if (i >= this.position && (i<this.position+this.showItems))
                html += "</font>";
        }
        dojo.byId('debug').innerHTML = html;
    }*/
});
