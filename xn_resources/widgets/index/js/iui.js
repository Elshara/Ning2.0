/*
 	 Copyright (c) 2007, iUI Project Members
	 See LICENSE.txt for licensing terms
 */

//Contains a list of functions to be called when the orientation changes
var orientation_listeners = new Object();

/**
 * Updates properties on orientation change and calls listeners
 */
function updateOrientation(){
	var contentType = "show_";
	switch (window.orientation){
		case 0:
		contentType += "normal";
		break;

		case -90:
		contentType += "right";
		break;

		case 90:
		contentType += "left";
		break;

		case 180:
		contentType += "flipped";
		break;
	}
	for (var listener in orientation_listeners) {
		orientation_listeners[listener]();
	}
	var w = document.getElementById("w");
	if (w) {
		w.setAttribute("class", contentType);
	}
	window.scrollTo(0, 1); // pan to the bottom, hides the location bar
}

window.addEventListener("load", updateOrientation, false);

function $(id) { return document.getElementById(id); }

(function() {

var slideSpeed = 20;
var slideInterval = 0;

var currentPage = null;
var currentDialog = null;
var currentWidth = 0;
var currentHash = location.hash;
var hashPrefix = "#_";
var pageHistory = [];
var newPageCount = 0;
var checkTimer;

// *************************************************************************************************

window.iui =
{
    showPage: function(page, backwards)
    {
        if (page)
        {
            if (currentDialog)
            {
                currentDialog.removeAttribute("selected");
                currentDialog = null;
            }

            if (hasClass(page, "dialog"))
                showDialog(page);
            else
            {
                var fromPage = currentPage;
                currentPage = page;

                if (fromPage)
                    setTimeout(slidePages, 0, fromPage, page, backwards);
                else
                    updatePage(page, fromPage);
            }
        }
    },

    showPageById: function(pageId)
    {
        var page = $(pageId);
        if (page)
        {
            var index = pageHistory.indexOf(pageId);
            var backwards = index != -1;
            if (backwards)
                pageHistory.splice(index, pageHistory.length);

            iui.showPage(page, backwards);
        }
    },

    showPageByHref: function(href, args, method, replace, cb)
    {
        var req = new XMLHttpRequest();
        req.onerror = function()
        {
            if (cb)
                cb(false);
        };

        req.onreadystatechange = function()
        {
            if (req.readyState == 4)
            {
                if (replace)
                    replaceElementWithSource(replace, req.responseText);
                else
                {
                    var frag = document.createElement("div");
                    frag.innerHTML = req.responseText;
                    iui.insertPages(frag.childNodes);
                }
                if (cb)
                    setTimeout(cb, 1000, true);
            }
        };

        if (args)
        {
            req.open(method || "GET", href, true);
            req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            req.setRequestHeader("Content-Length", args.length);
            req.send(args.join("&"));
        }
        else
        {
            req.open(method || "GET", href, true);
            req.send(null);
        }
    },

    insertPages: function(nodes)
    {
        var targetPage;
        for (var i = 0; i < nodes.length; ++i)
        {
            var child = nodes[i];
            if (child.nodeType == 1)
            {
                if (!child.id)
                    child.id = "__" + (++newPageCount) + "__";

                var clone = $(child.id);
                if (clone)
                    clone.parentNode.replaceChild(child, clone);
                else
                    document.body.appendChild(child);

                if (child.getAttribute("selected") == "true" || !targetPage)
                    targetPage = child;

                --i;
            }
        }

        if (targetPage)
            iui.showPage(targetPage);
    },

    getSelectedPage: function()
    {
        for (var child = document.body.firstChild; child; child = child.nextSibling)
        {
            if (child.nodeType == 1 && child.getAttribute("selected") == "true")
                return child;
        }
    }
};

// *************************************************************************************************

addEventListener("load", function(event)
{
    var page = iui.getSelectedPage();
    if (page)
        iui.showPage(page);

    setTimeout(preloadImages, 0);
    setTimeout(checkOrientAndLocation, 0);
    checkTimer = setInterval(checkOrientAndLocation, 300);
    updateOrientation();
    var notification = document.getElementById('notification');
    if (notification) {
    	setTimeout("document.getElementById('notification').className = 'fadeOut'",2000);
    }
}, false);

addEventListener("click", function(event)
{
    var link = findParent(event.target, "a");
    if (link)
    {
        function unselect() { link.removeAttribute("selected"); }

        if (link.href && link.hash && link.hash != "#")
        {
            link.setAttribute("selected", "true");
            iui.showPage($(link.hash.substr(1)));
            setTimeout(unselect, 500);
        }
        else if (link == $("backButton"))
            history.back();
        else if (link.getAttribute("type") == "submit")
            submitForm(findParent(link, "form"));
        else if (link.getAttribute("type") == "cancel")
            cancelDialog(findParent(link, "form"));
        else if (link.target == "_replace")
        {
            link.setAttribute("selected", "progress");
            iui.showPageByHref(link.href, null, null, link.parentNode, unselect);
        }
        // else if (!link.target)
        // {
        //     link.setAttribute("selected", "progress");
        //     iui.showPageByHref(link.href, null, null, null, unselect);
        // }
        else return;

        event.preventDefault();
    }
}, true);

var prevent = function(e) { e.preventDefault() };

/*
 *	Click listener for compose pages. Add button submits a form with the name of "compose".
 *	Cancel button has same effect as back button.
 *
 *	Inside the "compose" form all inputs with the _default and _required attributes have a special behavior:
 *  	_default		Sets the default value for an input
 *  	_required		Input is required and error will be displayed if input is empty. (#compose_error node is required)
 */
addEventListener("click", function(event)
{
	var div = findParent(event.target, "a");
	if (!div) return;
	if (div.getAttribute('class') == 'title-button' && div.getAttribute('id') == 'add')
	{
		var els = $('compose').elements, err = '';
		for (var i = 0; i<els.length; i++) {
			var r = els[i].getAttribute('_required'), d = els[i].getAttribute('_default'), v = els[i].value;
			if (r && (v == d || v.replace(/\s+/g,'') == '')) {
				err += '<li>'+r+'</li>';
			}
		}
		if (err == '' || !$('compose_error')) {
			for (var i = 0; i<els.length; i++) {
				var d = els[i].getAttribute('_default');
				if (d && els[i].value == d) els[i].value = '';
			}
			$('compose').submit();
		} else {
			var ul = $('compose_error').getElementsByTagName('ul')[0];
			ul.innerHTML = err;
			$('compose_error').style.display = '';
		}
	}
	if (div.getAttribute('class') == 'title-button' && div.getAttribute('id') == 'cancel')
	{
		window.history.go(-1);
	}
	if (div.getAttribute('class') == 'title-button' && div.getAttribute('id') == 'quick_add')
	{
		document.getElementById('quick_add_box').style.setProperty('display', 'block', null);
		window.scrollTo(0,1);
		document.addEventListener("touchmove", prevent, false);
	}
	if (div.getAttribute('class') == 'overlay-close')
	{
		div.parentNode.style.setProperty('display', 'none', null);
		document.removeEventListener("touchmove", prevent, false);
	}
	if (div.getAttribute('id') == 'photo_upload_link')
	{
		document.getElementById('quick_add_box').style.setProperty('display', 'none', null);
		document.getElementById('photo_upload_box').style.setProperty('display', 'block', null);
	}
}, true);

initComposeForm = function() {
	var ta = $('compose').getElementsByTagName('textarea');
	for (var i = 0; i<ta.length; i++) {
		if (ta[i].getAttribute('_default')) {
			if (ta[i].value == '') {
				ta[i].value = ta[i].getAttribute('_default');
			}
			ta[i].onfocus = function() { if (this.value==this.getAttribute('_default')) this.value='' };
			ta[i].onblur = function() { if (this.value=='') this.value=this.getAttribute('_default') };
		}
	}
}


/*
 * Click listener for list items.
 * Clicks on content lists (eg. forum posts or network members) will forward to the url
 * specified by the _url attribute
 * Clicks on checkbox li's will toggle both the li style and a hidden checkbox for form input
 * with the id specified in the _checkbox attribute
 */
addEventListener("click", function(event)
{
	var link = findParent(event.target, "li");
    if (link && link.getAttribute('_url')) {
        window.location = link.getAttribute('_url');
    }
    else if (link && link.getAttribute('_checkbox')) {
    	var checkbox = $(link.getAttribute('_checkbox'));
    	checkbox.checked = !checkbox.checked;
    	var checkimg = $(link.getAttribute('_checkimg'));
    	checkimg.setAttribute('class', (hasClass(checkimg,'check') ? 'mark' : 'mark check'));
    }
}, true);

function checkOrientAndLocation()
{
    if (window.innerWidth != currentWidth)
    {
        currentWidth = window.innerWidth;
        var orient = currentWidth == 320 ? "profile" : "landscape";
        document.body.setAttribute("orient", orient);
        setTimeout(scrollTo, 100, 0, 1);
    }

    if (location.hash != currentHash)
    {
        var pageId = location.hash.substr(hashPrefix.length)
        iui.showPageById(pageId);
    }
}

function showDialog(page)
{
    currentDialog = page;
    page.setAttribute("selected", "true");

    if (hasClass(page, "dialog") && !page.target)
        showForm(page);
}

function showForm(form)
{
    form.onsubmit = function(event)
    {
        event.preventDefault();
        submitForm(form);
    };

    form.onclick = function(event)
    {
        if (event.target == form && hasClass(form, "dialog"))
            cancelDialog(form);
    };
}

function cancelDialog(form)
{
    form.removeAttribute("selected");
}

function updatePage(page, fromPage)
{
    if (!page.id)
        page.id = "__" + (++newPageCount) + "__";

    location.href = currentHash = hashPrefix + page.id;
    pageHistory.push(page.id);

    var pageTitle = $("pageTitle");
    if (page.title)
        pageTitle.innerHTML = page.title;

    if (page.localName.toLowerCase() == "form" && !page.target)
        showForm(page);

    var backButton = $("backButton");
    if (backButton)
    {
        var prevPage = $(pageHistory[pageHistory.length-2]);
        if (prevPage && !page.getAttribute("hideBackButton"))
        {
            backButton.style.display = "inline";
            backButton.innerHTML = prevPage.title ? prevPage.title : "Back";
        }
        else
            backButton.style.display = "none";
    }
}

function slidePages(fromPage, toPage, backwards)
{
    var axis = (backwards ? fromPage : toPage).getAttribute("axis");
    if (axis == "y")
        (backwards ? fromPage : toPage).style.top = "100%";
    else
        toPage.style.left = "100%";

    toPage.setAttribute("selected", "true");
    scrollTo(0, 1);
    clearInterval(checkTimer);

    var percent = 100;
    slide();
    var timer = setInterval(slide, slideInterval);

    function slide()
    {
        percent -= slideSpeed;
        if (percent <= 0)
        {
            percent = 0;
            if (!hasClass(toPage, "dialog"))
                fromPage.removeAttribute("selected");
            clearInterval(timer);
            checkTimer = setInterval(checkOrientAndLocation, 300);
            setTimeout(updatePage, 0, toPage, fromPage);
        }

        if (axis == "y")
        {
            backwards
                ? fromPage.style.top = (100-percent) + "%"
                : toPage.style.top = percent + "%";
        }
        else
        {
            fromPage.style.left = (backwards ? (100-percent) : (percent-100)) + "%";
            toPage.style.left = (backwards ? -percent : percent) + "%";
        }
    }
}

function preloadImages()
{
    var preloader = document.createElement("div");
    preloader.id = "preloader";
    document.body.appendChild(preloader);
}

function submitForm(form)
{
    iui.showPageByHref(form.action || "POST", encodeForm(form), form.method);
}

function encodeForm(form)
{
    function encode(inputs)
    {
        for (var i = 0; i < inputs.length; ++i)
        {
            if (inputs[i].name)
                args.push(inputs[i].name + "=" + escape(inputs[i].value));
        }
    }

    var args = [];
    encode(form.getElementsByTagName("input"));
    encode(form.getElementsByTagName("select"));
    return args;
}

function findParent(node, localName)
{
    while (node && (node.nodeType != 1 || node.localName.toLowerCase() != localName))
        node = node.parentNode;
    return node;
}

function hasClass(self, name)
{
    var re = new RegExp("(^|\\s)"+name+"($|\\s)");
    return re.exec(self.getAttribute("class")) != null;
}

function replaceElementWithSource(replace, source)
{
    var page = replace.parentNode;
    var parent = replace;
    while (page.parentNode.nodeName != 'DIV')
    {
        page = page.parentNode;
        parent = parent.parentNode;
    }
    var frag = document.createElement(parent.localName);
    frag.innerHTML = source;

    page.removeChild(parent);

    while (frag.firstChild)
        page.appendChild(frag.firstChild);
}

})();
