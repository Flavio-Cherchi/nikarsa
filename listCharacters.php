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
    $numTurn = $_SESSION['numlTurn'];
    $admin = $_SESSION['admin'];
}

if (isset($_POST['toDelete'])) {
    $ID_character = $_POST['ID_character'];
    $name = $_POST['name'];
    if ($_POST['neutral']) {
        $neutral = 1;
    }

    $sql = " SELECT ID_community FROM characters WHERE ID = $ID_character AND ID_turn = $ID_turn; ";
    if ($ris = $conn->query($sql)) {
        $row = $ris->fetch_assoc();
        $ID_community = $row['ID_community'];
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
            <p>Vuoi davvero eliminare il personaggio "<?php echo $name; ?>"? L'operazione sarà irreversibile.</p>
            <table>
                <tr>   
                    <td>
                        <form class="kt-form" action="listCharacters.php" method="post">
                            <button type="submit" class="btn btn-info">Cancella</button>
                        </form>
                    </td>
                    <td>
                        <form class="kt-form" action="listCharacters.php" method="post">
                            <input type="hidden" name="ID_character" value="<?php echo $ID_character; ?>">
                            <input type="hidden" name="name" value="<?php echo $name; ?>">
                            <input type="hidden" name="ID_community" value="<?php echo $ID_community; ?>">
                            <?php
                            if ($neutral) {
                                ?>
                                <input type="hidden" name="neutral" value="1">
                                <?php
                            }
                            ?>

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

    $ID_character = $_POST['ID_character'];
    $name = $_POST['name'];
    $ID_community = $_POST['ID_community'];
    if ($_POST['neutral']) {
        $neutral = 1;
    }

    $sql = "DELETE FROM characters WHERE ID = '$ID_character' ";

    if ($conn->query($sql)) {

        $sql = " SELECT COUNT(ID) as count FROM characters WHERE ID_community = $ID_community AND ID_turn = $ID_turn; ";
        if ($ris = $conn->query($sql)) {
            $row = $ris->fetch_assoc();
            if ($neutral) {

                if ($row['count'] < 3) {


                    $classes = ["violent", "cooperative", "autarkic"];
                    $sel = rand(0, 2);
                    $class = $classes[$sel];

                    $sql = " SELECT name, sex FROM zMockDate ORDER BY RAND ( ) LIMIT 1 ";
                    if ($ris = $conn->query($sql)) {
                        while ($row = $ris->fetch_assoc()) {
                            $first = $row["name"];
                            $sex = $row["sex"];
                        }
                    }

                    $sql = " SELECT surname FROM zMockDate ORDER BY RAND ( ) LIMIT 1 ";
                    if ($ris = $conn->query($sql)) {
                        while ($row = $ris->fetch_assoc()) {
                            $second = $row["surname"];
                        }
                    }
                    $nameNew = $first . " " . $second;
                    $description = "personaggio neutrale";

                    $sql = " SELECT url FROM images WHERE tag = 'characters' AND sex = '$sex' ORDER BY RAND ( ) LIMIT 1 ";
                    $ris = $conn->query($sql);
                    $row = $ris->fetch_assoc();
                    $url = $row['url'];

                    $name = str_replace("'", "\\'", $name);
                    $description = str_replace("'", "\\'", $description);
                    $sql = " INSERT INTO characters "
                            . "(name, description, level, subLevel, class , ID_community, ID_turn, img) "
                            . "VALUES ('$nameNew', '$description', 1, 1, '$class', $ID_community, $ID_turn, '$url')";
                    $ris = $conn->query($sql);
                    $msg = "Ne è stato generato automaticamente uno nuovo (" . $nameNew . ") di livello uno";
                }
            } else {
                $msg = "Il giocatore deve provvedere a crearne uno nuovo";
            }
        }
        ?>
        <div class="alert alert-success" role="alert">
            <div class="alert-close">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close" autofocus="autofocus">
                    <span aria-hidden="true"><i class="la la-close"></i></span>
                </button>
            </div>
            <div class="alert-text">
                <p>Personaggio <?php echo $name . " eliminato. " . $msg; ?></p>
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

        $sql = "SELECT *, users.username as player, characters.id as ID_character, characters.img as image FROM characters "
                . "INNER JOIN communities ON communities.ID = characters.ID_community "
                . "INNER JOIN users ON users.ID = communities.ID_player "
                . "WHERE characters.ID_turn = $ID_turn AND username <> 'neutral' ORDER BY communities.communityName DESC";
        ?> 

        <div class="container">
            <div class="table-responsive">

                <table class="table table-bordered table-hover table-striped table-dark">
                    <thead>
                        <tr>
                            <th colspan="3" style="text-align:center" class="p-3 mb-2 btn-black text-white"><strong>Personaggi dei giocatori</strong></th>
                        </tr>
                        <tr>
                            <th style="text-align:center; vertical-align:middle;" width="10%">Dati</th>
                            <th class="hide" style="text-align:center; vertical-align:middle;" width="5%">Foto</th>
                            <th style="text-align:center; vertical-align:middle;" width="10%">Azioni</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        if ($ris = $conn->query($sql)) {

                            while ($row = $ris->fetch_assoc()) {
                                $ID_character = $row['ID_character'];
                                $name = $row['name'];
                                $level = $row['level'];
                                $class = $row['class'];
                                $img = $row['image'];
                                $player = $row['player'];
                                $communityName = $row['communityName'];

                                if ($class == "violent") {
                                    $italian = "violento";
                                } elseif ($class == "cooperative") {
                                    $italian = "cooperativo";
                                } elseif ($class == "autarkic") {
                                    $italian = "autarchico";
                                }
                                ?>
                                <tr>
                                    <td style="text-align:center; vertical-align:middle;" scope="row"><center><b><?php echo $player; ?><br>(<?php echo $communityName; ?>)</b><br><br><?php echo $name; ?><br>Personaggio <?php echo $italian; ?><br>Livello <?php echo $level; ?></center></td>
                            <td class="hide"  style="text-align:center; vertical-align:middle;" scope="row"><img class="elencoImg" src="<?php echo $img; ?>" alt="image"></td>
                            <td scope="row" style="vertical-align:middle; text-align:center">
                                <form class="kt-form" action="characters.php" method="post">
                                    <input type="hidden" name="ID_character" value="<?php echo $ID_character ?>">
                                    <input type="hidden" name="justOne" value="1">
                                    <button class="btn btn-black btn-block">Descrizione</button>
                                </form>
                                <?php
                                if ($admin) {
                                    ?> 
                                    <!--
                                        <form class="kt-form" action="#" method="post">
                                            <input type="hidden" name="ID_event" value="<?php echo $ID_event ?>">
                                    <?php
                                    if ($ID_player) {
                                        ?>
                                                                            <input type="hidden" name="ID_player" value="<?php echo $ID_player ?>">
                                        <?php
                                    }
                                    ?>
                                            <input type="hidden" name="assegnaEvento" value="1">
                                            <button class="btn btn-black btn-block">Usa</button>
                                        </form>
                                    -->
                                    <form class="kt-form" action="listCharacters.php" method="post">
                                        <input type="hidden" name="ID_character" value="<?php echo $ID_character; ?>">
                                        <input type="hidden" name="name" value="<?php echo $name; ?>">
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

                <?php
                $sql = "SELECT *, users.username as player, characters.id as ID_character, characters.img as image FROM characters "
                        . "INNER JOIN communities ON communities.ID = characters.ID_community "
                        . "INNER JOIN users ON users.ID = communities.ID_player "
                        . "WHERE characters.ID_turn = $ID_turn AND users.username = 'neutral' ORDER BY communities.communityName DESC";
                ?> 

                <table class="table table-bordered table-hover table-striped table-dark">
                    <thead>
                        <tr>
                            <th colspan="3" style="text-align:center" class="p-3 mb-2 btn-black text-white"><strong>Personaggi neutrali</strong></th>
                        </tr>
                        <tr>
                            <th style="text-align:center; vertical-align:middle;" width="10%">Dati</th>
                            <th class="hide" style="text-align:center; vertical-align:middle;" width="5%">Foto</th>
                            <th style="text-align:center; vertical-align:middle;" width="10%">Azioni</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        if ($ris = $conn->query($sql)) {

                            while ($row = $ris->fetch_assoc()) {
                                $ID_character = $row['ID_character'];
                                $name = $row['name'];
                                $level = $row['level'];
                                $class = $row['class'];
                                $img = $row['image'];
                                $communityName = $row['communityName'];

                                if ($class == "violent") {
                                    $italian = "violento";
                                } elseif ($class == "cooperative") {
                                    $italian = "cooperativo";
                                } elseif ($class == "autarkic") {
                                    $italian = "autarchico";
                                }
                                ?>
                                <tr>
                                    <td style="text-align:center; vertical-align:middle;" scope="row"><center><b><?php echo $communityName; ?></b><br><br><?php echo $name; ?><br>Personaggio <?php echo $italian; ?><br>Livello <?php echo $level; ?></center></td>
                            <td  class="hide" style="text-align:center; vertical-align:middle;" scope="row"><img class="elencoImg" src="<?php echo $img; ?>" alt="image"></td>
                            <td scope="row" style="vertical-align:middle; text-align:center">
                                <form class="kt-form" action="characters.php" method="post">
                                    <input type="hidden" name="ID_character" value="<?php echo $ID_character ?>">
                                    <input type="hidden" name="justOne" value="1">
                                    <button class="btn btn-black btn-block">Descrizione</button>
                                </form>
                                <?php
                                if ($admin) {
                                    ?> 
                                    <!--
                                        <form class="kt-form" action="#" method="post">
                                            <input type="hidden" name="ID_event" value="<?php echo $ID_event ?>">
                                    <?php
                                    if ($ID_player) {
                                        ?>
                                                                            <input type="hidden" name="ID_player" value="<?php echo $ID_player ?>">
                                        <?php
                                    }
                                    ?>
                                            <input type="hidden" name="assegnaEvento" value="1">
                                            <button class="btn btn-black btn-block">Usa</button>
                                        </form>
                                    -->
                                    <form class="kt-form" action="listCharacters.php" method="post">
                                        <input type="hidden" name="ID_character" value="<?php echo $ID_character; ?>">
                                        <input type="hidden" name="name" value="<?php echo $name; ?>">
                                        <input type="hidden" name="neutral" value="1">
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
    $_SESSION['redirect'] = "listCharacters";
}

$footer = new Footer();
$footer->show("../../");

session_commit();
?>