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


if ($ID_user) {
    if (!$_POST['ID_player']) {
        $ID_player = $ID_user;

        $sql = "SELECT ID as ID_community FROM communities WHERE ID_Player = $ID_player AND ID_turn = $ID_turn ";
        $ris = $conn->query($sql);
        $row = $ris->fetch_assoc();
        $ID_community = $row['ID_community'];
    } else {
        $ID_player = $_POST['ID_player'];
        $ID_community = $_POST['ID_community'];
    }


    if ($ID_player) {

        if ($_POST['changeName']) {

            $newName = $_POST['newName'];
            $sql = " UPDATE communities SET communityName = '$newName' WHERE ID_player = $ID_player ";
            $ris = $conn->query($sql);
        }


        $sql = "SELECT * FROM users WHERE ID = $ID_player";
        if ($ris = $conn->query($sql)) {
            while ($row = $ris->fetch_assoc()) {
                $username = $row["username"];
                ?> 



                <?php
            }
        }


        if ($_POST['image']) {
            $image = $_POST['image'];

            $sql = "SELECT url FROM images WHERE ID = $image";
            if ($ris = $conn->query($sql)) {
                $row = $ris->fetch_assoc();
                $url = $row["url"];
            }
            $sql = " UPDATE communities SET img = '$url' WHERE ID_player = $ID_player ";
            $ris = $conn->query($sql);
        }


        /* ================================ Sezione centrale ================================ */
        ?>

        <!--    <div class="ischidados "> -->
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

            $sql = "SELECT *, communities.ID "
                    . "FROM communities "
                    . "INNER JOIN users on users.ID = communities.ID_player "
                    . "WHERE communities.ID_turn = $ID_selectedTurn "
                    . "AND communities.ID_player = $ID_player "
                    . "AND communities.ID = $ID_community "
                    . "ORDER BY users.ID ";

            if ($ris = $conn->query($sql)) {

                $row = $ris->fetch_assoc();
                $ID_player = $row['ID_player'];
                $communityName = $row['communityName'];
                $img = $row['img'];
                $username = $row['username'];
                $population = $row['population'];
                $pViolence = $row['pViolence'];
                $pCooperation = $row['pCooperation'];
                $pAutarchy = $row['pAutarchy'];
                $food = $row['food'];
                $tool = $row['tool'];
                $drug = $row['drug'];
                $weapon = $row['weapon'];
                $pViolenceStart = $row['pViolenceStart'];
                $pCooperationStart = $row['pCooperationStart'];
                $pAutarchyStart = $row['pAutarchyStart'];
                $foodStart = $row['foodStart'];
                $toolStart = $row['toolStart'];
                $drugStart = $row['drugStart'];
                $weaponStart = $row['weaponStart'];
                $hygiene = $row['hygiene'];
                $ration = $row['ration'];
                $events = $row['events'];

                $foodNew = ($ration == "full") ? $food - ($population + 3) * 2 : $food - ($population + 3);
                $foodNew = ($foodNew < 0) ? 0 : $foodNew;
                $toolNew = $tool;
                $drugNew = $drug;
                $weaponNew = $weapon;
                $hygieneNew = $hygiene;
            }

            $sqltitle = "SELECT numTurn FROM turns WHERE ID = $ID_selectedTurn";
            $ris = $conn->query($sqltitle);
            $row = $ris->fetch_assoc();
            $titlepagina = $row['numTurn'];
            ?>

            <div class="table-responsive">
                <?php
                if ($ID_player == $ID_user) {
                    //For download img
                    $finalName = $communityName;
                    ?>
                    <div class="row">
                        <div class="offset-3"></div>
                        <div class="col-md-3">
                            <input id="preview" class="btn btn-black btn-user btn-block" type="button" value="Crea screenshot">
                        </div>
                        <br><br>
                        <div class="col-md-3">
                            <form class="user" action="userPanel.php" method="post">
                                <input type="hidden" name="communityName" value="<?php echo $communityName; ?>">
                                <input class="btn btn-black btn-user btn-block" type="submit" value="Gestione comunità">
                            </form>
                        </div>
                    </div>



                    <!-- AJAX for screenshot -->
                    <script>
                        $("#preview").on('click', function () {
                            html2canvas($('#screenshot'), {
                                onrendered: function (canvas) {
                                    var img = canvas.toDataURL();
                                    var imgdata = img.replace(/^data:image\/(png|jpg);base64,/, "");
                                    $.ajax({
                                        url: 'communityRed.php',
                                        data: {
                                            imgdata: imgdata
                                        },
                                        type: 'post',
                                    });
                                }
                            })
                            setTimeout(function () {
                                window.location.href = 'communityRed.php';
                            }, 2000);
                        });




                    </script>

                    <?php
                }

                if ($_SESSION['url']) {
                    $url = $_SESSION['url'];
                    $_SESSION['url'] = NULL;
                    $sql = " SELECT * FROM images WHERE url = '$url' ";
                    $ris = $conn->query($sql);
                    $row = $ris->fetch_assoc();
                    $ID_img = $row['ID'];
                    $url = $row['url'];
                    $uploadedBy = $row['uploadedBy'];
                    ?>
                                                                                                                    <!-- <img src="<?php echo $url; ?>" alt="imgPreview" height="42" width="42"> -->
                    <textarea class="form-control form-control-user" id="myInput" name="description" rows="3" >http://www.denai.it/projects/ischidados/<?php echo $url; ?></textarea>
                    <button class="btn btn-black btn-block" onclick="copy()">Copia il testo per BoPitalia</button> 
                    <br>
                    <script>
                        function copy() {
                            var copyText = document.getElementById("myInput");
                            copyText.select();
                            document.execCommand("copy");
                            alert("Testo copiato");
                        }
                    </script>
                    <?php
                }
                ?>

                <div id="screenshot"> 
                    <center>
                        <h1><?php echo $communityName; ?></h12>
                            <h3>(<?php echo $username; ?>)</h3>
                            <br>
                            <img class="imgCommunity" src="<?php echo $img; ?>" alt="imgPlayer">
                            <br>
                            <h3>Popolazione: <?php echo $population; ?></h3>
                    </center>

                    <?php
                    /*
                      if ($events) {
                      $color = "#00FF00";
                      $text = "<b>Eventi conclusi</b>";
                      } else {
                      $color = "#FF0000";
                      $text = "<b>Attenzione: eventi ancora in sospeso</b>";
                      }
                     */
                    $sql = " SELECT * FROM characters WHERE ID_community = $ID_community AND ID_turn = $ID_turn; ";
                    if ($ris = $conn->query($sql)) {

                        $num = 1;
                        while ($row = $ris->fetch_assoc()) {
                            $class = $row['class'];

                            if ($num == 1) {
                                $name1 = $row['name'];
                                $level1 = $row['level'];
                                $img1 = $row['img'];
                                if ($class == "violent") {
                                    $class1 = "Violento";
                                    $pV += (1 * $level1);
                                } elseif ($class == "cooperative") {
                                    $class1 = "Cooperativo";
                                    $pC += (1 * $level1);
                                } elseif ($class == "autarkic") {
                                    $class1 = "Autarchico";
                                    $pA += (1 * $level1);
                                }
                            } elseif ($num == 2) {
                                $name2 = $row['name'];
                                $level2 = $row['level'];
                                $img2 = $row['img'];
                                if ($class == "violent") {
                                    $class2 = "Violento";
                                    $pV += (1 * $level2);
                                } elseif ($class == "cooperative") {
                                    $class2 = "Cooperativo";
                                    $pC += (1 * $level2);
                                } elseif ($class == "autarkic") {
                                    $class2 = "Autarchico";
                                    $pA += (1 * $level2);
                                }
                            } elseif ($num == 3) {
                                $name3 = $row['name'];
                                $level3 = $row['level'];
                                $img3 = $row['img'];
                                if ($class == "violent") {
                                    $class3 = "Violento";
                                    $pV += (1 * $level3);
                                } elseif ($class == "cooperative") {
                                    $class3 = "Cooperativo";
                                    $pC += (1 * $level3);
                                } elseif ($class == "autarkic") {
                                    $class3 = "Autarchico";
                                    $pA += (1 * $level3);
                                }
                            }
                            $num++;
                        }
                    }

                    $pViolenceChange = $pViolence + $pV - $pViolenceStart;
                    $pCooperationChange = $pCooperation + $pC - $pCooperationStart;
                    $pAutarchyChange = $pAutarchy + $pA - $pAutarchyStart;
                    $foodChange = $foodNew - $foodStart;
                    $toolChange = $toolNew - $toolStart;
                    $drugChange = $drugNew - $drugStart;
                    $weaponChange = $weaponNew - $weaponStart;
                    $hygieneChange = $hygieneNew - $hygiene;
                    ?>

                    <br>


                    <br>
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">

                                <?php
                                if ($name3) {
                                    ?>
                                <table class="table table-bordered table-hover table-striped table-dark">
                                    <thead>
                                        <tr>
                                            <th colspan="4" style="text-align:center; vertical-align:middle;"><h3><b>Personaggi principali</b></h3></th>
                                    </tr>
                                    </thead>
                                        <tbody>
                                            <tr>
                                                <td style="text-align:center; vertical-align:middle;"><img class="imgCommunity" src="<?php echo $img1; ?>" alt="character1"></td>
                                                <td style="text-align:center; vertical-align:middle;"><img class="imgCommunity" src="<?php echo $img2; ?>" alt="character2"></td>
                                                <td style="text-align:center; vertical-align:middle;"><img class="imgCommunity" src="<?php echo $img3; ?>" alt="character3"></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align:center; vertical-align:middle;"><?php echo $name1; ?></td>
                                                <td style="text-align:center; vertical-align:middle;"><?php echo $name2; ?></td>
                                                <td style="text-align:center; vertical-align:middle;"><?php echo $name3; ?></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align:center; vertical-align:middle;"><?php echo $class1; ?> livello <?php echo $level1; ?></td>
                                                <td style="text-align:center; vertical-align:middle;"><?php echo $class2; ?> livello <?php echo $level2; ?></td>
                                                <td style="text-align:center; vertical-align:middle;"><?php echo $class3; ?> livello <?php echo $level3; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <?php
                                } else {
                                    ?>
                                    <br>
                                    <table class="table-bordered table-hover table-striped table-dark">
                                        <thead>
                                            <tr>
                                                <th style="text-align:center; vertical-align:middle;"><h3 style="color:red">Attenzione! <br>Hai meno di tre personaggi!</h3></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <tr style="text-align:center; vertical-align:middle;">
                                                <td>
                                                    <form class="user" action="characters.php" method="post">
                                                        <input class="btn-red btn-block" type="submit" value="Crea personaggio!">
                                                    </form>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <?php
                                }
                                ?>
                            </div>

                            <div class="col-md-6">
                                <table class="table table-bordered table-hover table-striped table-dark">
                                    <thead>
                                        <tr>
                                            <th colspan="2" style="text-align:center; vertical-align:middle;"><h3><b>Diplomazia</b></h3></th>
                                        </tr>

                                    <?php
                                    $sql = " SELECT distinct description FROM effects WHERE ID_community = $ID_community AND ID_turn = $ID_turn AND tag = 'diplomacy' ORDER BY description DESC";
                                    $ris = $conn->query($sql);
                                    while ($row = $ris->fetch_assoc()) {
                                        $description = $row['description'];
                                        ?>
                                        <tr>
                                            <th colspan="2" style="text-align:left; vertical-align:middle;"><center><?php echo $description; ?></center></th>
                                        </tr>
                                        <?php
                                    }
                                    ?>          

                                    <tr>
                                        <th style="text-align:center; vertical-align:middle;" width="60%">Comunità</th>
                                        <th style="text-align:center; vertical-align:middle;" width="40%">Relazioni</th> 
                                    </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                        $sql = " SELECT * FROM com_communities WHERE ID_community = $ID_community AND ID_turn = $ID_turn ORDER BY ID DESC";
                                        $ris = $conn->query($sql);
                                        while ($row = $ris->fetch_assoc()) {
                                            $ID_know = $row['ID_know'];

                                            $sqlKnow = "SELECT users.username, communities.ID as ID_com, communityName FROM communities "
                                                    . " INNER JOIN users ON users.ID = communities.ID_player "
                                                    . "WHERE communities.ID = $ID_know AND ID_turn = $ID_turn ";
                                            if ($risKnow = $conn->query($sqlKnow)) {
                                                $rowKnow = $risKnow->fetch_assoc();
                                                $communityName = $rowKnow['communityName'];
                                                $ID_com = $rowKnow['ID_com'];
                                                $username = ($rowKnow['username'] == 'neutral') ? "Neutrale" : $rowKnow['username'];
                                            }

                                            $relationship = $row['relationship'];

                                            switch ($relationship) {
                                                case $relationship < 20;
                                                    $italian = " Guerra aperta";
                                                    break;
                                                case $relationship >= 20 && $relationship < 40;
                                                    $italian = " Astiose";
                                                    break;
                                                case $relationship >= 40 && $relationship < 60;
                                                    $italian = " Neutre";
                                                    break;
                                                case $relationship >= 60 && $relationship < 80;
                                                    $italian = " Cordiali";
                                                    break;
                                                case $relationship >= 20:
                                                    $italian = " Stretta alleanza";
                                                    break;
                                                default:
                                                    break;
                                            }
                                            if ($relationship == 0) {
                                                $italian = " Guerra aperta";
                                            }
                                            ?>

                                            <tr>
                                                <td style="text-align:center; vertical-align:middle;"><?php echo $communityName; ?> (<?php echo $username; ?>)</td>
                                                <td style="text-align:center; vertical-align:middle;"><?php echo $italian; ?></td>
                                            </tr>

                                            <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <table class="table table-bordered table-hover table-striped table-dark">
                                    <thead>
                                        <tr>
                                            <th colspan="4" style="text-align:center; vertical-align:middle;" width="10%"><h3><b>Punti</b></h3></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td width="8%" style="text-align:center; vertical-align:middle; font-size:20px;">Tipo</td>
                                            <td style="text-align:center; vertical-align:middle; font-size:20px;">Inizio turno</td>
                                            <td style="text-align:center; vertical-align:middle; font-size:20px;">+/-</td>
                                            <td style="text-align:center; vertical-align:middle; font-size:20px;">Fine turno</td>
                                        </tr>
                                        <tr>
                                            <td width="20%" style="text-align:center; vertical-align:middle; font-size:20px;"><img class="imgElements" src="img/items/violence.png" alt="food"></td>
                                            <td style="text-align:center; vertical-align:middle; font-size:20px;"><?php echo $pViolenceStart; ?></td>
                                            <td style="text-align:center; vertical-align:middle; font-size:20px;"><?php echo $pViolenceChange; ?></td>
                                            <td style="text-align:center; vertical-align:middle; font-size:20px;"><?php echo $pViolence + $pV; ?></td>


                                        </tr>
                                        <tr>
                                            <td width="20%" style="text-align:center; vertical-align:middle; font-size:20px;"><img class="imgElements" src="img/items/cooperation.png" alt="food"></td>
                                            <td style="text-align:center; vertical-align:middle; font-size:20px;"><?php echo $pCooperationStart; ?></td>
                                            <td style="text-align:center; vertical-align:middle; font-size:20px;"><?php echo $pCooperationChange; ?></td>
                                            <td style="text-align:center; vertical-align:middle; font-size:20px;"><?php echo $pCooperation + $pC; ?></td>
                                        </tr>
                                        <tr>
                                            <td width="20%" style="text-align:center; vertical-align:middle; font-size:20px;"><img class="imgElements" src="img/items/autarchy.png" alt="food"></td>
                                            <td style="text-align:center; vertical-align:middle; font-size:20px;"><?php echo $pAutarchyStart; ?></td>
                                            <td style="text-align:center; vertical-align:middle; font-size:20px;"><?php echo $pAutarchyChange; ?></td>
                                            <td style="text-align:center; vertical-align:middle; font-size:20px;"><?php echo $pAutarchy + $pA; ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <table class="table table-bordered table-hover table-striped table-dark">
                                    <thead>
                                        <tr>
                                            <th colspan="4" style="text-align:center; vertical-align:middle;" width="10%"><h3><b>Risorse</b></h3></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td width="8%" style="text-align:center; vertical-align:middle; font-size:20px;">Tipo</td>
                                            <td style="text-align:center; vertical-align:middle; font-size:20px;">Inizio turno</td>
                                            <td style="text-align:center; vertical-align:middle; font-size:20px;">+/-</td>
                                            <td style="text-align:center; vertical-align:middle; font-size:20px;">Fine turno</td>
                                        </tr>
                                        <tr>
                                            <td width="15%" style="text-align:center; vertical-align:middle; font-size:20px;"><img class="imgElements" src="img/items/foods.png" alt="food"></td>
                                            <td style="text-align:center; vertical-align:middle; font-size:20px;"><?php echo $foodStart; ?></td>
                                            <td style="text-align:center; vertical-align:middle; font-size:20px;"><?php echo $foodChange; ?></td>
                                            <td style="text-align:center; vertical-align:middle; font-size:20px;"><?php echo $foodNew; ?></td>
                                        </tr>
                                        <tr>
                                            <td width="15%" style="text-align:center; vertical-align:middle; font-size:20px;"><img class="imgElements" src="img/items/tools.png" alt="tool"></td>
                                            <td style="text-align:center; vertical-align:middle; font-size:20px;"><?php echo $toolStart; ?></td>
                                            <td style="text-align:center; vertical-align:middle; font-size:20px;"><?php echo $toolChange; ?></td>
                                            <td style="text-align:center; vertical-align:middle; font-size:20px;"><?php echo $toolNew; ?></td>
                                        </tr>
                                        <tr>
                                            <td width="15%" style="text-align:center; vertical-align:middle; font-size:20px;"><img class="imgElements" src="img/items/drugs.png" alt="drug"></td>
                                            <td style="text-align:center; vertical-align:middle; font-size:20px;"><?php echo $drugStart; ?></td>
                                            <td style="text-align:center; vertical-align:middle; font-size:20px;"><?php echo $drugChange; ?></td>
                                            <td style="text-align:center; vertical-align:middle; font-size:20px;"><?php echo $drugNew; ?></td>
                                        </tr>
                                        <tr>
                                            <td width="15%" style="text-align:center; vertical-align:middle; font-size:20px;"><img class="imgElements" src="img/items/weapons2.png" alt="weapon"></td>
                                            <td style="text-align:center; vertical-align:middle; font-size:20px;"><?php echo $weaponStart; ?></td>
                                            <td style="text-align:center; vertical-align:middle; font-size:20px;"><?php echo $weaponChange; ?></td>
                                            <td style="text-align:center; vertical-align:middle; font-size:20px;"><?php echo $weaponNew; ?></td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                            <br><br>
                        </div>
                        <?php
                        $sql = "SELECT *, com_build.ID as ID_comBuild FROM com_build "
                                . "INNER JOIN communities ON communities.ID = com_build.ID_community "
                                . "INNER JOIN buildings ON buildings.ID = com_build.ID_building "
                                . "WHERE ID_community = $ID_community AND com_build.ID_turn = $ID_turn AND hidden = 0 "
                                . "ORDER BY class;";
                        $ris = $conn->query($sql);
                        $check = $ris->num_rows;
                        if ($check) {
                            ?> 
                            <table class="table table-bordered table-hover table-striped table-dark">
                                <thead>
                                    <tr>
                                        <th colspan="4" style="text-align:center; vertical-align:middle;"><h3><b>Edifici turno <?php echo $numTurn; ?></b></h3></th>
                                </tr>
                                <tr>
                                    <th style="text-align:center; vertical-align:middle;" width="25%">Nome</th>
                                    <th class="hide" style="text-align:center; vertical-align:middle;">Descrizione</th> 
                                    <th style="text-align:center; vertical-align:middle;" width="15%">Effetto</th> 
                                    <th style="text-align:center; vertical-align:middle;" width="10%">Lavoratori</th> 

                                </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row = $ris->fetch_assoc()) {

                                        $ID_com = $row['ID_comBuild'];
                                        $ID_build = $row['ID_build'];
                                        $underCostruction = $row['underCostruction'];
                                        $abandoned = $row['abandoned'];
                                        $numBuilding = $row['numBuilding'];

                                        $ID_building = $row['ID'];
                                        $name = $row['name'];
                                        $level = $row['level'];
                                        $level = ($row["level"] == '0') ? "Livello unico" : "Liv. " . $row['level'];
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
                                        if ($abandoned == $popMax) {
                                            $disableAdd = "disabled";
                                            $color1 = "btn-red";
                                        }

                                        if ($abandoned == 0) {
                                            $disableRemove = "disabled";
                                            $color2 = "btn-red";
                                        }

                                        $effects = Building::effects($ID_com);
                                        ?>

                                        <tr>
                                            <td style="text-align:center; vertical-align:middle;"><?php echo $name; ?><br>Livello <?php echo $level; ?></td>
                                            <td class="hide" style="text-align:center; vertical-align:middle;"><?php echo $description; ?></td>
                                            <td style="text-align:center; vertical-align:middle;"><?php echo $effects; ?></td>
                                            <td style="text-align:center; vertical-align:middle;"><?php echo $abandoned; ?></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php ?>
        <?php
        /* ================================ Fine sezione centrale ================================ */
    }
} else {
    header("location: login.php");
    $_SESSION['redirect'] = "community";
}

$footer = new Footer();
$footer->show("../../");

session_commit();
?>