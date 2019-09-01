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


if ($admin) {
    /* ================================ Sezione centrale ================================ */
    ?>

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

                    <div class="">
                        <div class="card-body p-0">
                            <div class="row">

                                <div class="col-lg-12">
                                    <div class="p-0">
                                        <div class="text-center">
                                            <h1 class="h4 text-gray-900 mb-4">Crea comunità</h1>
                                        </div>
                                        <form class="user" action="index.php" method="post">
                                            <div class="form-group">
                                                <span>Giocatore</span>
                                                <select name="ID_player" class="form-control" id="ID_player">

                                                    <?php
                                                    $sqlturn = "SELECT users.ID as ID_player, username FROM users where admin = 0 AND username <> 'neutral' AND active = 0";

                                                    if ($ris = $conn->query($sqlturn)) {
                                                        while ($row = $ris->fetch_assoc()) {
                                                            $ID_player = $row["ID_player"];
                                                            $username = $row["username"];
                                                            ?> 
                                                            <option value="<?php echo $ID_player ?>">  <?php echo $username ?> </option>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                                <br>
                                                Comunità neutrale <input type="checkbox"  name="neutral" value="1" onchange="document.getElementById('ID_player').disabled = this.checked;" />
                                            </div>
                                            <div class="form-group">
                                                <input type="text" class="form-control form-control-user" name="communityName" placeholder="Nome comunità">
                                            </div>
                                            <input type="hidden" name="createCommunity" value="1">
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

    </div>

    <?php
    /* ================================ Fine sezione centrale ================================ */
} else {
    header("location: login.php");
    $_SESSION['redirect'] = "createCommunity";
}

$footer = new Footer();
$footer->show("../../");

session_commit();
?>