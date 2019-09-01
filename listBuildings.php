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
    $title = $_SESSION['title'];
    $numTurn = $_SESSION['numlTurn'];
    $admin = $_SESSION['admin'];
}

if ($_POST['toDelete']) {
    $ID = $_POST['ID'];
    $name = $_POST['name'];
    $level = $_POST['level'];
    ?>

    <div class="alert alert-warning" role="alert">

        <div class="alert-close">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close" autofocus="autofocus">
                <span aria-hidden="true"><i class="la la-close"></i></span>
            </button>
        </div>
        <div class="alert-text">
            <h4 class="alert-heading">Attenzione!</h4>
            <p>Vuoi davvero eliminare l'edificio "<?php echo $name; ?>" livello <?php echo $level; ?>? L'operazione sarà irreversibile.</p>
            <table>
                <tr>   
                    <td>
                        <form class="kt-form" action="listBuildings.php" method="post">
                            <button type="submit" class="btn btn-info">Cancella</button>
                        </form>
                    </td>
                    <td>
                        <form class="kt-form" action="listBuildings.php" method="post">
                            <input type="hidden" name="ID" value="<?php echo $ID; ?>">
                            <input type="hidden" name="name" value="<?php echo $name; ?>">
                            <input type="hidden" name="level" value="<?php echo $level; ?>">
                            <?php
                            if ($neutral) {
                                ?>
                                <input type="hidden" name="neutral" value="1">
                                <?php
                            }
                            ?>

                            <button type="submit" name="deleted" value="1" class="btn btn-red">Sì, sono sicuro</button>
                        </form>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <?php
}
if ($_POST['deleted']) {

    $ID = $_POST['ID'];
    $name = $_POST['name'];
    $level = $_POST['level'];

    Building::delete($ID, $name, $level);
}




