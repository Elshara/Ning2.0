dojo.provide('xg.shared.IframeUpload');
(function(){
var createIframe = function(/*String*/fname, /*String*/onloadstr) {
	//	summary:
	//		Creates a hidden iframe in the page.
	//	fname: String
	//		The name of the iframe. Used for the name attribute on the
	//		iframe.
	//	onloadstr: String
	//		A string of JavaScript that will be executed when the content
	//		in the iframe loads.
	if(window[fname]){ return window[fname]; }
	if(window.frames[fname]){ return window.frames[fname]; }
	var cframe = null;
	if (dojo.render.html.ie) {
		cframe = document.createElement('<iframe name="'+fname+'" src="about:blank" onload="'+onloadstr+'">');
	} else {
		cframe = document.createElement('iframe');
		cframe.name = fname;
		cframe.id = fname;
		cframe.src = 'about:blank';
		cframe.onload = new Function(onloadstr);
	}

	if(dojo.render.html.safari){
		//We can't change the src in Safari 2.0.3 if absolute position. Bizarro.
		cframe.style.position = "absolute";
	}
	cframe.style.left = cframe.style.top = cframe.style.height = cframe.style.width = "1px";
	cframe.style.visibility = "hidden";
	return document.body.appendChild(cframe);
};

var iframeDoc = function(/*DOMNode*/ifrNode) {
	//summary: Returns the document object associated with the iframe DOM Node argument.
	var doc = ifrNode.contentDocument || // W3
		(
			(
				(ifrNode.name) && (ifrNode.document) &&
				(document.getElementsByTagName("iframe")[ifrNode.name].contentWindow) &&
				(document.getElementsByTagName("iframe")[ifrNode.name].contentWindow.document)
			)
		) ||  // IE
		(
			(ifrNode.name)&&(document.frames[ifrNode.name])&&
			(document.frames[ifrNode.name].document)
		) || null;
	return doc;
}

var iframeNode = undefined,
	iframeNodeName = 'xg_shared_transport',
	lastFormTarget = undefined,
	lastFormAction = undefined,
	lastForm = undefined,
lastRequestCallback = undefined;

var cleanIframeUpload = function() {
	lastFormTarget ? lastForm.setAttribute('target', lastFormTarget) : lastForm.removeAttribute('target');
	lastForm.setAttribute('action', lastFormAction);
	lastRequestCallback = undefined;
}

xg.shared.IframeUpload = {
	_onLoadTransport: function() {
		if (iframeDoc(iframeNode).location != 'about:blank' && lastRequestCallback) {
			var content = undefined, success = lastRequestCallback;
			cleanIframeUpload();
			try { content = iframeDoc(iframeNode).body.innerHTML } catch(e) {content = null}
			if (content.match(/^<pre[^>]*>(.*)<\/pre>$/i)) {
				content = RegExp.$1; // Internet Explorer, and sometimes Firefox [Jon Aquino 2006-05-06]
				// Safari 3 is even worse, it adds <pre style="..very-long-style.."></pre> around the response [Andrey 2008-07-03]
			}
		content = content.replace(/&amp;/gm, '&')
			.replace(/&lt;/gm, '<')
				.replace(/&gt;/gm, '>')
				.replace(/&quot;/gm, '"')
				.replace(/&#39;/gm, "'");
			success(content);
		}
	},

		/**
		 *  Starts form upload.
		 *
		 *  @param      formNode	DOMNode						form tag
		 *  @param 		success		function(responseText)		Callback
		 *  @param		url			string						Optional URL to submit form. If empty, form.action is used.
		 *  @return     void
		 */
		start: function (formNode, success, url) {
			if (!iframeNode) {
				iframeNode = createIframe(iframeNodeName, 'xg.shared.IframeUpload._onLoadTransport()');
			}
			if (lastRequestCallback) {
				alert("Cannot send request. Previous request wasn't cleaned up properly");
			}
			lastRequestCallback = success;

			lastForm = formNode;
			lastFormTarget = formNode.getAttribute('target');
			lastFormAction = formNode.getAttribute('action');
			formNode.setAttribute('target', iframeNodeName);
			if (url) {
				formNode.setAttribute('action', url);
			}
			formNode.submit();
		},

		/**
		 *  Cancels the current form upload
		 *
		 *  @return void
		 */
		stop: function() {
			cleanIframeUpload();
			iframeNode.src = 'about:blank';
		}
	}
})();
