<?php
include "./class/ConnessioneDB.php";
include "./class/Starter.php";
include "./class/Head.php";
include "./class/Fight.php";
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

if ($_POST['men']) {

    if ($_POST['ID_c1']) {

        if (($_POST['ID_c1'] != "noSelect") && ($_POST['ID_c2'] != "noSelect")) {
            if ($_POST['ID_c1'] != $_POST['ID_c2']) {
                $ID_c1 = $_POST['ID_c1'];
                $date1 = Fight::fighterHuman($ID_c1);
                $strength1 = $date1[0];
                $numViolent1 = $date1[1];
                $levViolent1 = $date1[2];
                $population1 = $date1[3];
                $pVMax1 = $date1[4];
                $clean1 = $date1[6];
                $title1 = $date1[7];

                $ID_c2 = $_POST['ID_c2'];
                $date2 = Fight::fighterHuman($ID_c2);
                $strength2 = $date2[0];
                $numViolent2 = $date2[1];
                $levViolent2 = $date2[2];
                $population2 = $date2[3];
                $pVMax2 = $date2[4];
                $hungry2 = $date2[5];
                $clean2 = $date2[6];
                $title2 = $date2[7];

                if ($_POST['modifyPotence']) {
                    $modifyPotence = $_POST['modifyPotence'];
                }

                if ($modifyPotence) {
                    $next = 2;
                } else {
                    $next = 1;
                    if ($_POST['modificationDone']) {
                        $numChar1 = $_POST['numChar1'];
                        $level1 = $_POST['level1'];
                        $population1 = $_POST['population1'];
                        $pViolence1 = $_POST['pViolence1'];
                        $food1 = $_POST['food1'];
                        $clean1 = $_POST['clean1'];

                        $numChar2 = $_POST['numChar2'];
                        $level2 = $_POST['level2'];
                        $population2 = $_POST['population2'];
                        $pViolence2 = $_POST['pViolence2'];
                        $food2 = $_POST['food2'];
                        $clean2 = $_POST['clean2'];

                        $strenght1 = Fight::fighterHumanMod($ID_c1, $numChar1, $level1, $population1, $pViolence1, $food1, $clean1, $title1);
                        $strenght2 = Fight::fighterHumanMod($ID_c2, $numChar2, $level2, $population2, $pViolence2, $food2, $clean2, $title2);

                        $res = Fight::realFightMod($strenght1, $strenght2, 0);

                        $esito = $res[0];
                        $score1 = $res[1];
                        $score2 = $res[2];
                        $died1 = $res[5];
                        $died2 = $res[6];
                    } else {
                        $res = Fight::realFight($ID_c1, $ID_c2, 0);

                        $esito = $res[0];
                        $score1 = $res[1];
                        $score2 = $res[2];
                        $died1 = $res[5];
                        $died2 = $res[6];
                    }
                }
            } else {
                $response = 1;
                $msg = "Le relazioni di una comunità con sè stessa? Davvero?";
            }
        } else {
            $response = 1;
            $msg = "Prego fare una selezione plausibile";
            $_POST['men'] = 1;
        }
    }
}

