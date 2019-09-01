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
    if($_POST['ID_community']){
            $ID_community = $_POST['ID_community'];

    $sql = " SELECT ID_player FROM communities WHERE ID = $ID_community AND ID_turn = $ID_turn ";
    $ris = $conn->query($sql);
    $row = $ris->fetch_assoc();
    $ID_player = $row['ID_player'];
    $go = 1; //ancora non usato
    }

} else {
    $ID_community = Building::findCommunityID($ID_user, $ID_turn);
}

/* Add and remove workers from buildings */
if ($_POST['addWorker']) {
    //Population
    $sql = " SELECT population FROM communities WHERE ID = $ID_community ";
    $ris = $conn->query($sql);
    $row = $ris->fetch_assoc();
    $population = $row['population'];


    //Workers
    $sql = " SELECT sum(abandoned) as workers FROM com_build WHERE ID_community = $ID_community AND ID_turn = $ID_turn ";
    $ris = $conn->query($sql);
    $row = $ris->fetch_assoc();
    $workers = $row['workers'];

    if ($workers < $population) {
        $ID_building = $_POST['ID_building'];
        $numBuilding = $_POST['numBuilding'];
        $sql = " UPDATE com_build SET abandoned = abandoned + 1 "
                . "WHERE ID_community = $ID_community AND ID_building = $ID_building AND ID_turn = $ID_turn AND numBuilding = $numBuilding; ";
        $ris = $conn->query($sql);
        Building::add_remove($ID_community, $ID_building, $ID_turn, 1);
    }
}

if ($_POST['removeWorker']) {
    $ID_building = $_POST['ID_building'];
    $numBuilding = $_POST['numBuilding'];
    $sql = " UPDATE com_build SET abandoned = abandoned - 1 "
            . "WHERE ID_community = $ID_community AND ID_building = $ID_building AND ID_turn = $ID_turn AND numBuilding = $numBuilding; ";
    $ris = $conn->query($sql);
    Building::add_remove($ID_community, $ID_building, $ID_turn, -1);
}

/* Upgrade building to next level */
if ($_POST['upgrade']) {
    $oldID_building = $_POST['ID_building'];
    $numBuilding = $_POST['numBuilding'];
    $sql = " SELECT name, level FROM buildings WHERE ID = $oldID_building ";
    $ris = $conn->query($sql);
    $row = $ris->fetch_assoc();
    $name = $row['name'];
    $newLevel = intval($row['level']) + 1;

    $sql = " SELECT ID FROM buildings WHERE name = '$name' AND level = $newLevel ";
    $risOld = $conn->query($sql);
    $rowOld = $risOld->fetch_assoc();
    $newID_building = $rowOld['ID'];

    $sql = " UPDATE com_build SET hidden = 1 "
            . "WHERE ID_community = $ID_community AND ID_building = $oldID_building AND ID_turn = $ID_turn AND numBuilding = $numBuilding; ";
    $ris = $conn->query($sql);

    $sql = " INSERT INTO com_build (ID_community, ID_building, ID_turn, numBuilding, underCostruction, abandoned, hidden) "
            . "VALUES ('$ID_community', $newID_building, $ID_turn, $numBuilding, 0,0, 0) ";
    $ris = $conn->query($sql);
}


