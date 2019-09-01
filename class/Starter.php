<?php
session_start();

class Start {

    public function __construct() {
        
    }

    public static function go() {

        $conn = connessione::start();


        if ($_SESSION['ID_user']) {
            $ID_user = $_SESSION['ID_user'];
            $username = $_SESSION['username'];
            $admin = $_SESSION['admin'];
        }


        //Log system
        if ($ID_user) {
            $log = ucfirst($username) . " - Logout";
        } else {
            $log = "Login";
        }


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

        $home = ($admin) ? "Gestione master" : "Scheda personale";
        /* ------------------------- End select actual game ------------------------- */
        ?>

        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="index.php"> Ischidados - turno <?php echo $numTurn; ?> </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarText">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link right" href="index.php"><?php echo $home; ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link right" href="listCommunities.php">Comunità</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link right" href="characters.php">Personaggi</a>
                        </li>
                        <?php 
                        if(!$admin){  
                        ?>
                        <li class="nav-item">
                            <a class="nav-link right" href="relations.php">D&C</a>
                        </li>
                       <?php 
                        }
                        ?>
                        <li class="nav-item">
                            <a class="nav-link right" href="outside.php">Simulatore</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link right" href="listEvents.php">Eventi</a>
                        </li>
                        <?php
                        if ($admin) {
                            ?>
                            <!--
                                <li class="nav-item">
                                    <a class="nav-link right" href="adminPanel.php">Gestione partita</a>
                                </li>
                            -->
                            <?php
                        }
                        ?>
                    </ul>
                    <form action="login.php" method="post">
                        <input type="hidden" name="carica" value="1"><br>
                        <a class="nav-link right" href="login.php"><?php echo $log; ?></a>
                    </form>
                </div>
        </nav>
        <?php
    }

}

session_commit();
