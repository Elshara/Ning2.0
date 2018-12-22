/**
 * Handles the mechanics of the iPhone photo slideshow
 */
(function(){
	var backBtn = $('back');
	var forwardBtn = $('forward');
	var frameSlider = $('frameSlider');
	var title = $('photo_title');

	function adjustProperties() {
		switch (window.orientation){
			default:
			case 0:
			case 180:
				width = '323'; break;
			case -90:
			case 90:
				width = '483'; break;
		}
		var pixel = width * frame;
		frameSlider.style.marginLeft = '-' + pixel + 'px';
		backBtn.setAttribute('class', frame ? '' : 'disabled');
		forwardBtn.setAttribute('class', frame == total-1 ? 'disabled' : '');
		title.innerText = titles[frame];
	};

	adjustProperties();
	orientation_listeners.photos = adjustProperties;

	backBtn.onclick = function() {
	    if (frame == 0) return;
		frame--;
		adjustProperties();
    };

	forwardBtn.onclick = function() {
		if (frame == total-1) return;
		frame++;
		adjustProperties();
	};
})();
