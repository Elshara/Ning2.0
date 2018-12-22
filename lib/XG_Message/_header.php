<?php
/**
 * Partial e-mail message template for the header of each message
 */
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<base href="http://<%= $_SERVER['HTTP_HOST'] %>/"/>
</head>

<body>
	<style type="text/css">
		body {
			background-color:#eee;
		}

		div.xg_head {
			width:480px;
			padding:10px;
			background-color:/* %headBgColor% */ #<%= $message['cssDefaults']['headBgColor'] %>;
			font:11px /* %textFont% */ <%= $message['cssDefaults']['textFont'] %>;
			color:/* %pageHeaderTextColor% */ #<%= $message['cssDefaults']['pageHeaderTextColor'] %>
			}
			h1.logo {
				margin:0;
				font-size:28px; font-weight:lighter; line-height:1.4em;
			}
			h1.logo img {
				display:block;
				border: 0;
			}
			p.slogan {
				margin:0 0 2em 0;
				font-size:12px;
			}

		div.xg_body {
			width:470px;
			padding:5px;
			border:10px solid /* %pageBgColor% */ #<%= $message['cssDefaults']['pageBgColor'] %>;
			background-color:#fff;
			font:11px /* %textFont% */ <%= $message['cssDefaults']['textFont'] %>;
			color:#333;
			}
			div.xg_body td {
				padding:5px;
				vertical-align:top;
			}
			div.xg_body h4 {
				margin:0;
				font-size:12px; line-height:1.6em;
			}
			div.xg_body h3 {
				margin:5px;
				font:bold 14px /* %headingFont% */ <%= $message['cssDefaults']['headingFont'] %>;
				color:#000;
			}
			div.xg_body p {
				margin:0 0 1em 0;
				font-size:12px; line-height:1.6em;
			}
			div.xg_body p big {
				font-size:15px;
			}
			div.xg_body p small {
				font-size:10px;
			}
			div.xg_body p.smallprint {
				margin:0;
				padding:5px;
				line-height:1.2em;
				color:#666;
			}
			div.xg_body a {
				color:#06c;
			}
	</style>

	<div class="xg_head">
	<?php if (isset($message['imagePaths']['logoImage'])) { ?>
		<h1 class="logo"><a href="http://<%= $_SERVER['HTTP_HOST'] %>"><img src="<%= xnhtmlentities($message['imagePaths']['logoImage']) %>" alt="<%= xnhtmlentities($message['appName']) %>"></a></h1>
	<?php } else { ?>
		<h1 class="logo"><%= xnhtmlentities($message['appName']) %></h1>
		<?php if (mb_strlen($message['appTagline'])) { ?>
		<p class="slogan"><%= xnhtmlentities($message['appTagline']) %></p>
		<?php } ?>
	<?php } ?>
	</div>
