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


if (isset($_POST['toDelete'])) {
    $ID_community = $_POST['ID_community'];

    $sql = " SELECT communityName FROM communities WHERE ID = $ID_community; ";
    if ($ris = $conn->query($sql)) {
        $row = $ris->fetch_assoc();
        $communityName = $row['communityName'];
    }
    ?>

    <div class="alert alert-warning" role="alert">
        <div class="alert-close">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close" autofocus="autofocus">
                <span aria-hidden="true"><i class="la la-close"></i></span>
            </button>
        </div>
        <div class="alert-text">
            <h4 class="alert-heading">Attenzione!</h4>
            <p>Vuoi davvero eliminare la comunità "<?php echo $communityName; ?>"? L'operazione sarà irreversibile.</p>
            <table>
                <tr>   
                    <td>
                        <form class="kt-form" action="listCommunities.php" method="post">
                            <button type="submit" class="btn btn-info">Cancella</button>
                        </form>
                    </td>
                    <td>
                        <form class="kt-form" action="listCommunities.php" method="post">
                            <input type="hidden" name="communityName" value="<?php echo $communityName; ?>">
                            <input type="hidden" name="ID_community" value="<?php echo $ID_community; ?>">
                            <button type="submit" name="deleted" value="1" class="btn btn-red">Sì, sono sicuro</button>
                        </form>
                    </td>
                </tr>
            </table>
        </div>
    </div>


    <?php
}
if ($_POST['deleted']) {
    $communityName = $_POST['communityName'];
    $ID_community = $_POST['ID_community'];

    $sql = "DELETE FROM communities WHERE ID = '$ID_community' ";

    if ($conn->query($sql)) {
        ?>
        <div class="alert alert-success" role="alert">
            <div class="alert-close">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close" autofocus="autofocus">
                    <span aria-hidden="true"><i class="la la-close"></i></span>
                </button>
            </div>
            <div class="alert-text">
                <p>Comunità "<?php echo $communityName; ?>" eliminata. </p>
            </div>
        </div>
        <?php
    } else {
        ?>
        <div class="alert alert-danger" role="alert">
            <div class="alert-close">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close" autofocus="autofocus">
                    <span aria-hidden="true"><i class="la la-close"></i></span>
                </button>
            </div>
            <div class="alert-text">
                <p>Errore nella procedura</p>
            </div>
        </div>
        <?php
    }
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

        $sql = "SELECT *, communities.ID as ID_community "
                . "FROM communities "
                . "INNER JOIN users on users.ID = communities.ID_player "
                . "WHERE communities.ID_turn = $ID_selectedTurn AND username <> 'neutral' ORDER BY users.ID ";

        $sqltitle = "SELECT numTurn FROM turns WHERE ID = $ID_selectedTurn";
        $ris = $conn->query($sqltitle);
        $row = $ris->fetch_assoc();
        $titlepagina = $row['numTurn'];
        ?>
        <div class="container">
            <div class="">
                <table class="table table-bordered table-hover table-striped table-dark">
                    <thead>
                        <tr class="noBorder">
                            <th colspan="3">
                    <form class="kt-form" action="listCommunities.php" method="post">
                        <select name="ID_selectedTurn" class="right" onchange="this.form.submit()">

                            <?php
                            $sqlturn = "SELECT * FROM turns where ID_campaign = $ID_campaign ORDER BY numTurn DESC";

                            if ($ris = $conn->query($sqlturn)) {
                                while ($row = $ris->fetch_assoc()) {
                                    $numeroSelezionato = $row["numTurn"];

                                    if (($ID_selectedTurn) && $ID_selectedTurn == $row["ID"]) {
                                        $selected = "selected";
                                    } else
                                        $selected = "";
                                    ?> 

                                    <option value="<?php echo $row["ID"] ?>" <?php echo $selected; ?>> Turno <?php echo $numeroSelezionato ?> </option>

                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </form> 
                    </th>
                    </tr>
                    <tr>
                        <th colspan="3" style="text-align:center" class="p-3 mb-2 btn-black text-white"><strong>Elenco comunità dei giocatori - turno <?php echo $titlepagina; ?></strong></th>
                    </tr>
                    <tr>
                        <th style="text-align:center; vertical-align:middle;" width="15%">Comunità</th>
                        <th style="text-align:center; vertical-align:middle;" width="5%">Punteggio</th>
                        <th style="text-align:center; vertical-align:middle;" width="5%">Azioni</th>
                    </tr>
                    </thead>
                    <tbody>

                        <?php
                        if ($ris = $conn->query($sql)) {

                            while ($row = $ris->fetch_assoc()) {
                                $ID_player = $row['ID'];
                                $ID_community = $row['ID_community'];
                                $communityName = $row['communityName'];
                                $username = $row['username'];
                                $population = $row['population'];
                                $pViolence = $row['pViolence'];
                                $food = $row['food'];
                                $weapon = $row['weapon'];
                                $hygiene = $row['hygiene'];

                                $events = $row['events'];

                                if ($weapon < ($population + 3)) {
                                    $popArmata = $weapon;
                                } else {
                                    $popArmata = ($population + 3);
                                }

                                $baseStrength = floor($popArmata * 100);

                                if ($pViolence == 1) {
                                    $violence = $baseStrength * 0.05;
                                } elseif ($pViolence == 2) {
                                    $violence = $baseStrength * 0.10;
                                } elseif ($pViolence == 3) {
                                    $violence = $baseStrength * 0.15;
                                } elseif ($pViolence == 4) {
                                    $violence = $baseStrength * 0.20;
                                } elseif ($pViolence >= 5) {
                                    $violence = $baseStrength * 0.25;
                                }

                                $hungry = 0;
                                if ($popArmata > ($food * 2)) {
                                    $hungry = ($baseStrength * 0.25) * -1;
                                }

                                if ($popArmata > ($food)) {
                                    $hungry = ($baseStrength * 0.50) * -1;
                                }

                                $clean = 0;
                                if ($hygiene >= 75) {
                                    $clean = $baseStrength * 0.25;
                                } elseif ($hygiene < 50 && $hygiene >= 25) {
                                    $clean = ($baseStrength * 0.10) * -1;
                                } elseif ($hygiene < 25) {
                                    $clean = ($baseStrength * 0.25) * -1;
                                }

                                $strength = $baseStrength + $clean + $violence + $hungry;
                                ?>
                                <tr>
                                    <td style="vertical-align:middle;" scope="row"><?php echo $communityName; ?><br>(<?php echo $username; ?>)</td>
                                    <td style="text-align:center; vertical-align:middle;" scope="row"><?php echo $strength; ?></td>
                                    <td scope="row" style="vertical-align:middle; text-align:center">
                                        <form class="kt-form" action="community.php" method="post">
                                            <input type="hidden" name="ID_player" value="<?php echo $ID_player ?>">
                                            <input type="hidden" name="ID_community" value="<?php echo $ID_community ?>">
                                            <button class="btn btn-black btn-block">Dettaglio</button>
                                        </form>
                                        <?php
                                        if ($admin) {
                                            ?>
                                        <form class="kt-form" action="createEffect.php" method="post">
                                            <input type="hidden" name="ID_player" value="<?php echo $ID_player ?>">
                                                <input type="hidden" name="modification" value="<?php echo $ID_community ?>">
                                                <button class="btn btn-black btn-block">Evento</button>
                                            </form>
                                            <form class="kt-form" action="listCommunities.php" method="post">
                                                <input type="hidden" name="ID_player" value="<?php echo $ID_player ?>">
                                                <input type="hidden" name="ID_community" value="<?php echo $ID_community ?>">
                                                <input type="hidden" name="toDelete" value="1">
                                                <button class="btn btn-red btn-block">Elimina</button>
                                            </form>
                                            <?php
                                        }
                                        ?>

                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
        $sql = "SELECT *, communities.ID as ID_community "
                . "FROM communities "
                . "INNER JOIN users on users.ID = communities.ID_player "
                . "WHERE communities.ID_turn = $ID_selectedTurn AND username = 'neutral' ORDER BY users.ID ";

        $sqltitle = "SELECT numTurn FROM turns WHERE ID = $ID_selectedTurn";
        $ris = $conn->query($sqltitle);
        $row = $ris->fetch_assoc();
        $titlepagina = $row['numTurn'];
        ?>
        <div class="container">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-dark table-striped">
                    <thead>
                        <tr>
                            <th colspan="3" style="text-align:center" class="p-3 mb-2 btn-black text-white"><strong>Elenco comunità neutrali - turno <?php echo $titlepagina; ?></strong></th>
                        </tr>

                        <tr>
                            <th style="text-align:center; vertical-align:middle;" width="15%">Comunità</th>
                            <th style="text-align:center; vertical-align:middle;" width="5%">Punteggio</th>
                            <th style="text-align:center; vertical-align:middle;" width="5%">Azioni</th>
                        </tr>

                    </thead>
                    <tbody>

                        <?php
                        if ($ris = $conn->query($sql)) {

                            while ($row = $ris->fetch_assoc()) {
                                $ID_player = $row['ID'];
                                $ID_community = $row['ID_community'];
                                $communityName = $row['communityName'];
                                $population = $row['population'];
                                $pViolence = $row['pViolence'];
                                $food = $row['food'];
                                $weapon = $row['weapon'];
                                $hygiene = $row['hygiene'];

                                $events = $row['events'];

                                if ($weapon < ($population + 3)) {
                                    $popArmata = $weapon;
                                } else {
                                    $popArmata = ($population + 3);
                                }

                                $baseStrength = floor($popArmata * 100);

                                if ($pViolence == 1) {
                                    $violence = $baseStrength * 0.05;
                                } elseif ($pViolence == 2) {
                                    $violence = $baseStrength * 0.10;
                                } elseif ($pViolence == 3) {
                                    $violence = $baseStrength * 0.15;
                                } elseif ($pViolence == 4) {
                                    $violence = $baseStrength * 0.20;
                                } elseif ($pViolence >= 5) {
                                    $violence = $baseStrength * 0.25;
                                }

                                $hungry = 0;
                                if ($popArmata > ($food * 2)) {
                                    $hungry = ($baseStrength * 0.25) * -1;
                                }

                                if ($popArmata > ($food)) {
                                    $hungry = ($baseStrength * 0.50) * -1;
                                }

                                $clean = 0;
                                if ($hygiene >= 75) {
                                    $clean = $baseStrength * 0.25;
                                } elseif ($hygiene < 50 && $hygiene >= 25) {
                                    $clean = ($baseStrength * 0.10) * -1;
                                } elseif ($hygiene < 25) {
                                    $clean = ($baseStrength * 0.25) * -1;
                                }

                                $strength = $baseStrength + $clean + $violence + $hungry;
                                ?>
                                <tr>
                                    <td style="vertical-align:middle;" scope="row"><?php echo $communityName; ?></td>
                                    <td style="text-align:center; vertical-align:middle;" scope="row"><?php echo $strength; ?></td>
                                    <td scope="row" style="vertical-align:middle; text-align:center">
                                        <form class="kt-form" action="community.php" method="post">
                                            <input type="hidden" name="ID_player" value="<?php echo $ID_player ?>">
                                            <input type="hidden" name="ID_community" value="<?php echo $ID_community ?>">
                                            <button class="btn btn-black btn-block">Dettaglio</button>
                                        </form>
                                        <?php
                                        if ($admin) {
                                            ?>
                                            <form class="kt-form" action="listCommunities.php" method="post">
                                                <input type="hidden" name="ID_player" value="<?php echo $ID_player ?>">
                                                <input type="hidden" name="ID_community" value="<?php echo $ID_community ?>">
                                                <input type="hidden" name="toDelete" value="1">
                                                <button class="btn btn-red btn-block">Elimina comunità</button>
                                            </form>
                                            <?php
                                        }
                                        ?>

                                    </td>
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