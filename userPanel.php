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
    $title = $_SESSION['title'];
    $numTurn = $_SESSION['numTurn'];
}


if ($ID_user) {

    if ($_POST['communityName']) {
        $ID_community = $_POST['ID_community'];
       $communityName = $_POST['communityName'];
    }

    /* Check for food ration: half ration or full ration */


    if (!$_POST['ration']) {
        $sql = " SELECT ration FROM communities WHERE communityName = '$communityName' AND ID_turn = $ID_turn; ";
        if ($ris = $conn->query($sql)) {

            $row = $ris->fetch_assoc();
            $ration = $row['ration'];
            $italian = ($ration == "full") ? "Dimezza le razioni" : "Aumenta le razioni";
            $color = ($ration == "full") ? "btn-red" : "btn-warning";
        }
    } else {
        $ration = $_POST['ration'];
        $rationNew = ($ration == "full") ? "half" : "full";
        $sql = " UPDATE communities SET ration = '$rationNew' WHERE communityName = '$communityName' AND ID_turn = $ID_turn; ";
        $ris = $conn->query($sql);
        $ration = $rationNew;
        $italian = ($ration == "full") ? "Dimezza le razioni" : "Aumenta le razioni";
        $color = ($ration == "full") ? "btn-red" : "btn-warning";
    }








    /* ================================ Sezione centrale ================================ */
    ?>

    <body>
        <!--    <div class="ischidados "> -->
        <div>

            <?php
            $navbar = new Start();
            $navbar->go();
            ?>
            <br>
            <div class="container">

                <!-- Outer Row -->
                <div class="row justify-content-center">

                    <div class="col-xl-10 col-lg-12 col-md-9">
                        <!-- Nested Row within Card Body -->
                        <div class="row">

                            <div class="col-lg-12">
                                <div class="p-0">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Gestione comunità</h1>
                                    </div>
                                    <form class="user" action="userPanel.php" method="post">
                                        <input type="hidden" name="ration" value="<?php echo $ration; ?>">
                                        <input type="hidden" name="communityName" value="<?php echo $communityName; ?>">
                                        <input class="btn <?php echo $color; ?> btn-user btn-block" type="submit" value="<?php echo $italian; ?>">
                                    </form>
                                    <hr>
                                    <button class="btn btn-black btn-user btn-block collapsed" type="button" data-toggle="collapse" data-target="#change">
                                        Modifiche
                                    </button>

                                    <div id="change" class="collapse">
                                        <br>
                                        <button class="btn btn-black btn-user btn-block collapsed" type="button" data-toggle="collapse" data-target="#changeName">
                                            Cambia nome comunità
                                        </button>
                                        <div id="changeName" class="collapse">
                                            <br>
                                            <center><form class="user" action="community.php" method="post">
                                                    <input type="hidden" name="changeName" value="1">
                                                    <input type="text" name="newName" placeholder="<?php echo $communityName; ?>">
                                                    <input class="btn btn-black btn-user" type="submit" value="Modifica">
                                                </form>
                                            </center>
                                        </div>
                                        <br>
                                        <form class="user" action="listImages.php" method="post">
                                            <input type="hidden" name="community" value="1">
                                            <input class="btn btn-black btn-user btn-block" type="submit" value="Cambia immagine">
                                        </form>
                                        <form class="user" action="userModify.php" method="post">
                                            <input class="btn btn-black btn-user btn-block" type="submit" value="Cambia nome utente e password">
                                        </form>
                                    </div>
                                    <hr>
                                    <form class="user" action="#" method="post">
                                        <input class="btn btn-black btn-user btn-block" type="submit" value="Gestione viaggi" disabled>
                                    </form>

                                    <form class="user" action="useBuildings.php" method="post">
                                        <input class="btn btn-black btn-user btn-block" type="submit" value="Gestione edifici">
                                    </form>

                                    <form class="user" action="#" method="post">
                                        <input type="hidden" name="imgAdmin" value="1">
                                        <input class="btn btn-black btn-user btn-block" type="submit" value="Gestione tecnologie" disabled>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>   
        </div>
    </body>
    <?php
    /* ================================ Fine sezione centrale ================================ */
} else {
    header("location: login.php");
    $_SESSION['redirect'] = "userPanel";
}

$footer = new Footer();
$footer->show("../../");

session_commit();
?>