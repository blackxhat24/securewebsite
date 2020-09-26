<?php
    function redirect($location, $http_code = 200){
        http_response_code($http_code);
        
        //  Check whether an error message file exists
        $error_message_path = "$http_code.php";
        if(file_exists($error_message_path)){
            include($error_message_path);
            die();
        }

        header('Location: ' . $location);
        die();  //  Prevent any script from occuring after redirection
    }