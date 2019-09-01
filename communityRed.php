<?php
include "./class/ConnessioneDB.php";
include "./class/Starter.php";
include "./class/Head.php";
include "./class/Footer.php";
include "./class/Building.php";

$conn = connessione::start();
$head = new Head();
$head->show();

session_start();

if ($_SESSION['ID_user']) {
    $ID_user = $_SESSION['ID_user'];
    $username = $_SESSION['username'];
    $ID_campaign = $_SESSION['ID_campaign'];
    $ID_turn = $_SESSION['ID_turn'];
    $numTurn = $_SESSION['numTurn'];
    $admin = $_SESSION['admin'];
}

/* New building */
if ($_POST['imgdata']) {

        $imagedata = base64_decode($_POST['imgdata']);
                    //$filename = $username . "Turno" . $numTurn . "-" . md5(uniqid(rand(), true));
                    $filename = $username . "_Turno_" . $numTurn . "_" . rand(11111, 99999);
                    //path where you want to upload image
                    //$file = 'http://www.denai.it/projects/ischidados/img/screenshots/' . $filename . '.png';

                    $file = $_SERVER['DOCUMENT_ROOT'] . 'projects/ischidados/img/screenshots/' . $filename . '.png';
                    $url = 'img/screenshots/' . $filename . '.png';
                    file_put_contents($file, $imagedata);

                    $sql = " INSERT INTO images (url, uploadedBy, tag, sex) "
                            . "VALUES ('$url', '$username', 'screenshot', 'n') ";
                    if ($upload = $conn->query($sql)) {

                        $sql = " SELECT * FROM images WHERE url = '$url' ";
                        $ris = $conn->query($sql);
                        $row = $ris->fetch_assoc();
                        $ID_img = $row['ID'];
                        $url = $row['url'];
                        $uploadedBy = $row['uploadedBy'];
                    }
                    $_SESSION['url'] = $url;
                    
    }
    header( "refresh:1;url=community.php" );
    //header("location: community.php");
    
?>

<img class="center" src="img/flash.gif" alt="Flash" > 

<?php




$footer = new Footer();
$footer->show("../../");

session_commit();
?>