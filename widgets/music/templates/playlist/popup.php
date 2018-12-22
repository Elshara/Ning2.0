<html><head><title><%= xg_xmlentities(is_null($appName) ? XN_Application::load()->name : $appName) %></title></head><body style="margin:0px;">
<?php
$this->renderPartial('fragment_playerProper',   'playlist',
                                        array(  'autoplay'                  =>  $_GET['a'],
                                                'playlist_url'              =>  $_GET['u'],
                                                'shuffle'                   =>  $_GET['s'],
                                                'select_track'              =>  $_GET['t'],
                                                'play_order'                =>  $_GET['o'],
                                                'repeat'                    =>  $_GET['r'],
                                                'width'                     => '100%',
                                                'height'                    => '100%',
                                                'showplaylist'              => 'true',
                                                'display_add_links'         => 'true',
                                                'detach_btn'                => 'off',
                                                'display_feature_btn'       => 'on',
                                                'embed' => true));
?>
</body></html>