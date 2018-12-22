<?php
/* commenting this out for now, remnant of development
<style type="text/css" media="screen,projection">
				.chat-frame {
					background-color: white;
					width: 502px;
					height: 500px;
					border: none;
				}

				iframe.chat-frame {
					display: none;
				}

				div.chat-frame {
					padding: 0;
					margin: 0;
					text-align: center;
					padding-top: 200px;
					height: 300px;
					font-size: 200%;
					font-family: sans-serif;
				}
		</style>
		*/
  			?>
  <div id='container'>
        <iframe id="chatRoom" class="chat-frame" frameborder="0"></iframe>
        <div id="loadingChatRoom" class="chat-frame"><%= xg_html('LOADING_CHAT') %></div>
    </div>
<script>(function(){
	var xhr = function (url, cb) {
		var http = window.ActiveXObject ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
		http.onreadystatechange = function () {
			if (4 != http.readyState || !http["status"]) return;
			if (http.status < 200 || http.status >= 300) throw "HTTP Error: "+http.status;
			cb(eval(http.responseText));
		};
		http.open('POST', url, true);
		http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		http.send('xg_token='+xg.token);
	}

	var frame = document.getElementById('chatRoom');
    var loading = document.getElementById('loadingChatRoom');
    var before = document.getElementById('before');
    var container = document.getElementById('container');
    var after = document.getElementById('after');

<?php
				if ($this->moduleLocation == "full") {
					$width = 680;
					$height = 500;
				}
				else if ($this->moduleLocation == "left") {
					$width = 220;
					$height = 400;
				}
				else if ($this->moduleLocation == "right") {
					$width = 173;
					$height = 400;
				}
				else { //if ($this->moduleLocation == "center") { also 'middle'
					$width = 492;
					$height = 500;
				}
?>

    var width = <%= "$width" %>;
    var height = <%= "$height" %>;

    container.style.width = width + 'px';
    if (before) before.style.width = width + 'px';
    if (after) after.style.width = width + 'px';
    frame.style.width = width + 'px';
    loading.style.width = width + 'px';

    container.style.height = height + 'px';
    frame.style.height = height + 'px';
    loading.style.height = height + 'px';

	var show = function() {
		document.getElementById('loadingChatRoom').style.display = 'none';
		frame.style.display = 'block';
	};
	frame.onload = show;
    // IE
	frame.onreadystatechange = function() {
		if (frame.readyState=="complete") {
			show();
		}
	};
	var loader = function() {
	        // this route is exempt from CSRF check (BAZ-9736) [ywh 2008-09-12]
		xhr(<%=json_encode($this->startChatUrl)%>, function(data) {
			frame.src='http://<%= $this->chatServer %>/?a='
			+ data['appSubdomain']
			+ '&h='
			+ data['appHost']
			+ '&t='
			+ data['token']
		});
	}
	if (window.addEventListener) window.addEventListener('load', loader, false);
	else if (window.attachEvent) window.attachEvent('onload', loader);
	else window.onload = loader;
})();</script>
