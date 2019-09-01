<?php
include "./class/ConnessioneDB.php";
include "./class/Starter.php";
include "./class/Head.php";
include "./class/Footer.php";

$conn = connessione::start();
$head = new Head();
$head->show();

session_start();

if ($error) {
    $_SESSION['error'] = $error;
}

if ($_SESSION['ID_user']) {
    $ID_user = $_SESSION['ID_user'];
    $username = $_SESSION['username'];
    $ID_campaign = $_SESSION['ID_campaign'];
    $ID_turn = $_SESSION['ID_turn'];
    $numTurn = $_SESSION['numTurn'];
    $admin = $_SESSION['admin'];
}

if ($_POST['ID_player']) {
    $ID_player = $_POST['ID_player'];
}

if ($_POST['delete']) {
    $ID_event = $_POST['ID_event'];
    $sql = " DELETE FROM `events` WHERE ID = $ID_event ";
    $ris = $conn->query($sql);
}

if ($_POST['blocca']) {
    $ID_event = $_POST['ID_event'];
    $sql = " UPDATE `events` SET active = 0 WHERE ID = $ID_event ";
    $ris = $conn->query($sql);
}


if ($_POST['sblocca']) {
    $ID_event = $_POST['ID_event'];
    $sql = " UPDATE `events` SET active = 1 WHERE ID = $ID_event ";
    $ris = $conn->query($sql);
}



if ($ID_user) {
    /* ================================ Sezione centrale ================================ */
    ?>

    <!--    <div class="ischidados "> -->
    <div>
        <?php
        $navbar = new Start();
        $navbar->go();
        ?>
        <br>

        <?php
        if ($_POST['ID_selectedTurn']) {
            $_POST['ID_selectedTurn'];
            $ID_selectedTurn = $_POST['ID_selectedTurn'];
        } else {
            $ID_selectedTurn = $ID_turn;
        }
        if ($admin) {
            //$sql = "SELECT * FROM events ORDER BY ID DESC";
            echo $sql = "SELECT * FROM users "
                    . "INNER JOIN communities on users.ID = communities.ID_player "
                    . "WHERE admin = 0 AND ID_turn = $ID_turn AND ID_campaign = $ID_campaign";
        } else {
            //$sql = "SELECT * FROM events WHERE uploadedBy = '$username' ORDER BY ID DESC";
            $sql = "SELECT * FROM users "
                    . "INNER JOIN communities on users.ID = communities.ID_player "
                    . "WHERE admin = 0 AND ID_turn = $ID_turn AND ID_campaign = $ID_campaign";
        }
        ?>

        <div class="">
            <div class="table-responsive">
                <h1><center><strong>Eventi turno <?php echo $numTurn; ?></strong></center></h1>
                <table class="table table-bordered table-hover table-striped father">
                    <thead>
                    <tr>
                        <th style="text-align:center; vertical-align:mittle;" width="10%">Comunità</th>
                        <th style="text-align:center; vertical-align:middle;" width="10%">Titolo</th>
                        <th style="text-align:center; vertical-align:middle;" width="10%">Effetti</th>
                    </tr>
                    </thead>
                    <tbody>

                        <?php
                        if ($ris = $conn->query($sql)) {

                            while ($row = $ris->fetch_assoc()) {
                                 $ID_player = $row['ID_player'];
                                $player = $row['username'];
                                $title = $row['title'];
                                $description = $row['description'];
                                /*
                                $ID_event = $row['ID'];
                                $uploadedBy = $row['uploadedBy'];
                                $title = $row['title'];
                                $description = $row['description'];
*/
                                if ($row['effetti']) {
                                    $effects = $row['effetti'];
                                } else {
                                    $effects = "nessuno";
                                }
                                ?>
                        <tr>
                                    <td style="vertical-align:top;" scope="row"><?php echo $player; ?></td>
                                    <td style="vertical-align:top;" scope="row"><?php echo $title; ?></td>
                                    <td style="vertical-align:top;" scope="row"><?php echo $effects; ?></td>
</tr>
                                    <?php
                                }
                            }
                            ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php ?>

    </div>

    <?php
    /* ================================ Fine sezione centrale ================================ */
} else {
    header("location: login.php");
    $_SESSION['redirect'] = "listCommunities";
}

$footer = new Footer();
$footer->show("../../");

session_commit();
?>