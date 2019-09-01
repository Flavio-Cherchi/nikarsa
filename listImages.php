<?php
include "./class/ConnessioneDB.php";
include "./class/Starter.php";
include "./class/Head.php";
include "./class/Footer.php";

$conn = connessione::start();
$head = new Head();
$head->show();

session_start();
//var_dump($_POST);
if ($error) {
    $_SESSION['error'] = $error;
}

if ($_SESSION['ID_user']) {
    $ID_user = $_SESSION['ID_user'];
    $username = $_SESSION['username'];
    $ID_campaign = $_SESSION['ID_campaign'];
    $ID_turn = $_SESSION['ID_turn'];
    $title = $_SESSION['title'];
    $numTurn = $_SESSION['numTurn'];
}

if ($_POST['sex']) {
    $sex = $_POST['sex'];
    $ID_image = $_POST['image'];
    $sql = " UPDATE images SET sex = '$sex' WHERE ID = $ID_image ";
    $ris = $conn->query($sql);
    $check = $_POST['image'];
}

if ($_POST['community']) {
    $community = 1;
}

if ($_POST['communityImgUpload']) {
    /* ------------------------- Upload image ------------------------- */
    if ($_POST['submit']) {
        $target_dir = "img/communities/";
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
            echo "Mi dispiace, il file è troppo grande (limite consentito: 0.5mb).";
            $uploadOk = 0;
        }
// Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo "Mi dispiace, sono permessi solo file JPG, JPEG, PNG e GIF.";
            $uploadOk = 0;
        }
// Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo " Il file non è stato caricato.";
// if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                "The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.";

                $sql = " INSERT INTO images (url, uploadedBy, tag, sex) VALUES ('$target_file', '$username', 'communities', 'n')";
                $ris = $conn->query($sql);

                $sql = " UPDATE communities SET img = '$target_file' WHERE ID_player = $ID_user ";
                $ris = $conn->query($sql);

                header("location: community.php");
            } else {
                echo "C'è stato un errore durante la procedura, prova da capo.";
            }
        }
    }
    /* ------------------------- End upload image ------------------------- */
}

