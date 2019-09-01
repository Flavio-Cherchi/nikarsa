<?php
include "./class/ConnessioneDB.php";
include "./class/Starter.php";
include "./class/Head.php";
include "./class/Footer.php";
include "./class/Neutral.php";
include "./class/Building.php";

$conn = connessione::start();
$head = new Head();
$head->show();

session_start();
var_dump($_SESSION['redirect']);
//Login
if ($_POST['logging']) {
    $username = strtolower($_POST['loginName']);
    $password = sha1(strtolower($_POST['loginPassord']));

    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";

    if ($ris = $conn->query($sql)) {

        while ($row = $ris->fetch_assoc()) {
            $_SESSION['ID_user'] = $ID_user = $row["ID"];
            $_SESSION['username'] = $username = $row["username"];
            $_SESSION['admin'] = $admin = $row["admin"];
        }
    }
}

if ($_SESSION['username']) {
    $username = $_SESSION['username'];
}

if ($_SESSION['admin']) {
    $admin = $_SESSION['admin'];
}
//Check and redirect to login page
if (!$_SESSION['ID_user']) {
    $_SESSION['error'] = 1;
    header("location: login.php");
} else
    $_SESSION['error'] = NULL;
/* ------------------------- Select actual game ------------------------- */
//Find active campaign and title
$sql = "select ID, title from campaign WHERE status = 1";
if ($ris = $conn->query($sql)) {
    $row = $ris->fetch_assoc();
    $ID_campaign = $row['ID'];
    $title = $row['title'];
}
//Find actual turns
$sql = "select ID, numTurn from turns WHERE ID_campaign = $ID_campaign ORDER BY numTurn DESC limit 1";
if ($ris = $conn->query($sql)) {
    $row = $ris->fetch_assoc();
    $ID_turn = $row['ID'];
    $numTurn = $row['numTurn'];
}

$_SESSION['ID_campaign'] = $ID_campaign;
$_SESSION['ID_turn'] = $ID_turn;
$_SESSION['title'] = $title;
$_SESSION['numTurn'] = $numTurn;

/* ------------------------- End select actual game ------------------------- */

