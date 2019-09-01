<?php

class connessione {

    public function __construct() {
        
    }

    public static function start() {

        $conn = new mysqli('89.46.111.107', 'Sql1342960', '50r27kl61p', 'Sql1342960_3');
//verifico la connessione al db
        if ($mysqli->connect_errno) {
            printf("Errore: %s\n", $mysqli->connect_errno);
            exit();
        }
        
        return $conn;
    }

}
