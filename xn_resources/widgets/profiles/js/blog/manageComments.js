dojo.provide('xg.profiles.blog.manageComments');

xg.addOnRequire(function() {
    // Fix IE 6's broken <button/> handling
    dojo.lang.forEach(['approve','delete'], function(act) {
        if (dojo.byId('comment_action_' + act)) {
            dojo.event.connect(dojo.byId('comment_action_' + act), 'onclick', function(evt) {
                dojo.byId('comment_action').value = act;
            });
        }
    }, true);
    
		 // Make each "More..." link hide its containing span (which contains the space before
		 // the link) and display the <span/> with the second part of the comment
		 dojo.lang.forEach(dojo.html.getElementsByClass('comment-more-link'), function(el) {
				     var m = el.id.match(/^comment-more-link-(.+)$/);
				     if (m) {
				       var commentId = m[1];
				       dojo.event.connect(el, 'onclick', function(evt) {
							    dojo.event.browser.stopEvent(evt);
							    dojo.html.hide('comment-more-container-' + commentId);
							    dojo.html.show('comment-more-' + commentId);
							  });
				     }
				   }, true);
		 // Make each "[ x ]" link hide the <span/> with the second part of the comment
		 // and show the "More..." link's container span
		 dojo.lang.forEach(dojo.html.getElementsByClass('comment-less-link'), function(el) {
				     var m = el.id.match(/^comment-less-link-(.+)$/);
				     if (m) {
				       var commentId = m[1];
				       dojo.event.connect(el, 'onclick', function (evt) {
							    dojo.event.browser.stopEvent(evt);
							    dojo.html.hide('comment-more-' + commentId);
							    dojo.html.show('comment-more-container-' + commentId);
							  });
				     }
				   }, true);
	       }
	       );



