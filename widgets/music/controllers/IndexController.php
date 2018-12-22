<?php

class Music_IndexController extends W_Controller {
    
    public function action_index() {
        header('Location: ' . xg_absolute_url('/'));
    }
    
    public function action_blank() {
    }

    public function action_error() {
    }
    
}
?>