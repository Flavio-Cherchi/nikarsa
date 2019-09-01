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

if ($ID_user) {

    $sql = "SELECT ID FROM communities WHERE ID_player = $ID_user AND ID_campaign = $ID_campaign; ";
    $ris = $conn->query($sql);
    $row = $ris->fetch_assoc();
    $check = $row['ID'];
    if (!$check) {
        $waiting = 1;
        //header("location: index.php");
    }

//Value if characters (3 per player) have been created;
    $sql = "SELECT COUNT(characters.ID) as numcharacters FROM characters "
            . "INNER JOIN communities ON characters.ID_community = communities.ID "
            . "INNER JOIN users ON communities.ID_player = users.ID "
            . "INNER JOIN turns ON communities.ID_turn = turns.ID "
            . "WHERE turns.ID = $ID_turn AND users.ID = $ID_user; ";

    $daCreare = 0;
    if ($ris = $conn->query($sql)) {
        while ($row = $ris->fetch_assoc()) {
            if ($row['numcharacters'] < 3) {
                $daCreare = 1;
            } else
                $daCreare = 0;
        }
    } else {
        $daCreare = 1;
    }

    if ($admin) {
        $daCreare = 0;
    }

//Create character
    if ($_POST['creato']) {

        $sql = " SELECT communities.ID as ID FROM communities "
                . " INNER JOIN users ON communities.ID_player = users.ID "
                . "INNER JOIN turns ON communities.ID_turn = turns.ID "
                . "WHERE turns.ID = $ID_turn AND users.ID = $ID_user; ";

        if ($ris = $conn->query($sql)) {
            $row = $ris->fetch_assoc();
            $ID_community = $row['ID'];
        }

        $name = $_POST['name'];
        $description = $_POST['description'];
        $class = $_POST['class'];
        $url = $_POST['url'];

        $name = str_replace("'", "\\'", $name);
        $description = str_replace("'", "\\'", $description);
        $sql = " INSERT INTO characters "
                . "(name, description, level, subLevel, class , ID_community, ID_turn, img) "
                . "VALUES ('$name', '$description', 1, 1, '$class', $ID_community, $ID_turn, '$url')";
        $ris = $conn->query($sql);

        $sql = " UPDATE communities SET population = population - 1 WHERE ID = $ID_community AND ID_turn = $ID_turn; ";
        $ris = $conn->query($sql);
    }

    if ($daCreare) {


        /* ------------------------- Upload image ------------------------- */
        if ($_POST['submit']) {
            $target_dir = "img/characters/";
            $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image
            if (isset($_POST["submit"])) {
                $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
                if ($check !== false) {
                    "Il file è un'immagine - " . $check["mime"] . ".";
                    $uploadOk = 1;
                } else {
                    echo "Il file non è un'immagine.";
                    $uploadOk = 0;
                }
            }
// Check if file already exists
            if (file_exists($target_file)) {
                echo "Mi dispiace, un file con lo stesso name è già presente.";
                $uploadOk = 0;
            }
// Check file size
            if ($_FILES["fileToUpload"]["size"] > 500000) {
                echo "Mi dispiace, il file è troppo grande (limite consentito: 5mb).";
                $uploadOk = 0;
            }
// Allow certain file formats
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                echo "Mi dispiace, sono permessi solo file JPG, JPEG, PNG e GIF.";
                $uploadOk = 0;
            }
// Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                echo "Mi dispiace, il file non è stato caricato.";
// if everything is ok, try to upload file
            } else {
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                    "The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.";

                    $sql = " INSERT INTO images (url, uploadedBy, tag, sex) VALUES ('$target_file', '$username', 'characters', 'n')";
                    $ris = $conn->query($sql);

                    $sql = "SELECT ID FROM images WHERE url = '$target_file'";
                    $ris = $conn->query($sql);
                    $row = $ris->fetch_assoc();
                    $_POST['image'] = $row['ID'];
                } else {
                    echo "C'è stato un errore durante la procedura, prova da capo.";
                }
            }
        }
        /* ------------------------- End upload image ------------------------- */


        /* ================================ Character creation ================================ */
        ?>

        <!--    <div class="ischidados "> -->
        <div>

            <?php
            $navbar = new Start();
            $navbar->go();
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
                                            <div class="text-center">
                                                <h1 class="h4 text-gray-900 mb-4">Creazione personaggio</h1>
                                            </div>

                                            <?php
                                            if (!$waiting) {
                                                if ((!$_POST['creato'])) {

                                                    if (!$_POST['image']) {
                                                        ?>
                                                        <form action="characters.php" method="post" enctype="multipart/form-data">
                                                            <input class="btn btn-black btn-block" type="submit" value="Carica immagine" name="submit">
                                                            <input class="btn"  type="file" name="fileToUpload" id="fileToUpload">
                                                            <input type="hidden" name="image">
                                                        </form>


                                                        <form action="listImages.php" method="post">
                                                            <input type="hidden" name="creatingChar" value='1'>
                                                            <input class="btn btn-black btn-block" type="submit" value="Catalogo immagini" name="submit">
                                                        </form>


                                                        <?php
                                                    } elseif ($_POST['image'] && (!$_POST['class'])) {
                                                        $image = $_POST['image'];

                                                        $sql = "SELECT url FROM images where ID = $image;";

                                                        if ($ris = $conn->query($sql)) {
                                                            $row = $ris->fetch_assoc();
                                                            $url = $row["url"];
                                                        }
                                                        ?>
                                                        <center>
                                                            <img class="elencoImg" src="<?php echo $url; ?>" alt="image">
                                                        </center>
                                                        <br>
                                                        <form class="user" action="characters.php" method="post">
                                                            <div class="form-group">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control form-control-user" name="name" placeholder="Nome">
                                                                </div>
                                                                <div class="form-group">
                                                                    <textarea class="form-control form-control-user" name="description" rows="3" placeholder="description"></textarea>
                                                                </div>
                                                                <center>
                                                                    <input type="radio" name="class" value="violent" checked>Violento
                                                                    <input type="radio" name="class" value="cooperative">Cooperativo
                                                                    <input type="radio" name="class" value="autarkic"> Autarchico 
                                                                </center>
                                                            </div>  

                                                            <input type="hidden" name="url" value="<?php echo $url; ?>">
                                                            <input type="hidden" name="creato" value="1">
                                                            <input class="btn btn-black btn-user btn-block" type="submit" value="Invia">
                                                            <br>
                                                        </form>
                                                        <?php
                                                    }
                                                } else {
                                                    header("location: community.php");
                                                    ?>
                                                    <!--
                                                    <center>
                                                        <h2><?php echo $name; ?></h2>
                                                        <img class="elencoImg" src="<?php echo $url; ?>" alt="image"></td> 
                                                    </center>
                                                    <br>
                                                    <center>
                                                        <p style="text-align: justify"><?php echo $description; ?></p>
                                                        <p><?php echo $class; ?>
                                                    </center>
                                                    -->
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                <div class="col-lg-12">
                                                    <div class="">
                                                        <div class="text-center">
                                                            <h1 class="h4 text-gray-900 mb-4" style="color:black">Ciao <?php echo $username; ?></h1>
                                                        </div>
                                                        <center>
                                                            <img class="elencoImg" src="img/events/waiting.gif" alt="waiting zombie">
                                                            <br><br>
                                                            <p style="color:black">Benvenuto ad Ischidados! Aspetta che il master crei la tua comunità.</p></center>
                                                    </div>
                                                </div>

                                                <?php
                                            }
                                            ?> 
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
        /* ================================ End character creation ================================ */
    } elseif (!$_POST['justOne']) {
        header("location: listCharacters.php");
    } else {

        /* ================================ Characters ================================ */
        ?>
        <div>

            <?php
            $navbar = new Start();
            $navbar->go();

            if ($_POST['ID_character']) {
                $ID_character = $_POST['ID_character'];
            } else {
                echo "Error!";
            }

            $sql = "SELECT * FROM characters WHERE ID = $ID_character; ";
            $ris = $conn->query($sql);
            $row = $ris->fetch_assoc();
            $name = $row["name"];
            $description = $row["description"];
            $subLevel = $row["level"];
            $img = $row["img"];
            $class = $row['class'];
            if ($class == "violent") {
                $italian = "violento";
            } elseif ($class == "cooperative") {
                $italian = "cooperativo";
            } elseif ($class == "autarkic") {
                $italian = "autarchico";
            }


            if ($subLevel < 6) {
                $level = 1;
            } elseif (($subLevel % 6) == 0) {
                $level = $subLevel / 6;
            } else
                $level = floor($subLevel / 6) + 1;     //da sistemare anche durante il cambio di turno!!!
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

                                            <center>
                                                <h2><?php echo $name; ?></h2>
                                                <br>
                                                <img class="elencoImg" src="<?php echo $img; ?>" alt="image"></td> 
                                            </center>
                                            <br>
                                            <center>
                                                <p>Personaggio <?php echo $italian; ?>
                                                <p style="text-align: justify"><?php echo $description; ?></p>
                                            </center>
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
        /* ================================ End characters ================================ */
    }
} else {
    header("location: login.php");
    $_SESSION['redirect'] = "characters";
}

$footer = new Footer();
$footer->show("../../");

session_commit();
?>