if ($_POST['zombie']) {

    if ($_POST['ID_c1']) {

        if ($_POST['ID_c1'] != "noSelect") {

            $ID_c1 = $_POST['ID_c1'];
            $date1 = Fight::fighterHuman($ID_c1);
            $strength1 = $date1[0];
            $numViolent1 = $date1[1];
            $levViolent1 = $date1[2];
            $population1 = $date1[3];
            $pVMax1 = $date1[4];
            $clean1 = $date1[6];
            $title1 = $date1[7];

            $population2 = $_POST['population2'];
            $title2 = "Zombie";

            if ($_POST['modifyPotence']) {
                $modifyPotence = $_POST['modifyPotence'];
            }
            if ($modifyPotence) {
                $next = 2;
            } else {
                $next = 1;
                if ($_POST['modificationDone']) {
                    $numChar1 = $_POST['numChar1'];
                    $level1 = $_POST['level1'];
                    $population1 = $_POST['population1'];
                    $pViolence = $_POST['pViolence'];
                    $food1 = $_POST['food1'];
                    $clean1 = $_POST['clean1'];

                    $population2 = $_POST['population2'];

                    $strenght1 = Fight::fighterHumanMod($ID_c1, $numChar1, $level1, $population1, $pViolence1, $food1, $clean1, $title1);
                    $strenght2 = Fight::fighterZombie($population2);

                    $res = Fight::realFightMod($strenght1, $strenght2, 1);

                    $esito = $res[0];
                    $score1 = $res[1];
                    $score2 = $res[2];
                    $died1 = $res[5];
                    $died2 = $res[6];
                } else {
                    $res = Fight::realFight($ID_c1, $population2, 1);
                    $esito = $res[0];
                    $score1 = $res[1];
                    $score2 = $res[2];
                    $died1 = $res[5];
                    $died2 = $res[6];
                }
            }
        } else {
            $response = 1;
            $msg = "Prego fare una selezione plausibile";
            $_POST['zombie'] = 1;
        }
    }
}



