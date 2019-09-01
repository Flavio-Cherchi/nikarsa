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

if ($_POST['onAct']) {

    if (($_POST['ID_c1'] != "noSelect") && ($_POST['ID_c2'] != "noSelect")) {
        if ($_POST['ID_c1'] != $_POST['ID_c2']) {
            //name first community
            $ID_c1 = $_POST['ID_c1'];
            $sql = " SELECT communityName from communities WHERE ID = $ID_c1 AND ID_turn = $ID_turn ";
            $ris = $conn->query($sql);
            $row = $ris->fetch_assoc();
            $communityName1 = $row['communityName'];

            //name second community
            $ID_c2 = $_POST['ID_c2'];
            $sql = " SELECT communityName from communities WHERE ID = $ID_c2 AND ID_turn = $ID_turn ";
            $ris = $conn->query($sql);
            $row = $ris->fetch_assoc();
            $communityName2 = $row['communityName'];

            $sql = " SELECT ID, relationship from com_communities WHERE ID_community = $ID_c1 AND ID_know = $ID_c2 AND ID_turn = $ID_turn ";
            if ($ris = $conn->query($sql)) {

                $row = $ris->fetch_assoc();
                $ID_com_communities = $row['ID'];
                $relationship = $row['relationship'];
                if (!$ID_com_communities) {
                    //echo "sono quiiiiiiii";
                    $italian = " dei perfetti sconosciuti";
                } else {
                    switch ($relationship) {
                        case $relationship < 20;
                            $italian = "in guerra aperta";
                            break;
                        case $relationship >= 20 && $relationship < 40;
                            $italian = "in relazioni astiose";
                            break;
                        case $relationship >= 40 && $relationship < 60;
                            $italian = "neutrali";
                            break;
                        case $relationship >= 60 && $relationship < 80;
                            $italian = " in rapporti cordiali";
                            break;
                        case $relationship >= 20:
                            $italian = " in stretta alleanza";
                            break;
                        default:
                            break;
                    }
                    if ($relationship == 0) {
                        $italian = " in guerra aperta";
                    }
                }
            }
        } else {
            $response = 1;
            $msg = "Le relazioni di una comunità con sè stessa? Davvero?";
            $_POST['onAct'] = 0;
        }
    } else {
        $response = 1;
        $msg = "Prego fare una selezione plausibile";
        $_POST['onAct'] = 0;
    }
}

if ($_POST['diplomacy']) {
    $response = 1;

    $ID_c1 = $_POST['ID_c1'];
    $communityName1 = $_POST['communityName1'];

    $ID_c2 = $_POST['ID_c2'];
    $communityName2 = $_POST['communityName2'];

    $relationship = $_POST['relationship'];
    switch ($relationship) {
        case $relationship < 20;
            $italian = "in guerra aperta";
            break;
        case $relationship >= 20 && $relationship < 40;
            $italian = "in relazioni astiose";
            break;
        case $relationship >= 40 && $relationship < 60;
            $italian = "neutrali";
            break;
        case $relationship >= 60 && $relationship < 80;
            $italian = " in rapporti cordiali";
            break;
        case $relationship >= 20:
            $italian = " in stretta alleanza";
            break;
        default:
            break;
    }
    if ($relationship == 0) {
        $italian = " in guerra aperta";
    }

    $sql = " SELECT ID from com_communities WHERE ID_community = $ID_c1 AND ID_know = $ID_c2 AND ID_turn = $ID_turn ";
    $ris = $conn->query($sql);
    $row = $ris->fetch_assoc();
    $ID_com_communities = $row['ID'];

    if ($ID_com_communities) {
        $sql = " UPDATE `com_communities` "
                . "SET relationship = $relationship "
                . "WHERE ID_community = $ID_c1 AND ID_know = $ID_c2 AND ID_turn = $ID_turn";
        $ris = $conn->query($sql);
        $sql = " UPDATE `com_communities` "
                . "SET relationship = $relationship "
                . "WHERE ID_community = $ID_c2 AND ID_know = $ID_c1 AND ID_turn = $ID_turn";
        $ris = $conn->query($sql);
        $msg = "Le relazioni tra $communityName1 e $communityName2 sono cambiate, ora sono $italian";
    } else {
        $sql = " INSERT INTO com_communities (ID_community, ID_know, ID_turn, relationship) "
                . "VALUES ('$ID_c1', '$ID_c2', '$ID_turn', $relationship)";
        $ris = $conn->query($sql);
        $sql = " INSERT INTO com_communities (ID_community, ID_know, ID_turn, relationship) "
                . "VALUES ('$ID_c2', '$ID_c1', '$ID_turn', $relationship)";
        $ris = $conn->query($sql);

        $msg = "Ora $communityName1 e $communityName2  si conoscono e sono $italian";
    }

    $msg = str_replace("'", "\\'", $msg);
    $sql = " INSERT INTO effects (ID_community, ID_turn, description, tag) 
                    VALUES ($ID_c1, $ID_turn, '$msg', 'diplomacy'); ";
    $ris = $conn->query($sql);

    $msg = str_replace("'", "\\'", $msg);
    $sql = " INSERT INTO effects (ID_community, ID_turn, description, tag) 
                    VALUES ($ID_c2, $ID_turn, '$msg', 'diplomacy'); ";
    $ris = $conn->query($sql);
}

