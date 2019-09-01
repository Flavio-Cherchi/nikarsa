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
if ($_POST['build']) {
    $ID_community = Building::findCommunityID($ID_user, $ID_turn);
    $ID_building = $_POST['ID_building'];
    $underCostruction = $_POST['underCostruction'];

    $numBuilding = Building::findSameBuilding($ID_community, $ID_building, $ID_turn);

    if ($numBuilding) {
        $sql = " INSERT INTO com_build (ID_community, ID_building, ID_turn, numBuilding, underCostruction, abandoned, hidden) "
                . "VALUES ('$ID_community', $ID_building, $ID_turn, $numBuilding, $underCostruction,0, 0) ";
        $ris = $conn->query($sql);

        echo "<br>Selezione -> " . $sql = "SELECT ID FROM com_build WHERE ID_community = $ID_community AND ID_building = $ID_building AND ID_turn = $ID_turn;";
        if ($ris = $conn->query($sql)) {

            while ($row = $ris->fetch_assoc()) {

                $ID = $row['ID'];
                echo "<br>Selezione -> " .  $sqlUpdate = " UPDATE com_build SET numBuilding = $numBuilding WHERE ID = $ID; ";
                
            }
            $ris = $conn->multi_query($sqlUpdate);
        }
    } else {
        var_dump($_SESSION);
        ?>
        <div class="alert alert-danger" role="alert">
            <div class="alert-close">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close" autofocus="autofocus">
                    <span aria-hidden="true"><i class="la la-close"></i></span>
                </button>
            </div>
            <div class="alert-text">
                <p>Error: users shouldn't see this message.</p>
            </div>
        </div>
        <?php
    }

    header("location: useBuildings.php");
}




$footer = new Footer();
$footer->show("../../");

session_commit();
?>