if ($ID_user) {
    ?>
    <div>
    <?php
    $navbar = new Start();
    $navbar->go();
    ?>
        <br>
        <?php
        if (!$admin) {   //aaaah, bisogna cambiarlo!!!! Maledizione!

            //Population
            $sql = " SELECT population FROM communities WHERE ID = $ID_community ";
            $ris = $conn->query($sql);
            $row = $ris->fetch_assoc();
            $population = $row['population'];

            //Workers
            $sql = " SELECT sum(abandoned) as workers FROM com_build WHERE ID_community = $ID_community AND ID_turn = $ID_turn ";
            $ris = $conn->query($sql);
            $row = $ris->fetch_assoc();
            $workers = $row['workers'];

            $sql = "SELECT *, com_build.ID as ID_comBuild FROM com_build "
                    . "INNER JOIN communities ON communities.ID = com_build.ID_community "
                    . "INNER JOIN buildings ON buildings.ID = com_build.ID_building "
                    . "WHERE ID_community = $ID_community AND com_build.ID_turn = $ID_turn AND hidden = 0 "
                    . "ORDER BY class;";
            $ris = $conn->query($sql);
            $check = $ris->num_rows;
            if ($check) {
                ?> 
                <div class="container">
                    <table align="center" class="table table-bordered table-hover table-striped table-dark table-responsive-xl">
                        <thead>
                            <tr>
                                <th colspan="5" style="text-align:center; vertical-align:middle;" width="10%"><h3><b>Edifici turno <?php echo $numTurn; ?></b></h3></th>
                        </tr>
                        <tr>
                            <th colspan="5" style="text-align:center; vertical-align:middle;" width="10%"><h5><b>Lavoratori: <?php echo $workers; ?>/<?php echo $population; ?></b></h5></th>
                        </tr>
                        <tr>
                            <th style="text-align:center; vertical-align:middle;" width="35%">Nome</th>
                            <th class="hide" style="text-align:center; vertical-align:middle;" width="50%">Descrizione</th> 
                            <th style="text-align:center; vertical-align:middle;" width="10%">Effetti</th> 
                            <th class="hide" style="text-align:center; vertical-align:middle;" width="5%">Lavoratori</th> 
                            <th style="text-align:center; vertical-align:middle;" width="10%">Azioni</th> 
                        </tr>
                        </thead>
                        <tbody>
            <?php
            $num = 1;
            while ($row = $ris->fetch_assoc()) {

                $ID_com = $row['ID_comBuild'];
                $ID_build = $row['ID_build'];
                $underCostruction = $row['underCostruction'];
                $abandoned = $row['abandoned'];
                $numBuilding = $row['numBuilding'];

                $ID_building = $row['ID'];
                $name = $row['name'];
                //Useful variables for check possible upgrades in "buildings built" table 
                $italianLevel = ($row["level"] == '0') ? "Livello unico" : "Liv. " . intval($row['level']);
                $level = $row["level"];
                $level = intval(intval($level));
                $upgradeCheck = $level + 1;

                $description = $row['description'];
                $food = ($row['food']) ? $row['food'] : " - ";
                $tool = ($row['tool']) ? $row['tool'] : " - ";
                $weapon = ($row['weapon']) ? $row['weapon'] : " - ";
                $underCostruction = $row['underCostruction'];
                $description = $row['description'];
                $popMax = $row['popMax'];
                $color1 = "btn-black";
                $color2 = "btn-black";
                $disableAdd = "";
                $disableRemove = "";
                if ($abandoned >= $popMax) {
                    $disableAdd = "disabled";
                    $color1 = "btn-red";
                }

                if ($abandoned == 0) {
                    $disableRemove = "disabled";
                    $color2 = "btn-red";
                }

                //Check for possible upgrades
                $sqlUpgrade = " SELECT * FROM buildings WHERE name = '$name' AND level = $upgradeCheck; ";
                $risUpgrade = $conn->query($sqlUpgrade);
                $check = $risUpgrade->num_rows;
                if ($check) {
                    $upgrade = 1;
                } else
                    $upgrade = 0;

                $num++;

                $effects = Building::effects($ID_com);
                $autofocus = "";
                if ($_POST['numRow']) {
                    $IDcheck = $_POST['numRow'];
                    $autofocus = ($IDcheck == $num) ? "autofocus" : "";
                }
                ?>

                                <tr>
                                    <td style="text-align:center; vertical-align:middle;"><?php echo $name; ?><br>Livello <?php echo $italianLevel; ?></td>
                                    <td class="hide" style="text-align:justify; vertical-align:middle;"><?php echo $description; ?></td>
                                    <td style="text-align:justify; vertical-align:middle;"><?php echo $effects; ?></td>
                                    <td class="hide" style="text-align:center; vertical-align:middle;"><?php echo $abandoned; ?></td>
                                    <td scope="row" style="vertical-align:middle; text-align:center">
                                        <form class="kt-form" action="useBuildings.php" method="post" >
                                            <input type="hidden" name="ID_building" value="<?php echo $ID_building ?>">
                                            <input type="hidden" name="numBuilding" value="<?php echo $numBuilding ?>">
                                            <input type="hidden" name="numRow" value="<?php echo $num ?>">
                                            <input type="hidden" name="addWorker" value="1">
                                            <button class="btn <?php echo $color1; ?> btn-block" <?php echo $disableAdd; ?>>Aggiungi lavoratore</button>
                                        </form>
                                        <form class="kt-form" action="useBuildings.php" method="post">
                                            <input type="hidden" name="ID_building" value="<?php echo $ID_building ?>">
                                            <input type="hidden" name="numBuilding" value="<?php echo $numBuilding ?>">
                                            <input type="hidden" name="numRow" value="<?php echo $num ?>">
                                            <input type="hidden" name="removeWorker" value="1">
                                            <button class="btn <?php echo $color2; ?> btn-block" <?php echo $disableRemove; ?>>Togli lavoratore</button>
                                        </form>
                <?php
                if ($upgrade) {
                    ?>
                                            <form class="kt-form" action="useBuildings.php" method="post">
                                                <input type="hidden" name="ID_building" value="<?php echo $ID_building ?>">
                                                <input type="hidden" name="numBuilding" value="<?php echo $numBuilding ?>">
                                                <input type="hidden" name="abandoned" value="<?php echo $abandoned ?>">
                                                <input type="hidden" name="numRow" value="<?php echo $num ?>">
                                                <input type="hidden" name="upgrade" value="1">
                                                <button class="btn btn-black btn-block">Upgrade</button>
                                            </form>
                    <?php
                }
                ?>

                                    </td>
                                </tr>
                <?php
            }
            ?>
                        </tbody>
                    </table>
                </div>
            <?php
        } else {


            /* ================================ Sezione centrale ================================ */
            ?>



                <div class="container">
                    <table align="center" class="table-bordered table-hover table-striped table-dark table-responsive-xl">
                        <thead>
                            <tr>
                                <th style="text-align:center; vertical-align:middle;"><h3 style="color:red">Nessun edificio presente</h3></th>
                        </tr>
                        </thead>
                    </table>
                </div>
                <br>
            <?php
        }
        ?>
            <div class="container">
                <table align="center" class="table table-bordered table-hover table-striped table-dark table-responsive-xl">
                    <thead>
                        <tr>
                            <th colspan="7" style="text-align:center; vertical-align:middle;" width="10%"><h3><b>Nuovo edificio</b></h3></th>
                    </tr>
                    <tr>
                        <th colspan="7" style="text-align:center; vertical-align:middle;" width="10%">
                    <form class="user" action="listBuildings.php" method="post">
                        <input class="btn btn-black btn-user btn-block" type="submit" value="Visualizza elenco completo">
                    </form>
                    </th>
                    </tr>
                    <tr>
                        <th rowspan="2" style="text-align:center; vertical-align:middle;" width="35%">Nome</th>
                        <th class="hide" rowspan="2" style="text-align:center; vertical-align:middle;" width="45%">Descrizione</th> 
                        <th class="hide" colspan="3" style="text-align:center; vertical-align:middle;" width="33%">Risorse necessarie</th> 
                        <th rowspan="2" style="text-align:center; vertical-align:middle;" width="10%">Turni</th> 
                        <th rowspan="2" style="text-align:center; vertical-align:middle;" width="10%">Azioni</th> 
                    </tr>
                    <tr>
                        <th class="hide" style="text-align:center; vertical-align:middle;" width="11%">Cibo</th>
                        <th class="hide" style="text-align:center; vertical-align:middle;" width="11%">Utensili</th> 
                        <th class="hide" style="text-align:center; vertical-align:middle;" width="11%">Armi</th> 
                    </tr>
                    </thead>
                    <tbody>

        <?php
        $sql = " SELECT * FROM buildings WHERE "
                . "name NOT IN (SELECT name "
                . "FROM buildings "
                . "LEFT JOIN com_build ON buildings.ID = com_build.ID_building "
                . "WHERE ID_community = $ID_community AND numMax = numBuilding AND ID_turn = $ID_turn) "
                . "AND level BETWEEN 0 AND 1 ORDER BY class DESC";

        $ris = $conn->query($sql);
        while ($row = $ris->fetch_assoc()) {
            $ID = $row['ID_com'];
            $ID_building = $row['ID'];
            $name = $row['name'];
            $level = $row['level'];
            $description = $row['description'];
            $food = ($row['food']) ? $row['food'] : " - ";
            $tool = ($row['tool']) ? $row['tool'] : " - ";
            $weapon = ($row['weapon']) ? $row['weapon'] : " - ";
            $underCostruction = $row['underCostruction'];
            $description = $row['description'];
            $popMax = $row['popMax'];
            //$numBuilding 
            ?>

                            <tr>
                                <td style="text-align:center; vertical-align:middle;"><?php echo $name; ?></td>
                                <td class="hide" style="text-align:justify; vertical-align:middle;"><?php echo $description; ?></td>
                                <td class="hide" style="text-align:center; vertical-align:middle;"><?php echo $food; ?></td>
                                <td class="hide" style="text-align:center; vertical-align:middle;"><?php echo $tool; ?></td>
                                <td class="hide" style="text-align:center; vertical-align:middle;"><?php echo $weapon; ?></td>
                                <td style="text-align:center; vertical-align:middle;"><?php echo $underCostruction; ?></td>
                                <td scope="row" style="vertical-align:middle; text-align:center">
                                    <form class="kt-form" action="useBuildingsRed.php" method="post">
                                        <input type="hidden" name="ID_building" value="<?php echo $ID_building ?>">
                                        <input type="hidden" name="underCostruction" value="<?php echo $underCostruction ?>">
                                        <input type="hidden" name="build" value="1">
                                        <button class="btn btn-black btn-block">Costruisci</button>
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
        /* ================================ Fine sezione centrale ================================ */
    } else {
        ?>
            <div class="container">
                <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">Seleziona comunità</h1>
                </div>
                <form class="user" action="useBuildings.php" method="post">
                    <select name="ID_community" class="form-control">
        <?php
        $sql = " SELECT *, communities.ID as ID_community FROM users "
                . "INNER JOIN communities ON users.ID = communities.ID_player "
                . "WHERE ID_turn = $ID_turn";
        if ($ris = $conn->query($sql)) {
            while ($row = $ris->fetch_assoc()) {
                if ($row['username'] == "neutral") {
                    $class = 'colorSelectNeutral';
                } else
                    $class = "";
                ?> 
                                <option class="<?php echo $class; ?>" value="<?php echo $row['ID_community']; ?>"><?php echo $row['communityName'] . ' (' . $row['username'] . ')'; ?></option>                                                 
                                <?php
                            }
                        }
                        ?>
                    </select>
                    <button type="submit" class="btn btn-black btn-block">Seleziona</button>
                </form>
            </div>
        <?php
    }
    ?>
    </div> 
        <?php
    } else {
        header("location: login.php");
        $_SESSION['redirect'] = "useBuildings";
    }

    $footer = new Footer();
    $footer->show("../../");

    session_commit();
    ?>