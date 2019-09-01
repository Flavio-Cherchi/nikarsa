<?php

include "./class/ConnessioneDB.php";
include "./class/Starter.php";
include "./class/Head.php";
include "./class/Footer.php";
include "./class/Neutral.php";

$conn = connessione::start();
$head = new Head();
$head->show();

session_start();

//Alter Table communities
//  Add Constraint hygiene Check (hygiene >= 0 AND hygiene <= 100 );


/* ================================ Sezione centrale ================================ */


//Insert
$sql = " INSERT INTO turns (ID_campaign, numTurn) VALUES ('$ID_campaign', $newTurn) ";
$ris = $conn->query($sql);

//Selezione singola
$sql = " SELECT ID FROM turns ";
$ris = $conn->query($sql);
$row = $ris->fetch_assoc();
$ID = $row['ID'];

//Selezione multipla
$sql = "SELECT * FROM communities WHERE ID_campaign = $ID_campaign;";
if ($ris = $conn->query($sql)) {

    while ($row = $ris->fetch_assoc()) {

        $ID_old = $row['ID'];
        $ID_player = $row['ID_player'];
    }
}

//Update
$sql = " UPDATE users SET active = $active WHERE ID = $ID_player; ";
$ris = $conn->query($sql);

//Check row
$sql = "SELECT * FROM com_build WHERE ID_community = $ID_community; ";
$ris = $conn->query($sql);
$check = $ris->num_rows;
if ($check) {
    /* ... */
}

$ID_community = 25562;
Neutral::journeys($ID_community);

/* ================================ Fine sezione centrale ================================ */


$footer = new Footer();
$footer->show("../../");

session_commit();
?>