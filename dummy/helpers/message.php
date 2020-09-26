<?php
    function add_error($error_string){
        if(!isset($_SESSION['errors']))
            $_SESSION['errors'] = [];
        $_SESSION['errors'][] = $error_string;
    }

    function add_message($message_string){
        if(!isset($_SESSION['messages']))
            $_SESSION['messages'] = [];
        $_SESSION['messages'][] = $message_string;
    }

    function add_success($success_string){
        if(!isset($_SESSION['successes']))
            $_SESSION['successes'] = [];
        $_SESSION['successes'][] = $success_string;
    }

    function set_flash($id, $content){
        $_SESSION["flash-$id"] = $content;
    }

    function get_errors(){
        if(!isset($_SESSION['errors']))
            return [];
        else{
            $errors = $_SESSION['errors'];
            $_SESSION['errors'] = [];
            return $errors;
        }
    }

    function get_messages(){
        if(!isset($_SESSION['messages']))
            return [];
        else{
            $messages = $_SESSION['messages'];
            $_SESSION['messages'] = [];
            return $messages;
        }
    }

    function get_successes(){
        if(!isset($_SESSION['successes']))
            return [];
        else{
            $successes = $_SESSION['successes'];
            $_SESSION['successes'] = [];
            return $successes;
        }
    }

    function get_flash($id){
        if(!isset($_SESSION["flash-$id"]))
            return null;
        $content = $_SESSION["flash-$id"];
        unset($_SESSION["flash-$id"]);
        return $content;
    }

    function has_error(){
        if(!isset($_SESSION['errors']) || count($_SESSION['errors']) == 0)
            return false;
        return true;
    }

    function has_message(){
        if(!isset($_SESSION['messages']) || count($_SESSION['messages']) == 0)
            return false;
        return true;
    }

    function has_success(){
        if(!isset($_SESSION['successes']) || count($_SESSION['successes']) == 0)
            return false;
        return true;
    }

    function has_flash($id){
        return isset($_SESSION["flash-$id"]);
    }