if ($ID_user) {
    /* ================================ Sezione centrale ================================ */
    ?>

    <!--    <div class="ischidados "> -->

    <?php
    $navbar = new Start();
    $navbar->go();
    ?>
    <br>

        <?php
        if ($_POST['creatingChar']) {
            $destinazione = "characters.php";
        } elseif ($_POST['communityImg']) {
            $destinazione = "community.php";
        } else {
            $destinazione = "createEvent.php";
        }

        $sql = "SELECT * FROM images WHERE tag = 'zombie' ";
        if (!$community) {
            ?>
            <div class="container-fluid center-90">
                <button class="btn btn-black btn-user btn-block collapsed" type="button" data-toggle="collapse" data-target="#zombie">
                    Zombie
                </button>
                <br>
                <div id="zombie" class="collapse row">
                    <?php
                    if ($ris = $conn->query($sql)) {

                        while ($row = $ris->fetch_assoc()) {
                            $ID_image = $row['ID'];
                            $url = $row['url'];
                            $uploadedBy = $row['uploadedBy'];
                            $tag = $row['tag'];
                            ?>

                            <div class="col-lg-4 col-md-6 col-sm-4 pageCenter-small">
                                <form class="kt-form" action="<?php echo $destinazione; ?>" method="post">
                                    <input type="hidden" name="image" value="<?php echo $ID_image ?>">
                                    <center><button class="btn btn-black"><img class="elencoImg" src="<?php echo $url; ?>" alt="image"></button></center>
                                </form>
                            </div>



                            <?php
                        }
                    }
                    ?>
                </div>
            </div>

            <?php
            $sql = "SELECT * FROM images WHERE tag = 'characters' ";
            ?>
            <div class="container-fluid center-90">
                <button class="btn btn-black btn-user btn-block collapsed" type="button" data-toggle="collapse" data-target="#personaggi">
                    Personaggi
                </button>
                <br>
                <div id="personaggi" class="collapse row">

                    <?php
                    if ($ris = $conn->query($sql)) {
                        while ($row = $ris->fetch_assoc()) {
                            $ID_image = $row['ID'];
                            $url = $row['url'];
                            $uploadedBy = $row['uploadedBy'];

                            switch ($row['tag']) {
                                case "zombie":
                                    $tag = "Zombie";
                                    break;
                                case "characters":
                                    $tag = "Personaggio";
                                    break;
                                default:
                                    break;
                            }

                            $selected1 = ($row['sex'] == "m") ? "selected" : "";
                            $selected2 = ($row['sex'] == "f") ? "selected" : "";
                            $selected3 = ($row['sex'] == "n") ? "selected" : "";

                            if (!$_POST['imgAdmin']) {
                                ?>

                                <div class="col-lg-4 col-md-6 col-sm-4 pageCenter-small">
                                    <form class="kt-form" action="<?php echo $destinazione; ?>" method="post">
                                        <input type="hidden" name="image" value="<?php echo $ID_image ?>">
                                        <center><button class="btn btn-black"><img class="elencoImg" src="<?php echo $url; ?>" alt="image"></button></center>
                                    </form>
                                </div>
                                <?php
                            } else {

                                if ($check == $ID_image) {
                                    $autofocus = "autofocus";
                                }
                                ?>

                                <div class="col-lg-4 col-md-6 col-sm-4 pageCenter-small">
                                    <form class="kt-form" action="<?php echo $destinazione; ?>" method="post">
                                        <input type="hidden" name="image" value="<?php echo $ID_image ?>">
                                        <center><button class="btn btn-black"><img class="elencoImg" src="<?php echo $url; ?>" alt="image"></button></center>
                                    </form>
                                    <form class="kt-form" action="listImages.php" method="post">
                                        <center><select name="sex" class="halfwidth">
                                                <option value="m" <?php echo $selected1; ?>>Uomo</option>
                                                <option value="f" <?php echo $selected2; ?>>Donna</option>
                                                <option value="n" <?php echo $selected3; ?>>Nessuno</option>
                                            </select>
                                            <input type="hidden" name="image" value="<?php echo $ID_image ?>">
                                            <input type="hidden" name="imgAdmin" value="1"></center>
                                        <center><button class="btn btn-black" <?php echo $autofocus; ?>>Modifica sesso</button></center>
                                    </form>
                                </div>

                                <?php
                            }
                        }
                    }
                    ?>
                </div>
            </div>

            <?php
            $sql = "SELECT * FROM images WHERE tag = 'communities' ";
            ?>
            <div class="container-fluid center-90">
                <button class="btn btn-black btn-user btn-block collapsed" type="button" data-toggle="collapse" data-target="#comunita">
                    Comunità
                </button>
                <br>
                <div id="comunita" class="collapse row">
                    <?php
                    if ($ris = $conn->query($sql)) {

                        while ($row = $ris->fetch_assoc()) {
                            $ID_image = $row['ID'];
                            $url = $row['url'];
                            $uploadedBy = $row['uploadedBy'];
                            $tag = $row['tag'];
                            ?>
                            <div class="col-lg-4 col-md-6 col-sm-4 pageCenter-small">
                                <form class="kt-form" action="<?php echo $destinazione; ?>" method="post">
                                    <input type="hidden" name="image" value="<?php echo $ID_image ?>">
                                    <center><button class="btn btn-black"><img class="elencoImg" src="<?php echo $url; ?>" alt="image"></button></center>
                                </form>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
    
                <?php
            $sql = "SELECT * FROM images WHERE tag = 'signs' ";
            ?>
            <div class="container-fluid center-90">
                <button class="btn btn-black btn-user btn-block collapsed" type="button" data-toggle="collapse" data-target="#cartelli">
                    Cartelli
                </button>
                <br>
                <div id="cartelli" class="collapse row">
                    <?php
                    if ($ris = $conn->query($sql)) {

                        while ($row = $ris->fetch_assoc()) {
                            $ID_image = $row['ID'];
                            $url = $row['url'];
                            $uploadedBy = $row['uploadedBy'];
                            $tag = $row['tag'];
                            ?>
                            <div class="col-lg-4 col-md-6 col-sm-4 pageCenter-small">
                                <form class="kt-form" action="<?php echo $destinazione; ?>" method="post">
                                    <input type="hidden" name="image" value="<?php echo $ID_image ?>">
                                    <center><button class="btn btn-black"><img class="elencoImg" src="<?php echo $url; ?>" alt="image"></button></center>
                                </form>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
     
        <?php
    } else {
        ?>
        <div class="container">
            <form action="listImages.php" method="post" enctype="multipart/form-data">
                <input class="btn btn-black btn-block" type="submit" value="Carica immagine" name="submit">
                <input type="hidden" name="communityImgUpload" value="1">
                <input class="btn"  type="file" name="fileToUpload" id="fileToUpload">
                <input type="hidden" name="image">
            </form>
            <form action="listImages.php" method="post">
                <input type="hidden" name="communityImg" value="1">
                <input class="btn btn-black btn-block" type="submit" value="Catalogo immagini" name="submit">
            </form>
        </div>

        <?php
    }
    /* ================================ Fine sezione centrale ================================ */
} else {
    header("location: login.php");
    $_SESSION['redirect'] = "listImages";
}

$footer = new Footer();
$footer->show("../../");

session_commit();
?>