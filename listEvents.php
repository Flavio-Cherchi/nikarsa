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

if (isset($_POST['toDelete'])) {
        $ID_event = $_POST['ID_event'];

    $sql = " SELECT title FROM events WHERE ID = $ID_event; ";
    if ($ris = $conn->query($sql)) {
        $row = $ris->fetch_assoc();
        $title = $row['title'];
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
            <p>Vuoi davvero eliminare l'evento  "<?php echo $title; ?>"? L'operazione sarà irreversibile.</p>
            <table>
                <tr>   
                    <td>
                        <form class="kt-form" action="listEvents.php" method="post">
                            <button type="submit" class="btn btn-info">Cancella</button>
                        </form>
                    </td>
                    <td>
                        <form class="kt-form" action="listEvents.php" method="post">
                            <input type="hidden" name="ID_event" value="<?php echo $ID_event; ?>">
                            <input type="hidden" name="title" value="<?php echo $title; ?>">
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
    $ID_event = $_POST['ID_event'];
    $title = $_POST['title'];
    $sql = " DELETE FROM `events` WHERE ID = $ID_event ";
      if ($conn->query($sql)) {
        ?>
        <div class="alert alert-success" role="alert">
            <div class="alert-close">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close" autofocus="autofocus">
                    <span aria-hidden="true"><i class="la la-close"></i></span>
                </button>
            </div>
            <div class="alert-text">
                <p>Evento "<?php echo $title; ?>" eliminato. </p>
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
            $sql = "SELECT * FROM events ORDER BY ID DESC";
        } else {
            $sql = "SELECT * FROM events WHERE uploadedBy = '$username' ORDER BY ID DESC";
        }
        ?>

        <div class="">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped father">
                    <thead>
                        <tr>
                    <form class="user" action="createEvent.php" method="post">
                        <th colspan="5" style="text-align:center" class="p-3 mb-2 btn-black text-white">
                            <strong>
                                <input class="btn btn-black btn-user btn-block" type="submit" value="Nuovo evento">
                            </strong>
                        </th>
                    </form>
                    </tr>
                    <tr>
                        <th colspan="3" style="text-align:center" class="p-3 mb-2 text-black"><strong>Elenco eventi</strong></th>
                    </tr>
                    <tr>
                        <th style="text-align:center; vertical-align:mittle;" width="10%">Creatore</th>
                        <th style="text-align:center; vertical-align:middle;" width="10%">Titolo</th>
                        <th style="text-align:center; vertical-align:middle;" width="10%">Azioni</th>
                    </tr>
                    </thead>
                    <tbody>

                        <?php
                        if ($ris = $conn->query($sql)) {

                            while ($row = $ris->fetch_assoc()) {
                                $ID_event = $row['ID'];
                                $uploadedBy = $row['uploadedBy'];
                                $title = $row['title'];
                                $description = $row['description'];

                                if ($row['effetti']) {
                                    $effetti = $row['effetti'];
                                } else {
                                    $effetti = "nessuno";
                                }
                                ?>
                                <tr>
                                    <td style="vertical-align:top;" scope="row"><?php echo $uploadedBy; ?></td>
                                    <td style="vertical-align:top;" scope="row"><?php echo $title; ?></td>
                                    <td scope="row" style="vertical-align:middle; text-align:center">
                                        <form class="kt-form" action="createEvent.php" method="post">
                                            <input type="hidden" name="ID_event" value="<?php echo $ID_event ?>">
                                            <input type="hidden" name="leggi" value="1">
                                            <button class="btn btn-black btn-block">Descrizione</button>
                                        </form>
                                        <?php
                                        if ($admin) {
                                            ?> 
                                        <form class="kt-form" action="createEffect.php" method="post">
                                                <?php
                                                if ($ID_player) {
                                                    ?>
                                            <input type="hidden" name="modification" value="<?php echo $ID_player ?>">
                                                    <?php
                                                }
                                                ?>
                                                <input type="hidden" name="useEvent" value="<?php echo $ID_event ?>">
                                                <button class="btn btn-black btn-block">Usa</button>
                                            </form>
                                            <?php
                                            /*
                                            if ($active) {
                                                $name = "blocca";
                                                $color = "primary";
                                            } else {
                                                $name = "sblocca";
                                                $color = "warning";
                                            }
                                            */
                                            ?>
                                        <!--
                                            <form class="kt-form" action="listEvents.php" method="post">
                                                <input type="hidden" name="ID_event" value="<?php echo $ID_event; ?>">
                                                <input type="hidden" name="<?php echo $name; ?>" value="1">
                                                <button class="btn btn-<?php echo $color; ?> btn-block"><?php echo ucfirst($name); ?></button>
                                            </form>
                                        -->
                                            <form class="kt-form" action="listEvents.php" method="post">
                                                <input type="hidden" name="ID_event" value="<?php echo $ID_event; ?>">
                                                <input type="hidden" name="toDelete" value="1">
                                                <button class="btn btn-red btn-block">Elimina</button>
                                            </form>
                                            <?php
                                        }
                                        ?>
                                    </td>

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
    $_SESSION['redirect'] = "listEvents";
}

$footer = new Footer();
$footer->show("../../");

session_commit();
?>