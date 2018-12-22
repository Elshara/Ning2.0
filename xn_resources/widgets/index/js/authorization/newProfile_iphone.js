(function(){
	var toggle = document.getElementById('display_gender_toggle');
	if (toggle) {
		toggle.addEventListener("click", function(event)
		{
	    	var checkbox = document.getElementById('display_gender');
		    checkbox.value = (checkbox.value == '1' ? '0' : '1');
		    toggle.setAttribute('class', (toggle.getAttribute('class') == 'checkbox' ? 'checkbox checked' : 'checkbox'));
		}, true);
	}
})();