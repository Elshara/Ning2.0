/**
 * Attaches onclick functions to the add/remove friend links.
 * A confirmation dialog is displayed - if ok is clicked,
 * send a post request to submit the friend/unfriend action.
 */
(function() {
	var addLink = document.getElementById('add_friend_link');
	var removeLink = document.getElementById('remove_friend_link');
	
	if (addLink) {
		addLink.onclick = function() {
			if (confirm(addLink.getAttribute('_msg'))) {
				document.add_friend_form.submit();
			}
		};
	}
	if (removeLink) {
		removeLink.onclick = function() {
			if (confirm(removeLink.getAttribute('_msg'))) {
				document.remove_friend_form.submit();
			}
		};
	}
})();