/* ------------------------- New turns ------------------------- */
if ($_POST['newTurn']) {
    $newTurn = $numTurn + 1;
    $sql = " INSERT INTO turns (ID_campaign, numTurn) VALUES ('$ID_campaign', $newTurn) ";
    $ris = $conn->query($sql);


    $sql = " SELECT MAX(ID) AS ID FROM turns ";
    $ris = $conn->query($sql);
    $row = $ris->fetch_assoc();
    $ID_turnNuovo = $row['ID'];

    //Duplicate communities for new turns
    $sqlturn = "SELECT * FROM communities WHERE communities.ID_turn = $ID_turn AND ID_campaign = $ID_campaign;";

    if ($ris = $conn->query($sqlturn)) {

        while ($row = $ris->fetch_assoc()) {

            $ID_old = $row['ID'];
            $ID_player = $row['ID_player'];
            $communityName = $row['communityName'];
            $img = $row['img'];
            $neutral = $row['neutral'];
            $ration = $row['ration'];
            /* ------ population iniziale------ */
            $populationOld = $row['population'];
            /* ------ Punti esperienza iniziali------ */
            $pViolenceOld = $row['pViolence'];
            $pCooperationOld = $row['pCooperation'];
            $pAutarchyOld = $row['pAutarchy'];


            /* ------ Risorse iniziali ------ */
            $foodOld = $row['food'];
            $toolOld = $row['tool'];
            $drugOld = $row['drug'];
            $weaponOld = $row['weapon'];
            $hygieneOld = $row['hygiene'];

            /* ------ Personaggi ------ */
            $sqlChar = " SELECT * FROM characters WHERE ID_community = $ID_old AND ID_turn = $ID_turn";
            if ($risChar = $conn->query($sqlChar)) {

                $num = 1;

                while ($rowChar = $risChar->fetch_assoc()) {
                    $level = $rowChar['level'];
                    $class = $rowChar['class'];

                    if ($num == 1) {
                        $pViolenceNew1 = ($class == "violent") ? $level : 0;
                        $pCooperationNew1 = ($class == "cooperative") ? $level : 0;
                        $pAutarchyNew1 = ($class == "autarkic") ? $level : 0;
                    } elseif ($num == 2) {
                        $pViolenceNew2 = ($class == "violent") ? $level : 0;
                        $pCooperationNew2 = ($class == "cooperative") ? $level : 0;
                        $pAutarchyNew2 = ($class == "autarkic") ? $level : 0;
                    } elseif ($num == 3) {
                        $pViolenceNew3 = ($class == "violent") ? $level : 0;
                        $pCooperationNew3 = ($class == "cooperative") ? $level : 0;
                        $pAutarchyNew3 = ($class == "autarkic") ? $level : 0;
                    }
                    $num++;
                }
            }
            $populationNew = $populationOld + 1;
            $foodNew = ($ration == "full") ? $foodOld - ($populationOld + 3) * 2 : $foodOld - ($populationOld + 3);
            $foodNew = ($foodNew < 0) ? 0 : $foodNew;
            $toolNew = $toolOld;
            $drugNew = $drugOld;
            $weaponNew = $weaponOld;
            $hygieneNew = $hygieneOld;

            $pViolenceNew = $pViolenceNew1 + $pViolenceNew2 + $pViolenceNew3 + $pViolenceOld;
            $pCooperationNew = $pCooperationNew1 + $pCooperationNew2 + $pCooperationNew3 + $pCooperationOld;
            $pAutarchyNew = $pAutarchyNew1 + $pAutarchyNew2 + $pAutarchyNew3 + $pAutarchyOld;

            $msg = " <br> resoconto: <br>Popolazione iniziale = $populationOld "
                    . "<br>Popolazione attuale = $populationNew "
                    . "<br>Punti violenza iniziali = $pViolenceOld"
                    . "<br>Punti violenza attuali = $pViolenceNew"
                    . "<br>Punti cooperazione iniziali = $pCooperationOld"
                    . "<br>Punti cooperazione attuali = $pCooperationNew"
                    . "<br>Punti autarchia iniziali = $pAutarchyOld"
                    . "<br>Punti autarchia attuali = $pAutarchyNew"
                    . "<br>Cibo iniziale = $foodOld"
                    . "<br>Cibo attuale = $foodNew"
            ;


            $sqlcommunities .= "INSERT INTO communities"
                    . "(ID_player, ID_campaign, communityName, img, population, pViolenceStart, pViolence, pCooperationStart, pCooperation, pAutarchyStart, pAutarchy, "
                    . "foodStart, food, toolStart, tool, drugStart, drug, weaponStart, weapon, hygiene, ration, ID_turn, ID_old, neutral) "
                    . "VALUES ($ID_player, $ID_campaign, '$communityName', '$img', $populationNew,$pViolenceNew,$pViolenceNew,$pCooperationNew,$pCooperationNew,$pAutarchyNew,$pAutarchyNew,"
                    . " $foodNew,$foodNew,$toolNew,$toolNew,$drugNew,$drugNew,$weaponNew,$weaponNew,$hygieneNew, '$ration', $ID_turnNuovo, $ID_old, $neutral);";
        }



        if ($conn->multi_query($sqlcommunities) === TRUE) {
            //other variables if it needed;
        } else {
            echo "<br>Errore in fase di inserimento (comunità)" . $mysqli->error;
        }
    }

    $conn2 = connessione::start();

    //Duplicate characters for new turns

    $sqlcharacters1 = "SELECT * FROM `characters` WHERE `ID_turn` = $ID_turn ";

    if ($ris2 = $conn2->query($sqlcharacters1)) {

        while ($row = $ris2->fetch_assoc()) {
            $ID_old = $row['ID_community'];
            $name = $row['name'];
            $description = $row['description'];
            /* ------ Starting level------ */
            $level = $row['level'];
            $subLevel = $row['subLevel'];
            /* ------ character's categories------ */
            $class = $row['class'];
            /* ------ Img ------ */
            $img = $row['img'];

            /* ------ Values that change every turns ------ */
            if ($subLevel == 6) {
                $level++;
                $subLevel = 0;
            }



            $subLevel++;

            $sqlcharacters2 = " SELECT `ID`, ID_old FROM `communities` WHERE `ID_old` = $ID_old; ";

            if ($ris = $conn2->query($sqlcharacters2)) {
                $row2 = $ris->fetch_assoc();
                $ID_new = $row2['ID'];

                $name = str_replace("'", "\\'", $name);
                $description = str_replace("'", "\\'", $description);
                $sqlcharactersUp .= " INSERT INTO `characters`"
                        . "(`name`, `description`, `level`, `subLevel`, `class`, "
                        . "`ID_community`, `ID_turn`, `img`) "
                        . "VALUES ('$name','$description',$level,$subLevel,'$class',"
                        . "$ID_new,$ID_turnNuovo,'$img'); ";
            }
        }
        if ($conn2->multi_query($sqlcharactersUp) === TRUE) {
            //other variables if it needed;
            //echo "ora funziona (personaggi)";
        } else {
            echo "<br>Errore in fase di inserimento (personaggi)" . $mysqli->error;
        }
    } else {
        echo "<br>Errore generale, contattare l'amministratore";
    }

    //Duplicate communities relations for new turns AKA into the darkness!
    $conn3 = connessione::start();

    $sqlFind = " SELECT ID as ID_new1, ID_old FROM communities WHERE ID_turn = $ID_turnNuovo; ";
    if ($risFind = $conn3->query($sqlFind)) {

        while ($rowFind = $risFind->fetch_assoc()) {
            $ID_new1 = $rowFind['ID_new1'];
            $ID_old = $rowFind['ID_old'];

            $sqlFind2 = " SELECT ID_know, relationship FROM com_communities WHERE ID_community = $ID_old AND ID_turn = $ID_turn; ";
            if ($risFind2 = $conn3->query($sqlFind2)) {

                while ($rowFind2 = $risFind2->fetch_assoc()) {
                    $ID_know = $rowFind2['ID_know'];
                    $relationship = $rowFind2['relationship'];
                    $relationship = ($relationship < 1) ? 0 : $relationship = $relationship - 5;
                    if ($relationship > 100) {
                        $relationship = 100;
                    }
                    $sqlFind3 = " SELECT ID as ID_new2 FROM communities WHERE ID_old = $ID_know AND ID_turn = $ID_turnNuovo; ";
                    if ($risFind3 = $conn3->query($sqlFind3)) {

                        while ($rowFind3 = $risFind3->fetch_assoc()) {
                            $ID_new2 = $rowFind3['ID_new2'];

                            $sqlCom2 .= " INSERT INTO `com_communities`"
                                    . "(`ID_community`, `ID_know`, `ID_turn`, `relationship`) "
                                    . "VALUES ('$ID_new1','$ID_new2',$ID_turnNuovo,$relationship); ";
                        }
                    } else
                        echo "no results";
                }
            } else
                echo "no results";
        }
    }
    $ris = $conn3->multi_query($sqlCom2);

    
    /* Update buildings */
    $conn4 = connessione::start();
    
    $sql = "SELECT ID, ID_old FROM communities WHERE ID_turn = $ID_turnNuovo AND ID_campaign = $ID_campaign;";
    //var_dump($conn4->query($sql));
    if ($ris = $conn4->query($sql)) {

        while ($row = $ris->fetch_assoc()) {

            $ID_community = $row['ID'];
            $IDold_Community = $row['ID_old'];

            $sql = "SELECT * FROM com_build WHERE ID_community = $IDold_Community AND ID_turn = $ID_turn;";
            if ($risSelect = $conn4->query($sql)) {

                while ($rowSelect = $risSelect->fetch_assoc()) {

                    $ID_building = $rowSelect['ID_building'];
                    $numBuilding = $rowSelect['numBuilding'];
                    $underCostruction = $rowSelect['underCostruction'];
                    $abandoned = $rowSelect['abandoned'];
                    $hidden = $rowSelect['hidden'];

                    //Buildings effects
                    if(!$hidden){

                    Building::newTurn($ID_community, $ID_building, $abandoned, $ID_turnNuovo);    
                    }
                   
                    $sqlUpBuilding .= " INSERT INTO com_build (ID_community, ID_building, ID_turn, numBuilding, underCostruction, abandoned, hidden) "
                            . "VALUES ('$ID_community', $ID_building, $ID_turnNuovo, $numBuilding, $underCostruction, $abandoned, $hidden); ";
                    
                }
            }
        }
        $ris = $conn4->multi_query($sqlUpBuilding);
    }
}
/* ------------------------- End new turns ------------------------- */


