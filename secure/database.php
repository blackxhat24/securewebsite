<?php include('database_config.php') ?>
<?php
    $connection = new mysqli($server_address, $username, $password, $database);
    if($connection->connect_error){
        die('Connection failed: ' . $connection->connect_error);
    }