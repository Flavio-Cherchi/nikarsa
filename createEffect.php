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
//var_dump($_POST);
if ($_POST['comEffect']) {


    $playerName = $_POST['playerName'];
    $communityName = $_POST['communityName'];
    if ($_POST['ID_event']) {

        $ID_event = $_POST['ID_event'];

        $sql = "SELECT * FROM events WHERE ID = $ID_event";
        $ris = $conn->query($sql);
        $row = $ris->fetch_assoc();
        $title = $row['title'];
        $description = $row['description'];
        $ID_image = $row['img'];

        $sql = "SELECT url FROM images WHERE ID = $ID_image";
        $ris = $conn->query($sql);
        $row = $ris->fetch_assoc();
        $url = $row['url'];


        $bopMsg = " [size=100][center][b][color=#BF0000]Giocatore[/color][/b]: [b]" . $playerName . "[/b][/center][/size]
            [size=100][center][b][color=#BF0000]Comunità[/color][/b]: [b]" . $communityName . "[/b][/center][/size]

[center][color=#BF0000][size=200][b]" . $title . "[/b][/size][/color][/center]" . "

" . "[center][img]http://www.denai.it/project/ischidados/" . $url . "[/img][/center]" . "
" . "
" .
                $description . "
" . " ";
    }



//ID community
    $ID_community = $_POST['ID_community'];
    $bopMsg1 .= "
             [b][color=#BF0000]Effetti[/color][/b]: " . ""
            . "[list]";
//For community
    $pop = $_POST['population'];
    if ($pop) {
        $msg = ($pop < 0) ? "<br>Popolazione diminuita di " . $pop * -1 . "." : "<br>Popolazione aumentata di " . $pop . ".";
        $bopMsg1 .= ($pop < 0) ? "[*][color=#BF0000]Popolazione diminuita di " . $pop * -1 . ".[/color]" : "[*][color=#40BF00]Popolazione aumentata di " . $pop . ".";
    }
    $pViolence = $_POST['pViolence'];
    if ($pViolence) {
        $msg .= ($pViolence < 0) ? "<br>Punti violenza diminuiti di " . $pViolence * -1 . "." : "<br>Punti violenza aumentati di " . $pViolence . ".";
        $bopMsg1 .= ($pViolence < 0) ? "[*][color=#BF0000]Punti violenza diminuiti di " . $pViolence * -1 . ".[/color]" : "[*][color=#40BF00]Punti violenza aumentati di " . $pViolence . ".[/color]";
    }
    $pCooperation = $_POST['pCooperation'];
    if ($pCooperation) {
        $msg .= ($pCooperation < 0) ? "<br>Punti cooperazione diminuiti di " . $pCooperation * -1 . "." : "<br>Punti cooperazione aumentati di " . $pCooperation . ".";
        $bopMsg1 .= ($pCooperation < 0) ? "[*][color=#BF0000]Punti cooperazione diminuiti di " . $pCooperation * -1 . ".[/color]" : "[*][color=#40BF00]Punti cooperazione aumentati di " . $pCooperation . ".[/color]";
    }
    $pAutarchy = $_POST['pAutarchy'];
    if ($pAutarchy) {
        $msg .= ($pAutarchy < 0) ? "<br>Punti autarchia diminuiti di " . $pAutarchy * -1 . "." : "<br>Punti autarchia aumentati di " . $pAutarchy . ".";
        $bopMsg1 .= ($pAutarchy < 0) ? "[*][color=#BF0000]Punti autarchia diminuiti di " . $pAutarchy * -1 . ".[/color]" : "[*][color=#40BF00]Punti autarchia aumentati di " . $pAutarchy . ".[/color]";
    }
    $food = $_POST['food'];
    if ($food) {
        $msg .= ($food < 0) ? "<br>Cibo diminuito di " . $food * -1 . "." : "<br>Cibo aumentato di " . $food . ".";
        $bopMsg1 .= ($food < 0) ? "[*][color=#BF0000]Cibo diminuito di " . $food * -1 . ".[/color]" : "[*][color=#40BF00]Cibo aumentato di " . $food . ".[/color]";
    }
    $tool = $_POST['tool'];
    if ($tool) {
        $msg .= ($tool < 0) ? "<br>Utensili diminuiti di " . $tool * -1 . "." : "<br>Utensili aumentati di " . $tool . ".";
        $bopMsg1 .= ($tool < 0) ? "[*][color=#BF0000]Utensili diminuiti di " . $tool * -1 . ".[/color]" : "[*][color=#40BF00]Utensili aumentati di " . $tool . ".[/color]";
    }
    $drug = $_POST['drug'];
    if ($drug) {
        $msg .= ($drug < 0) ? "<br>Farmaci diminuiti di " . $drug * -1 . "." : "<br>Farmaci aumentati di " . $drug . ".";
        $bopMsg1 .= ($drug < 0) ? "[*][color=#BF0000]Farmaci diminuiti di " . $drug * -1 . ".[/color]" : "[*][color=#40BF00]Farmaci aumentati di " . $drug . ".[/color]";
    }
    $weapon = $_POST['weapon'];
    if ($weapon) {
        $msg .= ($weapon < 0) ? "<br>Armi diminuite di " . $weapon * -1 . "." : "<br>Armi aumentate di " . $weapon . ".";
        $bopMsg1 .= ($weapon < 0) ? "[*][color=#BF0000]Armi diminuite di " . $weapon * -1 . ".[/color]" : "[*][color=#40BF00]Armi aumentate di " . $weapon . ".[/color]";
    }
    $hygiene = $_POST['hygiene'];
    if ($hygiene) {
        $msg .= ($hygiene < 0) ? "<br>Igiene diminuita di " . $hygiene * -1 . "." : "<br>Igiene aumentata di " . $hygiene . ".";
        $bopMsg1 .= ($hygiene < 0) ? "[*][color=#BF0000]Igiene diminuita di " . $hygiene * -1 . ".[/color]" : "[*][color=#40BF00]Igiene aumentata di " . $hygiene . ".[/color]";
    }

    $sql = " UPDATE communities SET "
            . "population = population + $pop,"
            . "pViolence = pViolence + $pViolence,"
            . "pCooperation = pCooperation + $pCooperation,"
            . "pAutarchy = pAutarchy + $pAutarchy,"
            . "food = food + $food,"
            . "tool = tool + $tool,"
            . "drug = drug + $drug,"
            . "weapon = weapon + $weapon,"
            . "hygiene = hygiene + $hygiene"
            . " WHERE ID = $ID_community AND ID_turn = $ID_turn; ";
    $ris = $conn->query($sql);

    //For characters
    $ID_char1 = $_POST['ID_char1'];
    $ID_char2 = $_POST['ID_char2'];
    $ID_char3 = $_POST['ID_char3'];
    $charName1 = $_POST['charName1'];
    $charName2 = $_POST['charName2'];
    $charName3 = $_POST['charName3'];
    //New levels
    $level1New = $_POST['level1New'];
    $level2New = $_POST['level2New'];
    $level3New = $_POST['level3New'];
    //Old levels
    $level1 = $_POST['level1'];
    $level2 = $_POST['level2'];
    $level3 = $_POST['level3'];
    $class1 = $_POST['class1New'];
    $class2 = $_POST['class2New'];
    $class3 = $_POST['class3New'];

    if ($_POST['class1New'] == "violent") {
        $italian = "violento";
    } elseif ($_POST['class1New'] == "cooperative") {
        $italian = "cooperativo";
    } elseif ($_POST['class1New'] == "autarkic") {
        $italian = "autarchico";
    }

    if ($_POST['class1New'] != $_POST['class1']) {
        echo $_POST['class1New'];
        echo "<br>Secondo" . $_POST['class1'];
        $msg1 = "<br>Il personaggio " . $charName1 . " diventa " . $italian . ".";
        $bopMsg1 .= "[*]Il personaggio " . $charName1 . " diventa " . $italian . ".";
    }

    if ($level1 != $level1New) {
        $msg1 .= "<br>Cambio di livello per " . $charName1 . ": da " . $level1 . " a " . $level1New . ".";
        $bopMsg1 .= "[*]Cambio di livello per " . $charName1 . ": da " . $level1 . " a " . $level1New . ".";
    }

    if ($_POST['class2New'] == "violent") {
        $italian = "violento";
    } elseif ($_POST['class2New'] == "cooperative") {
        $italian = "cooperativo";
    } elseif ($_POST['class2New'] == "autarkic") {
        $italian = "autarchico";
    }

    if ($_POST['class2New'] != $_POST['class2']) {
        $msg1 .= "<br>Il personaggio " . $charName2 . " diventa " . $italian . ".";
        $bopMsg1 .= "[*]Il personaggio " . $charName2 . " diventa " . $italian . ".";
    }

    if ($level2 != $level2New) {
        $msg1 .= "<br>Cambio di livello per " . $charName2 . ": da " . $level2 . " a " . $level2New . ".";
        $bopMsg1 .= "[*]Cambio di livello per " . $charName2 . ": da " . $level2 . " a " . $level2New . ".";
    }

    if ($_POST['class3New'] == "violent") {
        $italian = "violento";
    } elseif ($_POST['class3New'] == "cooperative") {
        $italian = "cooperativo";
    } elseif ($_POST['class3New'] == "autarkic") {
        $italian = "autarchico";
    }

    if ($_POST['class3New'] != $_POST['class3']) {
        $msg1 .= "<br>Il personaggio " . $charName3 . " diventa " . $italian . ".";
        $bopMsg1 .= "[*]Il personaggio " . $charName3 . " diventa " . $italian . ".";
    }

    if ($level3 != $level3New) {
        $msg1 .= "<br>Cambio di livello per " . $charName3 . ": da " . $level3 . " a " . $level3New . ".";
        $bopMsg1 .= "[*]Cambio di livello per " . $charName3 . ": da " . $level3 . " a " . $level3New . ".";
    }
    $bopMsg1 .= "[/list]";
    $sql = " UPDATE characters SET "
            . "level = $level1New,"
            . "class = '$class1' "
            . "WHERE ID = $ID_char1 AND ID_turn = $ID_turn;";
    $ris = $conn->query($sql);

    $sql = " UPDATE characters SET "
            . "level = $level2New,"
            . "class = '$class2' "
            . "WHERE ID = $ID_char2 AND ID_turn = $ID_turn;";
    $ris = $conn->query($sql);

    $sql = " UPDATE characters SET "
            . "level = $level3New,"
            . "class = '$class3' "
            . "WHERE ID = $ID_char3 AND ID_turn = $ID_turn;";
    $ris = $conn->query($sql);

    if ($msg1) {
        $msg .= "<br>" . $msg1;
    }

    if ($bopMsg1) {
        $bopMsg .= "" . $bopMsg1;
    }
    $done = 1;
}


if ($admin) {

    if ($_POST['useEvent']) {
        $ID_event = $_POST['useEvent'];
        $title = $_POST['title'];
    }


    if ($_POST['modification']) {
        $ID_community = $_POST['modification'];
        $ID_event = $_POST['ID_event'];

        $sql = " SELECT *, characters.ID as ID_char "
                . "FROM users "
                . "INNER JOIN communities as communities ON users.ID = communities.ID_player "
                . "INNER JOIN characters as characters ON communities.ID = characters.ID_community "
                . "WHERE communities.ID = $ID_community AND communities.ID_turn = $ID_turn ";

        if ($ris = $conn->query($sql)) {
            $num = 1;
            while ($row = $ris->fetch_assoc()) {
//From communities
                $communityName = $row['communityName'];
                $pop = $row['population'];
                $pViolence = $row['pViolence'];
                $pCooperation = $row['pCooperation'];
                $pAutarchy = $row['pAutarchy'];
                $food = $row['food'];
                $tool = $row['tool'];
                $drug = $row['drug'];
                $weapon = $row['weapon'];
                $hygiene = $row['hygiene'];
//From users
                $playerName = $row['username'];
//From characters

                $class = $row['class'];


                if ($num == 1) {
                    $ID_char1 = $row['ID_char'];
                    $charName1 = $row['name'];
                    $level1 = $row['level'];
                    $class1 = $class;
                    $charImg1 = $row['img'];
                } elseif ($num == 2) {
                    $ID_char2 = $row['ID_char'];
                    $charName2 = $row['name'];
                    $level2 = $row['level'];
                    $class2 = $class;
                    $charImg2 = $row['img'];
                } elseif ($num == 3) {
                    $ID_char3 = $row['ID_char'];
                    $charName3 = $row['name'];
                    $level3 = $row['level'];
                    $class3 = $class;
                    $charImg3 = $row['img'];
                }
                $num++;
            }
        }
    }




    /* ================================ Sezione centrale ================================ */
    ?>

    <!--    <div class="ischidados "> -->
    <div>

        <?php
        $navbar = new Start();
        $navbar->go();

        if (($ID_community) && ($done)) {
            $title0 = "Effetto generato per " . $playerName;
            $title1 = "Genera un nuovo effetto";
        } elseif ($ID_community) {
            $title1 = "Genera effetto per " . $playerName;
        } else
            $title1 = "Seleziona Comunità";
        ?>
        <br>
        <div class="container">
            <div class="row justify-content-center">

                <div class="col-xl-10 col-lg-12 col-md-9">

                    <div class="">
                        <div class="card-body p-0">
                            <!-- Nested Row within Card Body -->
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="p-0">

                                        <?php if ($done) {
                                            ?>
                                            <div class="text-center">
                                                <h1 class="h4 text-gray-900 mb-4"><?php echo $title0; ?></h1>
                                            </div>

                                            <p><?php echo $msg; ?></p>
                                            <textarea class="form-control form-control-user" id="myInput" name="description" rows="10" ><?php echo $bopMsg; ?></textarea>

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




                                        <div class="text-center">
                                            <h1 class="h4 text-gray-900 mb-4"><?php echo $title1; ?></h1>
                                        </div>
                                        <form class="user" action="createEffect.php" method="post">
                                            <?php
                                            if ($ID_community) {
                                                ?>

                                                <div class="text-center">
                                                    <h2 class="h4 text-gray-900 mb-4"><b><?php echo $communityName; ?></b></h2>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="form-group">
                                                            <input type="number" name="population" value="0">Popolazione (<?php echo $pop; ?>)
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="form-group">
                                                            <input type="number" name="pViolence" value="0">Punti violenza (<?php echo $pViolence; ?>)
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="number" name="pCooperation" value="0">Punti cooperazione (<?php echo $pCooperation; ?>)
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="number" name="pAutarchy" value="0">Punti autarchia (<?php echo $pAutarchy; ?>)
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="form-group">
                                                            <input type="number" name="food" value="0">Cibo (<?php echo $food; ?>)
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="number" name="tool" value="0">Utensili (<?php echo $tool; ?>)
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="number" name="drug" value="0">Farmaci (<?php echo $drug; ?>)
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="form-group">
                                                            <input type="number" name="weapon" value="0">Armi (<?php echo $weapon; ?>)
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="number" name="hygiene" value="0">Igiene (<?php echo $hygiene; ?>)
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <!-- ------------ Characters modification section ------------ -->
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <center>
                                                            <img class="elencoImg" src="<?php echo $charImg1; ?>" alt="image1">
                                                        </center>
                                                        <div class="form-group">
                                                            <center>
                                                                <b><?php echo $charName1; ?></b>
                                                                <br>Livello<input type="number" name="level1New" value="<?php echo $level1; ?>">
                                                                <input type="hidden" name="level1" value="<?php echo $level1; ?>">
                                                                <?php
                                                                if ($class1 == "violent") {
                                                                    $check1 = "checked";
                                                                } elseif ($class1 == "cooperative") {
                                                                    $check2 = "checked";
                                                                } elseif ($class1 == "autarkic") {
                                                                    $check3 = "checked";
                                                                }
                                                                ?>
                                                                <br>
                                                                <label class="radio-inline">
                                                                    <input type="radio" name="class1New" value="violent" <?php echo $check1; ?>> Violento
                                                                    <br><input type="radio" name="class1New" value="cooperative" <?php echo $check2; ?>> Cooperativo
                                                                    <br><input type="radio" name="class1New" value="autarkic" <?php echo $check3; ?>> Autarchico 
                                                                </label>
                                                            </center>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <center>
                                                            <img class="elencoImg" src="<?php echo $charImg2; ?>" alt="image2">
                                                        </center>
                                                        <div class="form-group">
                                                            <center>
                                                                <b><?php echo $charName2; ?></b>
                                                                <br>Livello<input type="number" name="level2New" value="<?php echo $level2; ?>">
                                                                <input type="hidden" name="level2" value="<?php echo $level2; ?>">
                                                                <?php
                                                                if ($class2 == "violent") {
                                                                    $check4 = "checked";
                                                                } elseif ($class2 == "cooperative") {
                                                                    $check5 = "checked";
                                                                } elseif ($class2 == "autarkic") {
                                                                    $check6 = "checked";
                                                                }
                                                                ?>
                                                                <br>
                                                                <label class="radio-inline">
                                                                    <input type="radio" name="class2New" value="violent" <?php echo $check4; ?>> Violento
                                                                    <br><input type="radio" name="class2New" value="cooperative" <?php echo $check5; ?>> Cooperativo
                                                                    <br><input type="radio" name="class2New" value="autarkic" <?php echo $check6; ?>> Autarchico 
                                                                </label>
                                                            </center>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <center>
                                                            <img class="elencoImg" src="<?php echo $charImg3; ?>" alt="image3">
                                                        </center>
                                                        <div class="form-group">
                                                            <center>
                                                                <b><?php echo $charName3; ?></b>
                                                                <br>Livello<input type="number" name="level3New" value="<?php echo $level3; ?>">
                                                                <input type="hidden" name="level3" value="<?php echo $level3; ?>">
                                                                <?php
                                                                if ($class3 == "violent") {
                                                                    $check7 = "checked";
                                                                } elseif ($class3 == "cooperative") {
                                                                    $check8 = "checked";
                                                                } elseif ($class3 == "autarkic") {
                                                                    $check9 = "checked";
                                                                }
                                                                ?>
                                                                <br>
                                                                <label class="radio-inline">
                                                                    <input type="radio" name="class3New" value="violent" <?php echo $check7; ?>> Violento
                                                                    <br><input type="radio" name="class3New" value="cooperative" <?php echo $check8; ?>> Cooperativo
                                                                    <br><input type="radio" name="class3New" value="autarkic" <?php echo $check9; ?>> Autarchico 
                                                                </label>
                                                            </center> 
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <input type="hidden" name="comEffect" value="1">
                                                    <input type="hidden" name="ID_community" value="<?php echo $ID_community; ?>">
                                                    <input type="hidden" name="ID_char1" value="<?php echo $ID_char1; ?>">
                                                    <input type="hidden" name="ID_char2" value="<?php echo $ID_char2; ?>">
                                                    <input type="hidden" name="ID_char3" value="<?php echo $ID_char3; ?>">
                                                    <input type="hidden" name="class1" value="<?php echo $class1; ?>">
                                                    <input type="hidden" name="class2" value="<?php echo $class2; ?>">
                                                    <input type="hidden" name="class3" value="<?php echo $class3; ?>">
                                                    <input type="hidden" name="charName1" value="<?php echo $charName1; ?>">
                                                    <input type="hidden" name="charName2" value="<?php echo $charName2; ?>">
                                                    <input type="hidden" name="charName3" value="<?php echo $charName3; ?>">

                                                    <input type="hidden" name="playerName" value="<?php echo $playerName; ?>">
                                                    <input type="hidden" name="communityName" value="<?php echo $communityName; ?>">
                                                    <input type="hidden" name="modification" value="<?php echo $ID_community; ?>">
                                                    <?php
                                                    if ($ID_event) {
                                                        ?>
                                                        <input type="hidden" name="ID_event" value="<?php echo $ID_event; ?>">
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                                <br>

                                                <input class="btn btn-black btn-user btn-block" type="submit" value="Invia"> 
                                                <?php
                                            } else {
                                                ?>

                                                <input type="hidden" name="ID_event" value="<?php echo $ID_event; ?>">                                                    
                                                <select name="modification" class="form-control">
                                                    <?php
                                                    echo $sql = " SELECT *, communities.ID as ID_community FROM users "
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
                                                <?php
                                            }
                                            ?>              
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
    $_SESSION['redirect'] = "createEffect";
}





$footer = new Footer();
$footer->show("../../");

session_commit();
?>