/* ------------------------- New community and player ------------------------- */
if ($_POST['createCommunity']) {

    if ($_POST['neutral']) {
        $ID_player = 2;
        $neutral = 1;
        $population = 0;
    } else {
        $ID_player = $_POST['ID_player'];
        $neutral = 0;
        $population = 3;
    }

    $communityName = $_POST['communityName'];
    $communityName = str_replace("'", "\\'", $communityName);
    $sql = " UPDATE `users` SET active = 1 WHERE ID = $ID_player; ";
    $ris = $conn->query($sql);

    $sql = " INSERT INTO communities (ID_player, ID_campaign, communityName, img, population, pViolenceStart, pViolence, pCooperationStart, pCooperation, pAutarchyStart, pAutarchy, "
            . "foodStart, food, toolStart, tool, drugStart, drug, weaponStart, weapon, hygiene, ration, ID_turn, ID_old, neutral) "
            . "VALUES ('$ID_player', '$ID_campaign', '$communityName', 'img/communities/zombieKiller.jpg', $population, 1, 1, 1, 1, 1, 1, 10, 10, 10, 10, 10, 10, 10, 10, 50, 'full', $ID_turn, 0, $neutral); ";
    $ris = $conn->query($sql);
}

if ($_POST['neutral']) {
    //Automatic create characters for neutrals communities

    for ($index = 0; $index < 3; $index++) {
        $classes = ["violent", "cooperative", "autarkic"];
        $sel = rand(0, 2);
        $class = $classes[$sel];

        $sql = " SELECT ID, ID_turn FROM communities ORDER BY ID DESC LIMIT 1; ";
        $ris = $conn->query($sql);
        $row = $ris->fetch_assoc();
        $ID_communityNeutral = $row['ID'];
        $ID_turn = $row['ID_turn'];

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
        $name = $first . " " . $second;
        $description = "personaggio neutrale";

        $sql = " SELECT url FROM images WHERE tag = 'characters' AND sex = '$sex' ORDER BY RAND ( ) LIMIT 1 ";
        $ris = $conn->query($sql);
        $row = $ris->fetch_assoc();
        $url = $row['url'];

        $name = str_replace("'", "\\'", $name);
        $description = str_replace("'", "\\'", $description);
        $sql = " INSERT INTO characters "
                . "(name, description, level, subLevel, class , ID_community, ID_turn, img) "
                . "VALUES ('$name', '$description', 1, 1, '$class', $ID_communityNeutral, $ID_turn, '$url')";
        $ris = $conn->query($sql);
    }
}





