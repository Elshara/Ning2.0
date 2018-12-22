<html>
    <head>
        <title><%= xnhtmlentities(XN_Application::load()->name) %></title>
    </head>
    <body style="background:#000000; margin:0px; padding:0px">
        <div id="video" style="width:100%; height:100%;">
            <?php $this->_widget->dispatch('video', 'embeddableProper', array(array('id' => $this->id, 'width' => '100%', 'height' => '100%', 'layout' => 'fullscreen', 'autoplay' => true))); ?>
        </div>
    </body>
</html>
