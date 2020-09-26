<?php
    session_start();

    //  CSRF Handler
    function str_random($length){
        $result = '';
        $character = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        for($i = 0; $i < $length; $i++){
            $result .= $character[rand(0, strlen($character) - 1)];
        }
        return $result;
    }

    function get_token(){
        if(!session_has_token())
            regenerate_token();
        return $_SESSION['csrf_token'];
    }

    function regenerate_token(){
        $_SESSION['csrf_token'] = sha1(str_random(40));
    }

    function session_has_token(){
        return isset($_SESSION['csrf_token']);
    }
    
    if(!session_has_token())
        regenerate_token();