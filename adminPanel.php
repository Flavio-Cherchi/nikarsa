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

    //To close the campaign
    if (isset($_POST['toClose'])) {
        $ID_campaign = $_POST['ID_campaign'];
        ?>

        <div class="alert alert-warning" role="alert">

            <div class="alert-close">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close" autofocus="autofocus">
                    <span aria-hidden="true"><i class="la la-close"></i></span>
                </button>
            </div>
            <div class="alert-text">
                <h4 class="alert-heading">Attenzione!</h4>
                <p>Vuoi davvero chiudere la campagna in corso? L'operazione sarà irreversibile.</p>
                <table>
                    <tr>   
                        <td>
                            <form class="kt-form" action="adminPanel.php" method="post">
                                <button type="submit" class="btn btn-info">Cancella</button>
                            </form>
                        </td>
                        <td>
                            <form class="kt-form" action="adminPanel.php" method="post">
                                <input type="hidden" name="ID_campaign" value="<?php echo $ID_campaign; ?>">
                                <button type="submit" name="status" value="close" class="btn btn-red">Sì, sono sicuro</button>
                            </form>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <?php
    }

    if ($_POST['status'] == 'close') {
        $ID_campaign = $_POST['ID_campaign'];
        $sql = " UPDATE campaign SET status=0 WHERE ID = $ID_campaign; ";
        $ris = $conn->query($sql);

        $sql = " UPDATE users SET active=0 WHERE admin = 0; ";
        $ris = $conn->query($sql);
    }


    //To create the campaign
    if ($_POST['createCampaign']) {
        $title = $_POST['title'];
        $master = $_POST['master'];

        $sql = "select count('campaignNumber') as campaignNumber from campaign ";
        $ris = $conn->query($sql);
        if ($row = $ris->fetch_assoc()) {
            $campaignNumber = $row['campaignNumber'] + 1;
        } else
            $campaignNumber = 1;

        $title = str_replace("'", "\\'", $title);
        $sql = " INSERT INTO campaign (title, status, master, campaignNumber) VALUES ('$title', 1, '$master', $campaignNumber) ";

        $ris = $conn->query($sql);

        $sql = "select ID from campaign WHERE status = 1";
        $ris = $conn->query($sql);
        $row = $ris->fetch_assoc();
        $campaignInCorso = $row['ID'];
        $sql = " insert into turns (ID_campaign, numTurn) VALUES ('$campaignInCorso', 1) ";
        $ris = $conn->query($sql);
    }

    $sql = " SELECT * from campaign ";
    if ($ris = $conn->query($sql)) {

        while ($row = $ris->fetch_assoc()) {
            if ($row['status'] == 1) {
                $attiva = $row['status'];
                $ID_campaign = $row['ID'];
                $title = $row['title'];
                $master = $row['master'];
            } else
                $attiva = 0;
        }
    } else
        $attiva = 0;


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
            <?php
            if (!$attiva) {
                ?> 
                <div class="container">

                    <!-- Outer Row -->
                    <div class="row justify-content-center">
                        <div class="col-xl-10 col-lg-12 col-md-9">
                            <div class="card o-hidden border-0 shadow-lg my-5">
                                <div class="card-body p-0">
                                    <!-- Nested Row within Card Body -->
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="p-5">
                                                <div class="text-center">
                                                    <h1 class="h4 text-gray-900 mb-4">Inizia nuova campagna</h1>
                                                </div>
                                                <form class="user" action="adminPanel.php" method="post">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control form-control-user" name="title" placeholder="Titolo della campagna">
                                                    </div>
                                                    <div class="form-group">
                                                        <?php
                                                        $sql = " SELECT * from users ";
                                                        if ($ris = $conn->query($sql)) {

                                                            while ($row = $ris->fetch_assoc()) {
                                                                if ($row['admin'] == 1) {
                                                                    $ID_admin = $row['ID'];
                                                                    $usernameAdmin = $row['username'];
                                                                    ?>
                                                                    <input type="radio" name="master" value="<?php echo $usernameAdmin; ?>"><?php echo $usernameAdmin; ?><br>
                                                                    <?php
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                    <input type="hidden" name="createCampaign" value="1">
                                                    <input class="btn btn-black btn-user btn-block" type="submit" value="Invia">
                                                    <br>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>
                <?php
            } else {
                ?>
                <div class="container">

                    <!-- Outer Row -->
                    <div class="row justify-content-center">

                        <div class="col-xl-10 col-lg-12 col-md-9">
                            <!-- Nested Row within Card Body -->
                            <div class="row">

                                <div class="col-lg-12">
                                    <div class="p-0">
                                        <div class="text-center">
                                            <h1 class="h4 text-gray-900 mb-4">Gestione master</h1>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <form class="user" action="index.php" method="post">
                                                    <input type="hidden" name="newTurn" value="1">
                                                    <input class="btn btn-black btn-user btn-block" type="submit" value="Nuovo turno">
                                                </form>
                                            </div>
                                            <div class="col-lg-4">
                                                <form class="user" action="createCommunity.php" method="post">
                                                    <input class="btn btn-black btn-user btn-block" type="submit" value="Nuova comunità">
                                                </form>
                                            </div>
                                            <div class="col-lg-4">
                                                <form class="user" action="userModify.php" method="post">
                                                    <input class="btn btn-black btn-user btn-block" type="submit" value="Modifica nomi e password">
                                                </form>
                                            </div>
                                            <div class="col-lg-4">
                                                <button class="btn btn-black btn-user btn-block collapsed" type="button" data-toggle="collapse" data-target="#manageGame">
                                                    Gestione partita
                                                </button>

                                                <div id="manageGame" class="collapse" style="margin-top: 1em;">
                                                    <form class="user" action="battle.php" method="post">
                                                        <input class="btn btn-black btn-user btn-block" type="submit" value="Genera battaglia">
                                                    </form>
                                                    <form class="user" action="createEffect.php" method="post">
                                                        <input class="btn btn-black btn-user btn-block" type="submit" value="Comunità e personaggi">
                                                    </form>
                                                    <form class="user" action="relations.php" method="post">
                                                        <input class="btn btn-black btn-user btn-block" type="submit" value="Diplomazia">
                                                    </form>
                                                    <form class="user" action="useBuildings.php" method="post">
                                                        <input class="btn btn-black btn-user btn-block" type="submit" value="Edifici">
                                                    </form>
                                                </div>
                                                <br>
                                            </div>

                                            <div class="col-lg-4"> 
                                                <button class="btn btn-black btn-user btn-block collapsed" type="button" data-toggle="collapse" data-target="#list" aria-expanded="true" aria-controls="list">
                                                    Elenchi
                                                </button>
                                                <div id="list" class="collapse" style="margin-top: 1em;">
                                                    <form class="user" action="listEvents.php" method="post">
                                                        <input class="btn btn-black btn-user btn-block" type="submit" value="Eventi">
                                                    </form>

                                                    <form class="user" action="listBuildings.php" method="post">
                                                        <input class="btn btn-black btn-user btn-block" type="submit" value="Edifici">
                                                    </form>

                                                    <form class="user" action="listImages.php" method="post">
                                                        <input type="hidden" name="imgAdmin" value="1">
                                                        <input class="btn btn-black btn-user btn-block" type="submit" value="Immagini">
                                                    </form>
                                                </div>
                                                <br>
                                            </div>


                                            <?php $status = ($attiva == 1) ? "close" : ""; ?>
                                            <div class="col-lg-4">
                                                <form class="user" action="adminPanel.php" method="post">
                                                    <input type="hidden" name="toClose" value="1">
                                                    <input type="hidden" name="ID_campaign" value="<?php echo $ID_campaign; ?>">
                                                    <input class="btn btn-red btn-user btn-block" type="submit" value="Termina partita">
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>   
                    <?php
                }
                ?>
            </div>
    </body>
    <?php
    /* ================================ Fine sezione centrale ================================ */
} else {
    header("location: login.php");
}

$footer = new Footer();
$footer->show("../../");

session_commit();
?>