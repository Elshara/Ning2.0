<?php
//  All HTTP errors go here - redirect to WWF's index unless the error is a 50x (internal)
$code = intval($_GET['code']);
if ($code < 500) {
    header("Location: http://{$_SERVER['HTTP_HOST']}/main/error/$code?" . $_SERVER['QUERY_STRING']);
    exit;
}

//  Set the appropriate error code in the header
$descriptions = array(
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported');
$header = 'HTTP/1.0 ' . $code;
if (isset($descriptions[$code])) {
    $header .= ' ' . $descriptions[$code];
}
header($header);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Our Apologies - <%= xnhtmlentities(XN_Application::load()->name) %></title>
    <style type="text/css" media="screen">
        body {
            margin:0;
            padding:0;
            background:#dee;
            font-size:62.5%;
            font-family:"Lucida Grande", "Trebuchet MS", "Bitstream Vera Sans", Verdana, Helvetica, sans-serif;
            color:#333;
        }
        div.content {
            width:700px;
            margin:5% auto;
            padding:30px;
            border:3px solid #cdd;
            background-color:#fff;
            font-size:1em;
        }
        div.content h1 {
            color:#390;
            font:2.4em normal Georgia, "Times New Roman", Times, serif;
            margin:0;
        }
        div.content p {
            font-size:1.2em; line-height:1.5em;
        }
        a {
            color:#06c;
        }
    </style>
</head>

<body>
    <div class="content">

        <h1>Our apologies</h1>
        <p>We're sorry, this site has an issue.</p>
        <p>Please try refreshing the page in 30 seconds, or <a href="mailto:support@ning.com?subject=<%= xnhtmlentities(urlencode($_GET['code'] . ': ' . $_GET['uri'])) %>">let us know</a> if you get this message again.</p>
    </div>
</body>
</html>