if ($ID_user) {
    /* ================================ Sezione centrale ================================ */
    ?>

    <!--    <div class="ischidados "> -->
    <div>
        <?php
        $navbar = new Start();
        $navbar->go();
       
        $sql = " SELECT * FROM buildings ORDER BY ID ASC";

        if ($ris = $conn->query($sql)) {
            ?> 
            <br>
            <div class="container">
                <div class="table-responsive">

                    <table class="table table-bordered table-hover table-striped table-dark table-responsive-xl ">
                        <thead>
                            <?php
                            $url = ($admin) ? "createBuilding.php" : "useBuildings.php";
                            $value = ($admin) ? "Nuovo edificio" : "Torna indietro";
                            ?>
                            <tr>
                        <form class="user" action="<?php echo $url; ?>" method="post">
                            <th colspan="11" style="text-align:center" class="p-3 mb-2 btn-black text-white">
                                <strong>
                                    <input class="btn btn-black btn-user btn-block" type="submit" value="<?php echo $value; ?>">
                                </strong>
                            </th>
                        </form>
                        </tr>
                        <tr>
                            <th colspan="11" style="text-align:center" class="p-3 mb-2 btn-black text-white"><strong>Elenco degli edifici</strong></th>
                        </tr>
                        <tr>
                            <th rowspan="2" style="text-align:center; vertical-align:middle;" width="1%">Nome</th>
                            <th rowspan="2" style="text-align:center; vertical-align:middle;" width="50%">Descrizione</th>
                            <th rowspan="2" style="text-align:center; vertical-align:middle;">Classe</th>
                            <th rowspan="2" style="text-align:center; vertical-align:middle;" width="5%">Num max Edifici</th>
                            <th rowspan="2" style="text-align:center; vertical-align:middle;" width="5%">Num max lavoratori</th>
                            <th colspan="5" style="text-align:center; vertical-align:middle;" width="10%">In costruzione</th>
                            <?php if ($admin) { ?>
                             <th rowspan="2" style="text-align:center; vertical-align:middle;" width="10%">Azioni</th>
                            <?php } ?>
                        </tr>  
                        <tr>   
                            <th style="text-align:center; vertical-align:middle;">Turni</th>
                            <th style="text-align:center; vertical-align:middle;">Cibo</th>
                            <th style="text-align:center; vertical-align:middle;">Utensili</th>
                            <th style="text-align:center; vertical-align:middle;">Armi</th>
                            <th style="text-align:center; vertical-align:middle;">Manovali</th>

                        </tr>
                        </thead>
                        <tbody>

                            <?php
                            $num = 0;
                            while ($row = $ris->fetch_assoc()) {

                                $ID = $row["ID"];
                                $name = $row["name"];
                                $description = $row["description"];
                                $level = $row["level"];
                                $class = $row["class"];
                                $numMax = $row["numMax"];
                                $underCostruction = $row["underCostruction"];
                                $food = ($row['food']) ? $row['food'] : " - ";
                                $tool = ($row['tool']) ? $row['tool'] : " - ";
                                $weapon = ($row['weapon']) ? $row['weapon'] : " - ";
                                $popCostruction = $row["popCostruction"];
                                $popMax = $row["popMax"];
                                $level = ($row["level"] == '0') ? "Livello unico" : "Liv. " . $row['level'];
                                if ($class == "defense") {
                                    $italian = "Difesa";
                                } elseif ($class == "production") {
                                    $italian = "Produzione";
                                } elseif ($class == "relations") {
                                    $italian = "Relazioni";
                                } elseif ($class == "hygiene") {
                                    $italian = " Igiene ";
                                } elseif ($class == "colonization") {
                                    $italian = " Colonizzazione ";
                                } elseif ($class == "m1" || $class == "m2" || $class == "m3") {
                                    $italian = " Meraviglia ";
                                } 
                                 

                                if ($num == 5) {
                                    ?>
                                    <tr>
                                        <th rowspan="2" style="text-align:center; vertical-align:middle;">ID</th>
                                        <th rowspan="2" style="text-align:center; vertical-align:middle;">Nome</th>
                                        <th rowspan="2" style="text-align:center; vertical-align:middle;">Descrizione</th>
                                        <th rowspan="2" style="text-align:center; vertical-align:middle;">Classe</th>
                                        <th rowspan="2" style="text-align:center; vertical-align:middle;">Num max Edifici</th>
                                        <th rowspan="2" style="text-align:center; vertical-align:middle;">Num max lavoratori</th>
                                        <th colspan="5" style="text-align:center; vertical-align:middle;">In costruzione</th>
                                        <?php if ($admin) { ?>
                                            <th rowspan="2" style="text-align:center; vertical-align:middle;">Azioni</th>
                                        <?php } ?>
                                    </tr>  
                                    <tr>   
                                        <th style="text-align:center; vertical-align:middle;">Turni</th>
                                        <th style="text-align:center; vertical-align:middle;">Cibo</th>
                                        <th style="text-align:center; vertical-align:middle;">Utensili</th>
                                        <th style="text-align:center; vertical-align:middle;">Armi</th>
                                        <th style="text-align:center; vertical-align:middle;">Manovali</th>

                                    </tr>
                                    <?php
                                    $num = 0;
                                }
                                ?>


                                <tr>
                                    <td style="text-align:center; vertical-align:middle;"><?php echo $ID; ?></td>
                                    <td style="text-align:center; vertical-align:middle;"><?php echo $name; ?><br><?php echo $level; ?></td>
                                    <td style="text-align:justify; vertical-align:middle;"><?php echo $description; ?></td>
                                    <td style="text-align:center; vertical-align:middle;"><?php echo $italian; ?></td>
                                    <td style="text-align:center; vertical-align:middle;"><?php echo $numMax; ?></td>
                                    <td style="text-align:center; vertical-align:middle;"><?php echo $popMax; ?></td>
                                    <td style="text-align:center; vertical-align:middle;"><?php echo $underCostruction; ?></td>
                                    <td style="text-align:center; vertical-align:middle;"><?php echo $food; ?></td>
                                    <td style="text-align:center; vertical-align:middle;"><?php echo $tool; ?></td>
                                    <td style="text-align:center; vertical-align:middle;"><?php echo $weapon; ?></td>
                                    <td style="text-align:center; vertical-align:middle;"><?php echo $popCostruction; ?></td>
                                    <?php if ($admin) { ?>
                                        <td style="text-align:center; vertical-align:middle;">
                                            <form class="kt-form" action="createBuilding.php" method="post">
                                                <input type="hidden" name="ID" value="<?php echo $ID ?>">
                                                <input type="hidden" name="modify" value="1">
                                                <button class="btn btn-warning btn-block">Modifica</button>
                                            </form>
                                            <form class="kt-form" action="listBuildings.php" method="post">
                                                <input type="hidden" name="ID" value="<?php echo $ID; ?>">
                                                <input type="hidden" name="name" value="<?php echo $name; ?>">
                                                <input type="hidden" name="level" value="<?php echo $level; ?>">
                                                <input type="hidden" name="toDelete" value="1">
                                                <button class="btn btn-red btn-block">Elimina</button>
                                            </form>
                                        </td>
                                    <?php } ?>
                                </tr>


                                <?php
                                $num++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
        }
        ?>

    </div>

    <?php
    /* ================================ Fine sezione centrale ================================ */
} else {
    header("location: login.php");
    $_SESSION['redirect'] = "listBuildings";
}

$footer = new Footer();
$footer->show("../../");

session_commit();
?>