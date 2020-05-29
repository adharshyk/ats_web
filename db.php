<?php

    function connect_db(){
        $host = 'localhost';
        $user = 'phpmyadmin';
        $pass = 'ats_root';
        $db_name = 'ats';
        $conn = new mysqli($host, $user, $pass, $db_name );

        if ($conn->connect_error) {
            return 0;
        } 
        return $conn;
    }

?>