if ($admin) {

    /* ================================ Sezione centrale ================================ */
    ?>
    <div>

        <?php
        $navbar = new Start();
        $navbar->go();
        ?>
        <br>
        <?php //var_dump($_POST) ?>
        <div class="container" border-radius: 5%;">
             <!-- Outer Row -->
             <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="">
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
                                    <div class="text-center">

                                    </div>

                                    <br>
                                    <?php
                                    if (!$_POST['zombie'] && !$_POST['men']) {
                                        ?>
                                        <div class="row">
                                            <div class="col-lg-1"></div>
                                            <div class="col-lg-5">
                                                <center>
                                                    <form class="user" action="battle.php" method="post">
                                                        <input type="hidden" name="men" value="1">
                                                        <input class="btn btn-black btn-user btn-block" type="submit" value="Tra esseri umani">
                                                    </form>
                                                </center>
                                            </div>
                                            <div class="col-lg-5">
                                                <center>
                                                    <form class="user" action="battle.php" method="post">
                                                        <input type="hidden" name="zombie" value="1">
                                                        <input class="btn btn-black btn-user btn-block" type="submit" value="Contro zombie">
                                                    </form>
                                                </center>
                                            </div>
                                        </div>
                                        <?php
                                    } elseif ($_POST['men']) {

                                        if (!$next) {
                                            ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <form class="user" action="battle.php" method="post">
                                                        <div class="row">
                                                            <div class="col-lg-6">
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
                                                            </div>
                                                            <br><br>
                                                            <div class="col-lg-6">
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
                                                            </div>
                                                        </div>
                                                        <center>
                                                            Modifiche <input type="checkbox" name="modifyPotence" value="1"> 
                                                        </center>
                                                        <input type="hidden" name="men" value="1">
                                                        <input class="btn btn-black btn-user btn-block" type="submit" value="Prosegui">
                                                    </form>
                                                </div>
                                                <div class="col-lg-12">
                                                    <form class="user" action="battle.php" method="post">
                                                        <div class="col-lg-">
                                                            <input type="hidden" name="men" value="0">
                                                            <input type="hidden" name="zombie" value="0">
                                                            <input class="btn btn-black btn-user btn-block" type="submit" value="Torna indietro">
                                                        </div>

                                                    </form>
                                                </div>
                                            </div>
                                            <?php
                                        } else {

                                            if ($next == 2) {
                                                ?>
                                                <div class="row">
                                                    <div class="col-lg-2"></div>
                                                    <div class="col-lg-8">
                                                        <form class="user" action="battle.php" method="post">
                                                            <div class="row">
                                                                <div class="col-lg-6">
                                                                    <center><h5><?php echo $title1; ?></h5>
                                                                        <div class="form-group">
                                                                            <input type="number" name="numChar1" value="<?php echo $numViolent1; ?>" min="0"><br>Personaggi violenti
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <input type="number" name="level1" value="<?php echo $levViolent1; ?>" min="0"><br>Somma dei livelli dei personaggi
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <input type="number" name="population1" value="<?php echo $population1; ?>" min="0"><br>Popolazione armata
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <input type="number" name="pViolence1" value="0" min="0"><br>Punti violenza impiegati
                                                                        </div>
                                                                        <?php
                                                                        switch ($hungry1) {
                                                                            case 1:
                                                                                $check1_1 = "selected";
                                                                                break;
                                                                            case 2:
                                                                                $check1_2 = "selected";
                                                                                break;
                                                                            case 3:
                                                                                $check1_3 = "selected";
                                                                                break;

                                                                            default:
                                                                                break;
                                                                        }

                                                                        switch ($clean1) {
                                                                            case 1:
                                                                                $check1_4 = "selected";
                                                                                break;
                                                                            case 2:
                                                                                $check1_5 = "selected";
                                                                                break;
                                                                            case 3:
                                                                                $check1_6 = "selected";
                                                                                break;
                                                                            case 4:
                                                                                $check1_7 = "selected";
                                                                                break;

                                                                            default:
                                                                                break;
                                                                        }
                                                                        ?>
                                                                        <select name="food1" class="form-group form-control-sm">
                                                                            <option value="1" <?php echo $check1_1; ?>>Denutriti</option>
                                                                            <option value="2" <?php echo $check1_2; ?>>Mezza razione</option>
                                                                            <option value="3" <?php echo $check1_3; ?>>Sazi</option>
                                                                        </select>
                                                                        <br>
                                                                        <select name="clean1" class="form-group form-control-sm">
                                                                            <option value="1" <?php echo $check1_4; ?>>Nessuna igiene</option>
                                                                            <option value="2" <?php echo $check1_5; ?>>Poca igiene</option>
                                                                            <option value="3" <?php echo $check1_6; ?>>Moderata igiene</option>
                                                                            <option value="4" <?php echo $check1_7; ?>>Igiene totale</option>
                                                                        </select>
                                                                    </center>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <center><h5><?php echo $title2; ?></h5>
                                                                        <div class="form-group">
                                                                            <input type="number" name="numChar2" value="<?php echo $numViolent2; ?>" min="0"><br>Personaggi violenti
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <input type="number" name="level2" value="<?php echo $levViolent2; ?>" min="0"><br>Somma dei livelli dei personaggi
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <input type="number" name="population2" value="<?php echo $population2; ?>" min="0"><br>Popolazione armata
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <input type="number" name="pViolence2" value="0" min="0"><br>Punti violenza impiegati
                                                                        </div>
                                                                        <?php
                                                                        switch ($hungry1) {
                                                                            case 1:
                                                                                $check2_1 = "selected";
                                                                                break;
                                                                            case 2:
                                                                                $check2_2 = "selected";
                                                                                break;
                                                                            case 3:
                                                                                $check2_3 = "selected";
                                                                                break;

                                                                            default:
                                                                                break;
                                                                        }

                                                                        switch ($clean2) {
                                                                            case 1:
                                                                                $check2_4 = "selected";
                                                                                break;
                                                                            case 2:
                                                                                $check2_5 = "selected";
                                                                                break;
                                                                            case 3:
                                                                                $check2_6 = "selected";
                                                                                break;
                                                                            case 4:
                                                                                $check2_7 = "selected";
                                                                                break;

                                                                            default:
                                                                                break;
                                                                        }
                                                                        ?>
                                                                        <select name="food2" class="form-group form-control-sm">
                                                                            <option value="1" <?php echo $check2_1; ?>>Denutriti</option>
                                                                            <option value="2" <?php echo $check2_2; ?>>Mezza razione</option>
                                                                            <option value="3" <?php echo $check2_3; ?>>Sazi</option>
                                                                        </select>
                                                                        <br>
                                                                        <select name="clean2" class="form-group form-control-sm">
                                                                            <option value="1" <?php echo $check2_4; ?>>Nessuna igiene</option>
                                                                            <option value="2" <?php echo $check2_5; ?>>Poca igiene</option>
                                                                            <option value="3" <?php echo $check2_6; ?>>Moderata igiene</option>
                                                                            <option value="4" <?php echo $check2_7; ?>>Igiene totale</option>
                                                                        </select>
                                                                    </center>
                                                                </div>
                                                            </div>
                                                            <input type="hidden" name="modificationDone" value="1">
                                                            <input type="hidden" name="ID_c1" value="<?php echo $ID_c1; ?>">
                                                            <input type="hidden" name="ID_c2" value="<?php echo $ID_c2; ?>">
                                                            <input type="hidden" name="men" value="1">
                                                            <input class="btn btn-black btn-user btn-block" type="submit" value="Prosegui">
                                                        </form>
                                                        <form class="user" action="battle.php" method="post">
                                                            <div class="">
                                                                <input type="hidden" name="men" value="0">
                                                                <input type="hidden" name="zombie" value="0">
                                                                <input class="btn btn-black btn-user btn-block" type="submit" value="Torna indietro">
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <?php
                                            } elseif ($next == 1) {
                                                ?>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <table class="table table-bordered table-hover table-striped table-dark">
                                                            <thead>
                                                                <tr>
                                                                    <th colspan="3" style="text-align:center" class="p-3 mb-2 btn-black text-white"><strong>Esito battaglia</strong></th>
                                                                </tr>
                                                                <tr>
                                                                    <th width="5%"></th>
                                                                    <th style="text-align:center; vertical-align:middle;" width="10%"><?php echo $res[3]; ?></th>
                                                                    <th style="text-align:center; vertical-align:middle;" width="10%"><?php echo $res[4]; ?></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td style="text-align:center; vertical-align:middle;"><b>Punteggio</b>
                                                                    </td>
                                                                    <td style="text-align:center; vertical-align:middle;"><?php echo $score1; ?>
                                                                    </td>
                                                                    <td style="text-align:center; vertical-align:middle;"><?php echo $score2; ?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="text-align:center; vertical-align:middle;"><b>Combattenti <br>(senza personaggi)</b>
                                                                    </td>
                                                                    <td style="text-align:center; vertical-align:middle;"><?php echo $population1; ?>
                                                                    </td>
                                                                    <td style="text-align:center; vertical-align:middle;"><?php echo $population2; ?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="text-align:center; vertical-align:middle;"><b>Morti</b>
                                                                    </td>
                                                                    <td style="text-align:center; vertical-align:middle;"><?php echo $died1; ?>
                                                                    </td>
                                                                    <td style="text-align:center; vertical-align:middle;"><?php echo $died2; ?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="3" style="text-align:center; vertical-align:middle;"><?php echo $res[0]; ?>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <script>
                                                        function RefreshWindow()
                                                        {
                                                            window.location.reload(true);
                                                        }
                                                    </script>
                                                    <div class="col-lg-4">
                                                        <form class="user" action="battle.php" method="post">
                                                            <input type="hidden" name="men" value="1">
                                                            <input autofocus="autofocus" class="btn btn-black btn-block"  type="button" value="Ripeti la battaglia" onclick="return RefreshWindow();"/>
                                                        </form>

                                                    </div>
                                                    <div class="col-lg-4">
                                                        <form class="user" action="battle.php" method="post">
                                                            <input type="hidden" name="men" value="1">
                                                            <input class="btn btn-black btn-user btn-block" type="submit" value="Altra battaglia">
                                                        </form>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <form class="user" action="battle.php" method="post">
                                                            <input type="hidden" name="zombie" value="1">
                                                            <input class="btn btn-black btn-user btn-block" type="submit" value="Battaglia contro zombie">
                                                        </form>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                        }
                                    } elseif ($_POST['zombie']) {

                                        if (!$next) {
                                            ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <form class="user" action="battle.php" method="post">
                                                        <div class="row">
                                                            <div class="col-lg-6">
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
                                                            </div>
                                                            <br>
                                                            <div class="col-lg-6">
                                                                <center>
                                                                    <div class = "form-group custom">
                                                                        <input type = "number" name = "population2" value = "0" min = "0"><br>Zombie
                                                                    </div>
                                                                </center>
                                                            </div>
                                                        </div>
                                                        <center>
                                                            Modifiche <input type="checkbox" name="modifyPotence" value="1"> 
                                                        </center>
                                                        <input type="hidden" name="zombie" value="1">
                                                        <input class="btn btn-black btn-user btn-block" type="submit" value="Prosegui">
                                                    </form>
                                                </div>
                                                <div class="col-lg-12">
                                                    <form class="user" action="battle.php" method="post">
                                                        <div class="col-lg-">
                                                            <input type="hidden" name="men" value="0">
                                                            <input type="hidden" name="zombie" value="0">
                                                            <input class="btn btn-black btn-user btn-block" type="submit" value="Torna indietro">
                                                        </div>

                                                    </form>
                                                </div>
                                            </div>
                                            <?php
                                        } else {

                                            if ($next == 2) {
                                                ?>
                                                <div class="row">
                                                    <div class="col-lg-2"></div>
                                                    <div class="col-lg-8">
                                                        <form class="user" action="battle.php" method="post">
                                                            <div class="row">
                                                                <div class="col-lg-1"></div>
                                                                <div class="col-lg-5">
                                                                    <center><h5><?php echo $title1; ?></h5>
                                                                        <div class="form-group">
                                                                            <input type="number" name="numChar1" value="<?php echo $numViolent1; ?>" min="0"><br>Personaggi violenti
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <input type="number" name="level1" value="<?php echo $levViolent1; ?>" min="0"><br>Somma dei livelli dei personaggi
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <input type="number" name="population1" value="<?php echo $population1; ?>" min="0"><br>Popolazione armata
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <input type="number" name="pViolence1" value="0" min="0"><br>Punti violenza impiegati
                                                                        </div>
                                                                        <?php
                                                                        switch ($hungry1) {
                                                                            case 1:
                                                                                $check1_1 = "selected";
                                                                                break;
                                                                            case 2:
                                                                                $check1_2 = "selected";
                                                                                break;
                                                                            case 3:
                                                                                $check1_3 = "selected";
                                                                                break;

                                                                            default:
                                                                                break;
                                                                        }

                                                                        switch ($clean1) {
                                                                            case 1:
                                                                                $check1_4 = "selected";
                                                                                break;
                                                                            case 2:
                                                                                $check1_5 = "selected";
                                                                                break;
                                                                            case 3:
                                                                                $check1_6 = "selected";
                                                                                break;
                                                                            case 4:
                                                                                $check1_7 = "selected";
                                                                                break;

                                                                            default:
                                                                                break;
                                                                        }
                                                                        ?>
                                                                        <select name="food1" class="form-group form-control-sm">
                                                                            <option value="1" <?php echo $check1_1; ?>>Denutriti</option>
                                                                            <option value="2" <?php echo $check1_2; ?>>Mezza razione</option>
                                                                            <option value="3" <?php echo $check1_3; ?>>Sazi</option>
                                                                        </select>
                                                                        <br>
                                                                        <select name="clean1" class="form-group form-control-sm">
                                                                            <option value="1" <?php echo $check1_4; ?>>igiene</option>
                                                                            <option value="2" <?php echo $check1_5; ?>>Poca igiene</option>
                                                                            <option value="3" <?php echo $check1_6; ?>>Media igiene</option>
                                                                            <option value="4" <?php echo $check1_7; ?>>Igiene totale</option>
                                                                        </select>
                                                                    </center>
                                                                </div>
                                                                <div class = "col-lg-5">
                                                                    <center><h5>Orda zombie</h5>
                                                                        <div class = "form-group">
                                                                            <input type = "number" name = "population2" value = "0" min = "0"><br>Zombie
                                                                        </div>
                                                                    </center>
                                                                </div>
                                                            </div>
                                                            <input type="hidden" name="modificationDone" value="1">
                                                            <input type="hidden" name="ID_c1" value="<?php echo $ID_c1; ?>">
                                                            <input type="hidden" name="zombie" value="1">
                                                            <input class="btn btn-black btn-user btn-block" type="submit" value="Prosegui">
                                                        </form>
                                                        <form class="user" action="battle.php" method="post">
                                                            <div class="">
                                                                <input type="hidden" name="zombie" value="0">
                                                                <input type="hidden" name="men" value="0">
                                                                <input class="btn btn-black btn-user btn-block" type="submit" value="Torna indietro">
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <?php
                                            } elseif ($next == 1) {
                                                ?>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <table class="table table-bordered table-hover table-striped table-dark">
                                                            <thead>
                                                                <tr>
                                                                    <th colspan="3" style="text-align:center" class="p-3 mb-2 btn-black text-white"><strong>Esito battaglia</strong></th>
                                                                </tr>
                                                                <tr>
                                                                    <th width="5%"></th>
                                                                    <th style="text-align:center; vertical-align:middle;" width="10%"><?php echo $res[3]; ?></th>
                                                                    <th style="text-align:center; vertical-align:middle;" width="10%">Orda zombie</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td style="text-align:center; vertical-align:middle;"><b>Punteggio</b>
                                                                    </td>
                                                                    <td style="text-align:center; vertical-align:middle;"><?php echo $score1; ?>
                                                                    </td>
                                                                    <td style="text-align:center; vertical-align:middle;"><?php echo $score2; ?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="text-align:center; vertical-align:middle;"><b>Combattenti <br>(senza personaggi)</b>
                                                                    </td>
                                                                    <td style="text-align:center; vertical-align:middle;"><?php echo $population1; ?>
                                                                    </td>
                                                                    <td style="text-align:center; vertical-align:middle;"><?php echo $population2; ?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="text-align:center; vertical-align:middle;"><b>Morti</b>
                                                                    </td>
                                                                    <td style="text-align:center; vertical-align:middle;"><?php echo $died1; ?>
                                                                    </td>
                                                                    <td style="text-align:center; vertical-align:middle;"><?php echo $died2; ?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="3" style="text-align:center; vertical-align:middle;"><?php echo $res[0]; ?>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <script>
                                                        function RefreshWindow()
                                                        {
                                                            window.location.reload(true);
                                                        }
                                                    </script>
                                                    <div class="col-lg-4">
                                                        <form class="user" action="battle.php" method="post">
                                                            <input type="hidden" name="zombie" value="1">
                                                            <input autofocus="autofocus" class="btn btn-black btn-block"  type="button" value="Ripeti la battaglia" onclick="return RefreshWindow();"/>
                                                        </form>

                                                    </div>
                                                    <div class="col-lg-4">
                                                        <form class="user" action="battle.php" method="post">
                                                            <input type="hidden" name="zombie" value="1">
                                                            <input class="btn btn-black btn-user btn-block" type="submit" value="Altra battaglia">
                                                        </form>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <form class="user" action="battle.php" method="post">
                                                            <input type="hidden" name="men" value="1">
                                                            <input class="btn btn-black btn-user btn-block" type="submit" value="Battaglia tra esseri umani">
                                                        </form>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>
                                    <br>
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
    $_SESSION['redirect'] = "outside";
}

$footer = new Footer();
$footer->show("../../");

session_commit();
?>