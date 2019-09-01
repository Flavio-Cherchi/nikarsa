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
}

if ($_POST['createEvent']) {

    $sql = "SELECT username FROM users WHERE ID = $ID_user; ";
    $ris = $conn->query($sql);
    $row = $ris->fetch_assoc();
    $uploadedBy = $row["username"];

    $image = $_POST['image'];
    $oneResult = $_POST['oneResult'];
    $effetti = $_POST['effetti'];
    $scelte = $_POST['scelte'];
    $title = $_POST['title'];
    $description = $_POST['description'];

    $title = str_replace("'", "\\'", $title);
    $description = str_replace("'", "\\'", $description);
    $sql = " INSERT INTO events (title, description, img, oneResult, uploadedBy, active) VALUES ('$title', '$description', '$image', '$oneResult', '$uploadedBy', 0)";
    $ris = $conn->query($sql);
}



/* ------------------------- Upload image ------------------------- */
if ($_POST['submit']) {
    $target_dir = "img/events/";
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
        echo "Mi dispiace, un file con lo stesso nome è già presente.";
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

            $sql = " INSERT INTO images (url, uploadedBy, tag, sex) VALUES ('$target_file', '$username', 'zombie', 'n')";
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

        <div class="container">

            <!-- Outer Row -->
            <div class="row justify-content-center">

                <div class="col-xl-10 col-lg-12 col-md-9">

                    <div class="">
                        <div class="card-body p-0">
                            <!-- Nested Row within Card Body -->
                            <div class="row">

                                <div class="col-lg-12">
                                    <div class="p-0">
                                        <div class="text-center">
                                            <h1 class="h4 text-gray-900 mb-4">Crea e leggi eventi</h1>
                                        </div>

                                        <?php
                                        if ((!$_POST['createEvent']) && (!$_POST['leggi'])) {

                                            if (!$_POST['image']) {
                                                ?>


                                                <form action="createEvent.php" method="post" enctype="multipart/form-data">
                                                    <input class="btn btn-black btn-block" type="submit" value="Carica immagine" name="submit">
                                                    <input class="btn"  type="file" name="fileToUpload" id="fileToUpload">
                                                    <input type="hidden" name="image">

                                                </form>


                                                <form action="listImages.php" method="post">
                                                    <input type="hidden" name="image">
                                                    <input class="btn btn-black btn-block" type="submit" value="Catalogo immagini" name="submit">
                                                </form>


                                                <?php
                                            } elseif ($_POST['image']) {
                                                $image = $_POST['image'];
                                                ?> 

                                                <form class="user" action="createEvent.php" method="post">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control form-control-user" name="title" placeholder="title">
                                                    </div>
                                                    <div class="form-group">
                                                        <textarea class="form-control form-control-user" name="description" rows="3" placeholder="description"></textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="radio" name="oneResult" value="negativo" checked>Negativo<br>
                                                        <input type="radio" name="oneResult" value="neutro">Neutro<br>
                                                        <input type="radio" name="oneResult" value="positivo"> Positivo 
                                                    </div>  
                                                    <input type="hidden" name="createEvent" value="1">
                                                    <input type="hidden" name="image" value="<?php echo $image; ?>">
                                                    <input class="btn btn-black btn-user btn-block" type="submit" value="Invia">
                                                    <br>
                                                </form>

                                                <?php
                                            }
                                        } elseif ($_POST['description']) {
                                            $sql = "SELECT * FROM events "
                                                    . "INNER JOIN images "
                                                    . "on events.img = images.ID where events.ID=(SELECT MAX(events.ID) FROM events)";

                                            if ($ris = $conn->query($sql)) {
                                                while ($row = $ris->fetch_assoc()) {
                                                    $title = $row["title"];
                                                    $description = $row["description"];
                                                    $image = $row["url"];
                                                    $oneResult = $row["oneResult"];
                                                }
                                            }
                                            ?>

                                            <center>
                                                <h2><?php echo $title; ?></h2>
                                                <img class="elencoImg" src="<?php echo $image; ?>" alt="image"></td> 
                                            </center>
                                            <br>
                                            <p style="text-align: justify"><?php echo $description; ?></p>
                                            <center>Esito <?php echo $oneResult; ?></center>
                                            <br>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <form class="kt-form" action="createEvent.php" method="post">
                                                        <button class="btn btn-black btn-block">Crea nuovo evento</button>
                                                    </form>
                                                </div>
                                                <div class="col-lg-6"><form class="kt-form" action="listEvents.php" method="post">
                                                        <button class="btn btn-black btn-block">Visualizza lista eventi</button>
                                                    </form>
                                                </div>
                                            </div>
                                            <?php
                                        }

                                        if ($_POST['leggi']) {
                                            $ID_event = $_POST['ID_event'];

                                            $sql = "SELECT * FROM events "
                                                    . "INNER JOIN images "
                                                    . "on events.img = images.ID where events.ID='$ID_event'";

                                            if ($ris = $conn->query($sql)) {
                                                while ($row = $ris->fetch_assoc()) {
                                                    $title = $row["title"];
                                                    $description = $row["description"];
                                                    $image = $row["url"];
                                                    $oneResult = $row["oneResult"];
                                                }
                                            }
                                            ?>

                                            <center>
                                                <h2><?php echo $title; ?></h2>
                                                <img class="elencoImg" src="<?php echo $image; ?>" alt="image"></td> 
                                            </center>
                                            <br>
                                            <p style="text-align: justify"><?php echo $description; ?></p>
                                            <center>Esito <?php echo $oneResult; ?></center>
                                            <br>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <form class="kt-form" action="createEvent.php" method="post">
                                                        <button class="btn btn-black btn-block">Crea nuovo evento</button>
                                                    </form>
                                                </div>
                                                <div class="col-lg-6"><form class="kt-form" action="listEvents.php" method="post">
                                                        <button class="btn btn-black btn-block">Visualizza lista eventi</button>
                                                    </form>
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
    /* ================================ Fine sezione centrale ================================ */
} else {
    header("location: login.php");
    $_SESSION['redirect'] = "createEvent";
}

$footer = new Footer();
$footer->show("../../");

session_commit();
?>