/* ------------------------- End new community and player ------------------------- */


/* ================================ Sezione centrale ================================ */
if ($admin) {
    if ($_SESSION['redirect']) {
        $page = $_SESSION['redirect'];
        $_SESSION['redirect'] = 0;
        header("location: " . $page . ".php");
    } else {
        header("location: adminPanel.php");
    }
    ?>

    <div class="ischidados">

    <?php
    $navbar = new Start();
    $navbar->go();
    ?>

        <br>

        <div class="container">

            <!-- Outer Row -->
            <div class="row justify-content-center">

                <div class="col-xl-10 col-lg-12 col-md-9">

                    <div class="o-hidden border-0 my-5">
                        <div class="card-body p-0">
                            <div class="row">

    <?php
    if (!$admin) {
        $title1 = "Leggi gli eventi";
        $url1 = "generatedEvents.php";
        $value1 = 0;
        $title2 = "Aggiorna la tua comunità";
        $url2 = "#";
        $title3 = "Comincia i viaggi";
        $url3 = "#";
    } else {
        $title1 = "Leggi gli eventi";
        $url1 = "generatedEvents.php";
        $value1 = 1;
        $title2 = "Modifica le comunità";
        $url2 = "createEffect.php";
        $title3 = "Elenco viaggi";
        $url3 = "#";
    }
    ?>
                                <div class="col-lg-12">
                                    <div class="p-5">
                                        <div class="text-center">
                                            <h1 class="h4 text-gray-900 mb-4" style="color:white">Ciao <?php echo $username; ?>!</h1>
                                        </div>
                                        <form class="user" action="adminPanel.php" method="post">
                                            <input class="btn btn-black btn-user btn-block" type="submit" value="Pannello admin">
                                            <br>
                                        </form>
                                        <form class="user" action="<?php echo $url1; ?>" method="post">
                                            <input type="hidden" name="generatedEvents" value="<?php echo $value1; ?>">
                                            <input class="btn btn-black btn-user btn-block" type="submit" value="<?php echo $title1; ?>"  disabled="true">
                                            <br>
                                        </form>
                                        <form class="user" action="<?php echo $url2; ?>" method="post">
                                            <input type="hidden" name="createCommunity" value="1">
                                            <input class="btn btn-black btn-user btn-block" type="submit" value="<?php echo $title2; ?>" >
                                            <br>
                                        </form>
                                        <form class="user" action="<?php echo $url3; ?>" method="post">

                                            <input type="hidden" name="createCommunity" value="1">
                                            <input class="btn btn-black btn-user btn-block" type="submit" value="<?php echo $title3; ?>"  disabled="true">
                                            <br>
                                        </form>
                                    </div>
                                </div>

    <?php ?>

                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>


    </div>


    <?php
} else {
    if ($_SESSION['redirect']) {
        $page = $_SESSION['redirect'];
        $_SESSION['redirect'] = 0;
        header("location: " . $page . ".php");
    } else {
        header("location: community.php");
    }
}
/* ================================ Fine sezione centrale ================================ */

$footer = new Footer();
$footer->show("../../");

session_commit();
?>