if (($_POST['plus']) || ($_POST['minus'])) {
    $ID_c1 = $_POST['ID_c1'];
    $ID_c2 = $_POST['ID_c2'];
    $diploCapacity = $_POST['diploCapacity'];
    $diploCapacity = ($_POST['plus']) ? $diploCapacity : $diploCapacity * -1;

    $sql = " SELECT relationship FROM com_communities WHERE ID_community = $ID_c1  AND ID_know = $ID_c2 AND ID_turn = $ID_turn; ";
    if ($ris = $conn->query($sql)) {
        $row = $ris->fetch_assoc();
        $relationship = $row['relationship'];

        if ($_POST['minus']) {

            if ($relationship <= 0) {
                $unchanged = "minimo";
                $sql = " UPDATE com_communities "
                        . "SET relationship = 0 "
                        . "WHERE ID_community = $ID_c1 AND ID_know = $ID_c2 AND ID_turn = $ID_turn ";
                $ris = $conn->query($sql);

                $sql = " UPDATE com_communities "
                        . "SET relationship = 0 "
                        . "WHERE ID_community = $ID_c2 AND ID_know = $ID_c1 AND ID_turn = $ID_turn ";
                $ris = $conn->query($sql);
            } else {
                $sql = " UPDATE com_communities "
                        . "SET relationship = relationship + $diploCapacity "
                        . "WHERE ID_community = $ID_c1 AND ID_know = $ID_c2 AND ID_turn = $ID_turn ";
                $ris = $conn->query($sql);

                $sql = " UPDATE com_communities "
                        . "SET relationship = relationship + $diploCapacity "
                        . "WHERE ID_community = $ID_c2 AND ID_know = $ID_c1 AND ID_turn = $ID_turn ";
                $ris = $conn->query($sql);


                $sql = " UPDATE communities SET pCooperation = pCooperation - 1 "
                        . "WHERE ID = $ID_c1";
                $ris = $conn->query($sql);

                $changed = 1;
            }
        }

        if ($_POST['plus']) {
            if ($relationship >= 100) {

                $unchanged = "massimo";
                $sql = " UPDATE com_communities "
                        . "SET relationship = 100 "
                        . "WHERE ID_community = $ID_c1 AND ID_know = $ID_c2 AND ID_turn = $ID_turn ";
                $ris = $conn->query($sql);

                $sql = " UPDATE com_communities "
                        . "SET relationship = 100 "
                        . "WHERE ID_community = $ID_c2 AND ID_know = $ID_c1 AND ID_turn = $ID_turn ";
                $ris = $conn->query($sql);
            } else {

                $sql = " UPDATE com_communities "
                        . "SET relationship = relationship + $diploCapacity "
                        . "WHERE ID_community = $ID_c1 AND ID_know = $ID_c2 AND ID_turn = $ID_turn ";
                $ris = $conn->query($sql);

                $sql = " UPDATE com_communities "
                        . "SET relationship = relationship + $diploCapacity "
                        . "WHERE ID_community = $ID_c2 AND ID_know = $ID_c1 AND ID_turn = $ID_turn ";
                $ris = $conn->query($sql);


                $sql = " UPDATE communities SET pCooperation = pCooperation - 1 "
                        . "WHERE ID = $ID_c1";
                $ris = $conn->query($sql);

                $changed = 1;
            }
        }
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
        <?php ?>
        <div class="container">

            <!-- Outer Row -->
            <div class="row justify-content-center">

                <div class="col-lg-12">

                    <!-- <div class="card o-hidden border-0 shadow-lg my-5"> -->
                        <div class="card-body p-0">
                            <!-- Nested Row within Card Body -->
                            <?php
                            if ($response) {
                                ?>
                                <div class="alert alert-success" role="alert">
                                    <div class="alert-close">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close" autofocus="autofocus">
                                            <span aria-hidden="true"><i class="la la-close"></i></span>
                                        </button>
                                    </div>
                                    <div class="alert-text">
                                        <p><?php echo $msg; ?></p>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="p-0">
                                        <div class="text-center">
                                            <h1 class="h4 text-gray-900 mb-4">Diplomazia & commercio</h1>
                                        </div>
                                        <center><img class="elencoImg" src="img/trader.jpg" alt="trader"></center>
                                        <br>
                                        <?php
                                        if ($admin) {
                                            ?>
                                            <center><h5>Relazioni comunità</h5></center>

                                            <?php
                                            if (!$_POST['onAct']) {
                                                ?>

                                                <form class="user" action="relations.php" method="post">

                                                    <div class="input-group">
                                                        <select name="ID_c1" class="form-control">
                                                            <option value="noSelect" selected>Comunità 1</option>
                                                            <?php
                                                            $sqlturn = "SELECT communities.ID as ID_community, communityName, users.username FROM communities "
                                                                    . "INNER JOIN users ON users.ID = communities.ID_player "
                                                                    . "where ID_turn = $ID_turn ORDER BY communityName ASC";

                                                            if ($ris = $conn->query($sqlturn)) {
                                                                while ($row = $ris->fetch_assoc()) {
                                                                    $ID_community = $row["ID_community"];
                                                                    $communityName = $row["communityName"];
                                                                    $username = ($row["username"] == 'neutral') ? 'Neutrale' : $row["username"];

                                                                    ?> 
                                                                    <option value="<?php echo $ID_community ?>">  <?php echo $communityName ?> (<?php echo $username ?>) </option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                        <select name="ID_c2" class="form-control">
                                                            <option value="noSelect" selected>Comunità 2</option>
                                                            <?php
                                                            $sqlturn = "SELECT communities.ID as ID_community, communityName, users.username FROM communities "
                                                                    . "INNER JOIN users ON users.ID = communities.ID_player "
                                                                    . "where ID_turn = $ID_turn ORDER BY communityName ASC";

                                                            if ($ris = $conn->query($sqlturn)) {
                                                                while ($row = $ris->fetch_assoc()) {
                                                                    $ID_community = $row["ID_community"];
                                                                    $communityName = $row["communityName"];
                                                                    $username = ($row["username"] == 'neutral') ? 'Neutrale' : $row["username"];
                                                                    ?> 
                                                                    <option value="<?php echo $ID_community ?>">  <?php echo $communityName ?> (<?php echo $username ?>) </option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                        <br> <br> 
                                                        <input type="hidden" name="onAct" value="1">
                                                        <input class="btn btn-black btn-user btn-block" type="submit" value="Modifica relazioni">
                                                    </div>

                                                    <br>
                                                </form>

                                                <?php
                                            } else {
                                                ?>
                                                <p><?php echo $communityName1; ?> e <?php echo $communityName2; ?> sono al momento <?php echo $italian; ?></p>
                                                <form class="user" action="relations.php" method="post">
                                                    <div class="input-group">
                                                        <select name="relationship" class="form-control">
                                                            <option value="10">In guerra! (10)</option>
                                                            <option value="30">Relazioni astiose (30)</option>
                                                            <option value="50" selected>Relazioni neutre (50)</option>
                                                            <option value="70">Relazioni cordiali (70)</option>
                                                            <option value="90">Alleati! (90)</option>
                                                        </select>
                                                        <br> <br> 
                                                        <input type="hidden" name="communityName1" value="<?php echo $communityName1; ?>">
                                                        <input type="hidden" name="ID_c1" value="<?php echo $ID_c1; ?>">
                                                        <input type="hidden" name="communityName2" value="<?php echo $communityName2; ?>">
                                                        <input type="hidden" name="ID_c2" value="<?php echo $ID_c2; ?>">
                                                        <input type="hidden" name="diplomacy" value="1">
                                                        <input class="btn btn-black btn-user btn-block" type="submit" value="Conferma modifiche"  autofocus="autofocus">
                                                    </div>

                                                </form>
                                                <form class="user" action="relations.php" method="post">
                                                    <input type="hidden" name="onAct" value="0">
                                                    <input class="btn btn-warning btn-user btn-block" type="submit" value="Annulla modifiche">
                                                </form>
                                                <?php
                                            }
                                            ?>
                                            <br>
                                            <?php
                                        } else {

                                            $sql = "SELECT ID as ID_c1, communityName, pCooperation FROM communities where ID_player = $ID_user AND ID_turn = $ID_turn";

                                            if ($ris = $conn->query($sql)) {
                                                while ($row = $ris->fetch_assoc()) {
                                                    $ID_c1 = $row["ID_c1"];
                                                    $communityName = $row["communityName"];
                                                    $pCooperation = $row["pCooperation"];
                                                }
                                            }

                                            if ($pCooperation > 0) {
                                                ?>
                                                <center><h5>Modifica relazioni</h5>


                                                </center>
                                                <?php
                                                $sql = "select max(level) as maxLevel from characters WHERE class = 'cooperative' AND ID_community = $ID_c1 AND ID_turn = $ID_turn ";
                                                if ($ris = $conn->query($sql))
                                                    ;
                                                $row = $ris->fetch_assoc();
                                                if ($row['maxLevel']) {
                                                    $maxLevel = $row['maxLevel'];
                                                    $multiplier = ($maxLevel > 1) ? 1 + $maxLevel / 10 : 1;

                                                    $sql = "SELECT name, level FROM characters WHERE class = 'cooperative' AND ID_community = $ID_c1 AND ID_turn = $ID_turn ";

                                                    if ($ris = $conn->query($sql)) {
                                                        ?>
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-hover table-striped  table-dark father">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="text-align:center; vertical-align:middle;" width="15%">Personaggi cooperativi</th>
                                                                        <th style="text-align:center; vertical-align:middle;" width="5%">Livello</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>

                                                                    <?php
                                                                    while ($row = $ris->fetch_assoc()) {
                                                                        $name = $row["name"];
                                                                        $level = $row["level"];
                                                                        ?>
                                                                        <tr>
                                                                            <td style="vertical-align:middle; text-align:center" scope="row"><?php echo $name; ?></td>
                                                                            <td style="vertical-align:middle; text-align:center" scope="row"><?php echo $level; ?></td>
                                                                        </tr>
                                                                        <?php
                                                                    }
                                                                    ?>


                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <?php
                                                    }
                                                } else {
                                                    $multiplier = 1;
                                                    ?>
                                                    <center>
                                                        <p>Nessun personaggio cooperativo</p>
                                                    </center>
                                                    <?php
                                                }

                                                $diploCapacity = 10 * $multiplier;
                                                ?>

                                                <center>
                                                    <p>Punti cooperazione disponibili: <?php echo $pCooperation; ?></p>
                                                    <p>Capacità diplomatica di: <?php echo floor($diploCapacity); ?> (10 di base * <?php echo $multiplier; ?> dal livello più alto di cooperazione)</p>
                                                    <?php
                                                    if ($changed) {
                                                        ?>
                                                        <button class="btn btn-warning" autofocus="autofocus">Modifica effettuata!</button>
                                                        <br><br>
                                                        <?php
                                                    } elseif ($unchanged) {
                                                        ?>
                                                        <button class="btn btn-warning" autofocus="autofocus">Valore <?php echo $unchanged; ?> raggiunto!</button>
                                                        <br><br>
                                                        <?php
                                                    }
                                                    ?>
                                                </center>

                                                <?php
                                                $sql = " SELECT ID_know, relationship, communityName FROM com_communities "
                                                        . "JOIN turns ON turns.ID = com_communities.ID_turn "
                                                        . "JOIN communities ON communities.ID_turn = turns.ID "
                                                        . "WHERE com_communities.ID_community = $ID_c1 "
                                                        . "AND com_communities.ID_turn = $ID_turn "
                                                        . "AND ID_know = communities.ID";
                                                if ($ris = $conn->query($sql)) {
                                                    ?>
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-hover table-striped table-dark father">
                                                            <thead>
                                                                <tr>
                                                                    <th style="text-align:center; vertical-align:middle;" width="15%">Comunità conosciute</th>
                                                                    <th style="text-align:center; vertical-align:middle;" width="5%">Rapporti</th>
                                                                    <th style="text-align:center; vertical-align:middle;" width="5%">Azioni</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>

                                                                <?php
                                                                while ($row = $ris->fetch_assoc()) {
                                                                    $ID_c2 = $row['ID_know'];
                                                                    $relationship = $row['relationship'];
                                                                    $communityName2 = $row['communityName'];
                                                                    switch ($relationship) {
                                                                        case $relationship < 20;
                                                                            $italian = "in guerra aperta";
                                                                            break;
                                                                        case $relationship >= 20 && $relationship < 40;
                                                                            $italian = "in relazioni astiose";
                                                                            break;
                                                                        case $relationship >= 40 && $relationship < 60;
                                                                            $italian = "neutrali";
                                                                            break;
                                                                        case $relationship >= 60 && $relationship < 80;
                                                                            $italian = " in rapporti cordiali";
                                                                            break;
                                                                        case $relationship >= 20:
                                                                            $italian = " in stretta alleanza";
                                                                            break;
                                                                        default:
                                                                            break;
                                                                    }
                                                                    if ($relationship == 0) {
                                                                        $italian = " in guerra aperta";
                                                                    }
                                                                    
                                                                    if($_POST['diplo']){
                                                                        $ID_com = $_POST['ID_com'];
                                                                            $autofocus = ($ID_com == $ID_c2) ? "autofocus" : "";
                                                                    }
                                                                    
                                                                    ?>
                                                                    <tr>
                                                                        <td style="vertical-align:middle; text-align:center" scope="row"><?php echo $communityName2; ?></td>
                                                                        <td style="vertical-align:middle; text-align:center" scope="row"><?php echo $italian; ?></td>
                                                                        <td scope="row" style="vertical-align:middle; text-align:center">
                                                                            <form class="kt-form" action="relations.php" method="post">
                                                                                <input type="hidden" name="ID_c1" value="<?php echo $ID_c1 ?>">
                                                                                <input type="hidden" name="ID_c2" value="<?php echo $ID_c2 ?>">
                                                                                <input type="hidden" name="diploCapacity" value="<?php echo floor($diploCapacity) ?>">
                                                                                <input type="hidden" name="plus" value="1">
                                                                                <button class="btn btn-black btn-block" <?php echo $autofocus; ?>>Aumenta di <?php echo $diploCapacity; ?></button>
                                                                            </form>
                                                                            <form class="kt-form" action="relations.php" method="post">
                                                                                <input type="hidden" name="ID_c1" value="<?php echo $ID_c1 ?>">
                                                                                <input type="hidden" name="ID_c2" value="<?php echo $ID_c2 ?>">
                                                                                <input type="hidden" name="diploCapacity" value="<?php echo floor($diploCapacity) ?>">
                                                                                <input type="hidden" name="minus" value="1">
                                                                                <button class="btn btn-black btn-block">Diminuisci di <?php echo $diploCapacity; ?></button>
                                                                            </form>
                                                                        </td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                                ?>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                <center><p><b>Punti cooperazione non sufficienti per la diplomazia</b></p></center>

                                                <?php
                                            }
                                        }
                                        ?>
                                        <!--
                                                                                <form class="user" action="relations.php" method="post">
                                                                                    <input class="btn btn-black btn-user btn-block" type="submit" value="Nuovo evento">
                                                                                </form>
                                                                                <form class="user" action="relations.php" method="post">
                                                                                    <input class="btn btn-black btn-user btn-block" type="submit" value="Elenco eventi">
                                                                                </form>
                                                                                <form class="user" action="relations.php" method="post">
                                                                                    <input class="btn btn-black btn-user btn-block" type="submit" value="Nuovo effetto">
                                                                                </form>
                                                                                <hr>
                                        -->

                                    </div>
                                </div>
                            </div>
                        </div>
                   

                </div>

            </div>

        </div>   
        <?php ?>
    </div>

    <?php
    /* ================================ Fine sezione centrale ================================ */
} else {
    header("location: login.php");
    $_SESSION['redirect'] = "relations";
}

$footer = new Footer();
$footer->show("../../");

session_commit();
?>