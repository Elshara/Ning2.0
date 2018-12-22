<html>
<head>
    <script src="<%= xg_cdn('/xn_resources/widgets/shared/js/PluginDetect.js') %>"></script>
    <script>
        window.location = PluginDetect.isMinVersion('Java', '1.5') >= 0 ? '<%= $this->mediaUploaderUrl %>' : '<%= $this->simpleUploaderUrl %>';
    </script>
    <?php /* Redirect if no JavaScript [Jon Aquino 2007-12-18] */ ?>
    <meta http-equiv="refresh" content="0;url=<%= xnhtmlentities($this->simpleUploaderUrl) %>">
</head>
<body>
</body>
</html>
