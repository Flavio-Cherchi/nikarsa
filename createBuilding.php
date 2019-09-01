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

if ($_POST['modify']) {
    $ID = $_POST['ID'];

    $sql = " SELECT * FROM buildings WHERE ID = $ID ";

    if ($ris = $conn->query($sql)) {
        $row = $ris->fetch_assoc();
        $name = $row["name"];
        $description = $row["description"];
        $level = $row["level"];
        $class = $row["class"];
        $numMax = $row["numMax"];
        $underCostruction = $row["underCostruction"];
        $food = $row["food"];
        $tool = $row["tool"];
        $weapon = $row["weapon"];
        $popCostruction = $row["popCostruction"];
        $popMax = $row["popMax"];

        $select1 = ($class == "defense") ? "selected" : "";
        $select2 = ($class == "relations") ? "selected" : "";
        $select3 = ($class == "production") ? "selected" : "";
        $select4 = ($class == "hygiene") ? "selected" : "";
        $select5 = ($class == "colonization") ? "selected" : "";
        $select6 = ($class == "m1") ? "selected" : "";
        $select7 = ($class == "m2") ? "selected" : "";
        $select8 = ($class == "m3") ? "selected" : "";
        }

        $title = "Modifica " . $name;
        $post = "modified";
        $italian = "Modifica";
        } else {
        $level = 1;
        $numMax = 1;
        $underCostruction = 1;
        $food = 0;
        $tool = 0;
        $weapon = 0;
        $popCostruction = 1;
        $popMax = 1;

        $title = "Crea edificio";
        $post = "created";
        $italian = "Invia";
        }


        if ($_POST['created'] || $_POST['modified']) {

        $name = $_POST["name"];
        $description = $_POST["description"];
        $level = $_POST["level"];
        $class = $_POST["class"];
        $numMax = $_POST["numMax"];
        $underCostruction = $_POST["underCostruction"];
        $food = $_POST["food"];
        $tool = $_POST["tool"];
        $weapon = $_POST["weapon"];
        $popCostruction = $_POST["popCostruction"];
        $popMax = $_POST["popMax"];

        $name = str_replace("'", "\\'", $name);
        $description = str_replace("'", "\\'", $description);
        if ($_POST['modified']) {
        $ID = $_POST["ID"];
        Building::update($ID, $name, $description, $level, $class, $numMax, $underCostruction, $food, $tool, $weapon, $popCostruction, $popMax);
        }
        if ($_POST['created']) {
        Building::insert($name, $description, $level, $class, $numMax, $underCostruction, $food, $tool, $drug, $weapon, $popCostruction, $popMax);
        }
        header("location: listBuildings.php");
        }








        if ($admin) {

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
                <div class="row justify-content-center">
                    <div class="col-xl-10 col-lg-12 col-md-9">
                        <div class="card-body p-0">
                            <!-- Nested Row within Card Body -->
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="p-0">
                                        <div class="text-center">
                                            <h1 class="h4 text-gray-900 mb-4"><?php echo $title; ?></h1>
                                        </div>
                                        <form class="user" action="createBuilding.php" method="post">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <center>
                                                        <div class="form-group">
                                                            <span>Nome edificio</span><br>
                                                            <input type="text" name="name" value="<?php echo $name; ?>">
                                                        </div>
                                                        <div class="form-group">
                                                            <span>Livello</span><br>
                                                            <span>(Livello 0 = Edifici senza upgrade)</span><br>
                                                            <input type="number" name="level" value="<?php echo $level; ?>" min="0" max="10">
                                                        </div>
                                                    </center>
                                                    <div class="form-group">
                                                        <span>Descrizione</span>
                                                        <textarea class="form-control form-control-user" name="description" rows="3"><?php echo $description; ?></textarea>
                                                    </div>
                                                    <span>Categoria</span><br>
                                                    <select name="class" class="form-control">
                                                        <option value="defense" <?php echo $select1; ?>>Difesa</option>                                                 
                                                        <option value="relations" <?php echo $select2; ?>>Relazioni</option>                                                 
                                                        <option value="production" <?php echo $select3; ?>>Produzione</option>                                                 
                                                        <option value="hygiene" <?php echo $select4; ?>>Igiene</option>
                                                        <option value="colonization" <?php echo $select5; ?>>Colonizzazione</option>
                                                        <option value="m1" <?php echo $select6; ?>>Meraviglia (violenza)</option>
                                                        <option value="m2" <?php echo $select7; ?>>Meraviglia (cooperazione)</option>
                                                        <option value="m3" <?php echo $select8; ?>>Meraviglia (autarchia)</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <br>
                                            <center>
                                                <div class="row">  
                                                    <div class="col-lg-6">
                                                        <div class="form-group">
                                                            <span>Numero massimo edifici</span><br>
                                                            <input type="number" name="numMax" min="1" value="<?php echo $numMax; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="form-group">
                                                            <span>Numero massimo lavoratori</span><br>
                                                            <input type="number" name="popMax" min="1" value="<?php echo $popMax; ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </center>
                                            <hr><center> <strong>Durante la costruzione </strong><br>
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="form-group">
                                                            <span>Manovali</span><br>
                                                            <input type="number" name="popCostruction" min="1" value="<?php echo $popCostruction; ?>">
                                                        </div>
                                                        <div class="form-group">
                                                            <span>Turni</span><br>
                                                            <input type="number" name="underCostruction" min="1" value="<?php echo $underCostruction; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="form-group">
                                                            <span>Cibo</span><br>
                                                            <input type="number" name="food" min="0" value="<?php echo $food; ?>">
                                                        </div>
                                                        <div class="form-group">
                                                            <span>Utensili</span><br>
                                                            <input type="number" name="tool" min="0" value="<?php echo $tool; ?>">
                                                        </div>
                                                        <div class="form-group">
                                                            <span>Armi</span><br>
                                                            <input type="number" name="weapon" min="0" value="<?php echo $weapon; ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </center>
                                            <hr>
                                            <div class="row">
                                                <input type="hidden" name="<?php echo $post; ?>" value="1">
                                                <input type="hidden" name="ID" value="<?php echo $ID; ?>">
                                                <input class="btn btn-black btn-user btn-block" type="submit" value="<?php echo $italian; ?>"> 
                                                </form>
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
            $_SESSION['redirect'] = "createBuilding";
        }





        $footer = new Footer();
        $footer->show("../../");

        session_commit();
        ?>