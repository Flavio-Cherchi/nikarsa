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


if ($_POST['simulation']) {
    $numchar1 = $_POST['numChar1'];
    $level1 = $_POST['level1'];
    $population1 = $_POST['population1'];
    $pViolence1 = $_POST['pViolence1'];
    $food1 = $_POST['food1'];
    $clean1 = $_POST['clean1'];

    if (!$_POST['zombie']) {
        $numchar2 = $_POST['numChar2'];
        $level2 = $_POST['level2'];
        $population2 = $_POST['population2'];
        $pViolence2 = $_POST['pViolence2'];
        $food2 = $_POST['food2'];
        $clean2 = $_POST['clean2'];
    }
    $population2 = $_POST['population2'];

    $simulation = new Fight;
    $res1 = $simulation->simulation($numchar1, $level1, $population1, $pViolence1, $food1, $clean1, 0);
    if (!$_POST['zombie']) {
        $res2 = $simulation->simulation($numchar2, $level2, $population2, $pViolence2, $food2, $clean2, 0);
    } else {
        $res2 = $simulation->simulation(0, 0, $population2, 0, 0, 0, 1);
    }

    $fortune1 = ($res1 / 100) * rand(1, 5);
    $fortune2 = ($res2 / 100) * rand(1, 5);
    $outcome = ($res1 + $fortune1) - ($res2 + $fortune2);

    if ($res1 > ($res2 * 5)) {
        //echo "quintuplo!";
        $dec1a = 0;
        $dec1b = floor($population1 / 100 * 5);
        $dec2a = floor($population2 / 100 * 95);
        $dec2b = $population2;
    } elseif ($res1 > ($res2 * 2)) {
        //echo "doppio!";
        $dec1a = floor($population1 / 100 * 10);
        $dec1b = floor($population1 / 100 * 20);
        $dec2a = floor($population2 / 100 * 80);
        $dec2b = floor($population2 / 100 * 90);
    } elseif ($res1 > $res2) {
        //echo "più grande!";
        $dec1a = floor($population1 / 100 * 20);
        $dec1b = floor($population1 / 100 * 50);
        $dec2a = floor($population2 / 100 * 50);
        $dec2b = floor($population2 / 100 * 80);
    } elseif ($res1 == $res2) {
        //echo "uguale!";
        $dec1a = floor($population1 / 100 * 70);
        $dec1b = floor($population1 / 100 * 90);
        $dec2a = floor($population2 / 100 * 70);
        $dec2b = floor($population2 / 100 * 90);
    } elseif ($res2 > $res1) {
        //echo "più grande!";
        $dec2a = floor($population1 / 100 * 20);
        $dec2b = floor($population1 / 100 * 50);
        $dec1a = floor($population2 / 100 * 50);
        $dec1b = floor($population2 / 100 * 80);
    } elseif ($res2 > ($res1 * 2)) {
        //echo "doppio!";
        $dec2a = floor($population1 / 100 * 10);
        $dec2b = floor($population1 / 100 * 20);
        $dec1a = floor($population2 / 100 * 80);
        $dec1b = floor($population2 / 100 * 90);
    } elseif ($res2 > ($res1 * 5)) {
        //echo "quintuplo!";
        $dec2a = 0;
        $dec2b = floor($population1 / 100 * 5);
        $dec1a = floor($population2 / 100 * 95);
        $dec1b = $population2;
    }

    $died1 = rand($dec1a, $dec1b);
    $died2 = rand($dec2a, $dec2b);

    if ($outcome <= 0) {
        $winner = "Il difensore";
        $italian0 = "per il difensore";
        $loser = "L'attaccante";
        if ($died2 == 0) {
            $italian1 = "senza subire perdite. ";
        } elseif ($died2 == 1) {
            $italian1 = "perdendo un solo uomo.";
        } else {
            $italian1 = "perdendo " . $died2 . " uomini.";
        }

        if ($died1 = $population1) {
            $italian3 = " ed è stato sterminato.";
        } else {
            $italian3 = " ed ha perso " . $died1 . " uomini.";
        }
    } else {
        $winner = "L'attaccante";
        $loser = "Il difensore";
        $italian0 = "per l'attaccante";
        if ($died1 == 0) {
            $italian1 = "senza subire perdite. ";
        } elseif ($died1 == 1) {
            $italian1 = "perdendo un solo uomo";
        } else {
            $italian1 = "perdendo " . $died1 . " uomini.";
        }

        if ($died2 == $population1) {
            $italian3 = " ed è stato sterminato.";
        } else {
            $italian3 = " ed ha perso " . $died2 . " uomini.";
        }
    }
    $tagOutcome = 1;
    $msg = $winner . " ha vinto, " . $italian1 . " " . $loser . " ha subito una cocente sconfitta " . $italian3;
}



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
        <?php ?>
        <div class="container">

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
                                    <div class="p-0">
                                        <?php
                                        if (!$_POST['war']) {
                                            ?>
                                            <div class="text-center">
                                                <h1 class="h4 text-gray-900 mb-4">Simulatore battaglie</h1>
                                            </div>
                                            <center><img class="elencoImg" src="img/fight.jpg" alt="trader"></center>
                                            <br>
                                            <?php
                                            if (!$_POST['zombie'] && !$_POST['men']) {
                                                ?>
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <form class="user" action="outside.php" method="post">
                                                            <input type="hidden" name="men" value="1">
                                                            <input class="btn btn-black btn-user btn-block" type="submit" value="Tra esseri umani">
                                                        </form>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <form class="user" action="outside.php" method="post">
                                                            <input type="hidden" name="zombie" value="1">
                                                            <input class="btn btn-black btn-user btn-block" type="submit" value="Contro zombie">
                                                        </form>
                                                    </div>
                                                </div>
                                                <?php
                                            } elseif ($_POST['men']) {
                                                $men = $_POST['men'];
                                                if (!$tagOutcome) {
                                                    ?>

                                                    <form class="user" action="outside.php" method="post">
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <center><h5>Attaccanti</h5>
                                                                    <div class="form-group">
                                                                        <input type="number" name="numChar1" value="0" min="0" max="3"><br>Personaggi violenti
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <input type="number" name="level1" value="0" min="0"><br>Livello max personaggi violenti
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <input type="number" name="population1" value="0" min="0"><br>Popolazione armata
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <input type="number" name="pViolence1" value="0" min="0"><br>Punti violenza impiegati
                                                                    </div>
                                                                    <select name="food1" class="form-group">
                                                                        <option value="1">Denutriti</option>
                                                                        <option value="2">Mezza razione</option>
                                                                        <option value="3" selected>Sazi</option>
                                                                    </select>
                                                                    <select name="clean1" class="form-group">
                                                                        <option value="1">Nessuna igiene</option>
                                                                        <option value="2">Poca igiene</option>
                                                                        <option value="3" >Moderata igiene</option>
                                                                        <option value="4" selected>Igiene totale</option>
                                                                    </select>
                                                                </center>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <center><h5>Difensori</h5>
                                                                    <div class="form-group">
                                                                        <input type="number" name="numChar2" value="0" min="0" max="3"><br>Personaggi violenti
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <input type="number" name="level2" value="0" min="0"><br>Livello max personaggi violenti
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <input type="number" name="population2" value="0" min="0"><br>Popolazione armata
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <input type="number" name="pViolence2" value="0" min="0"><br>Punti violenza impiegati
                                                                    </div>
                                                                    <select name="food2" class="form-group">
                                                                        <option value="1">Denutriti</option>
                                                                        <option value="2">Mezza razione</option>
                                                                        <option value="3" selected>Sazi</option>
                                                                    </select>
                                                                    <select name="clean2" class="form-group">
                                                                        <option value="1">Nessuna igiene</option>
                                                                        <option value="2">Poca igiene</option>
                                                                        <option value="3" >Moderata igiene</option>
                                                                        <option value="4" selected>Igiene totale</option>
                                                                    </select>
                                                                </center>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="simulation" value="1">
                                                        <input type="hidden" name="men" value="1">
                                                        <input class="btn btn-black btn-user btn-block" type="submit" value="Simula battaglia">
                                                    </form>
                                                    <form class="user" action="outside.php" method="post">
                                                        <div>
                                                            <input type="hidden" name="men" value="0">
                                                            <input type="hidden" name="zombie" value="0">
                                                            <input class="btn btn-black btn-user btn-block" type="submit" value="Torna indietro">
                                                        </div>
                                                    </form>

                                                    <?php
                                                } else {
                                                    ?>
                                                    <table class="table table-bordered table-hover table-striped table-dark father">
                                                        <thead>

                                                            <tr>
                                                                <th colspan="3" style="text-align:center" class="p-3 mb-2 btn-black text-white"><strong>Esito battaglia</strong></th>
                                                            </tr>
                                                            <tr>
                                                                <th width="5%"></th>
                                                                <th style="text-align:center; vertical-align:middle;" width="10%">Attaccante</th>
                                                                <th style="text-align:center; vertical-align:middle;" width="10%">Difensore</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td style="text-align:center; vertical-align:middle;"><b>Punteggio</b>
                                                                </td>
                                                                <td style="text-align:center; vertical-align:middle;"><?php echo $res1 + $fortune1; ?>
                                                                </td>
                                                                <td style="text-align:center; vertical-align:middle;"><?php echo $res2 + $fortune2; ?>
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
                                                                <td colspan="3" style="text-align:center; vertical-align:middle;"><b>Vittoria <?php echo $italian0; ?> </b>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3" style="text-align:center; vertical-align:middle;"><?php echo $msg; ?>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <script>
                                                        function RefreshWindow()
                                                        {
                                                            window.location.reload(true);
                                                        }
                                                    </script>
                                                    <center>
                                                        <div class="row">
                                                            <div class="col-lg-4">
                                                                <form class="user" action="outside.php" method="post">
                                                                    <input type="hidden" name="men" value="1">
                                                                    <input autofocus="autofocus" class="btn btn-black btn-block"  type="button" value="Ripeti la battaglia" onclick="return RefreshWindow();"/>
                                                                </form>

                                                            </div>
                                                            <div class="col-lg-4">
                                                                <form class="user" action="outside.php" method="post">
                                                                    <input type="hidden" name="men" value="1">
                                                                    <input class="btn btn-black btn-user btn-block" type="submit" value="Simula un'altra battaglia">
                                                                </form>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <form class="user" action="outside.php" method="post">
                                                                    <input type="hidden" name="zombie" value="1">
                                                                    <input class="btn btn-black btn-user btn-block" type="submit" value="Simula battaglia contro zombie">
                                                                </form>
                                                            </div>
                                                        </div>


                                                    </center>
                                                    <?php
                                                }
                                            } elseif ($_POST['zombie']) {
                                                $zombie = $_POST['zombie'];

                                                if (!$tagOutcome) {
                                                    ?>

                                                    <form class="user" action="outside.php" method="post">
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <center><h5>Esseri umani</h5>
                                                                    <div class="form-group">
                                                                        <input type="number" name="numChar1" value="0" min="0" max="3"><br>Personaggi violenti
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <input type="number" name="level1" value="0" min="0"><br>Livello max personaggi violenti
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <input type="number" name="population1" value="0" min="0"><br>Popolazione armata
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <input type="number" name="pViolence1" value="0" min="0"><br>Punti violenza impiegati
                                                                    </div>
                                                                    <select name="food1" class="form-group">
                                                                        <option value="1">Denutriti</option>
                                                                        <option value="2">Mezza razione</option>
                                                                        <option value="3" selected>Sazi</option>
                                                                    </select>
                                                                    <select name="clean1" class="form-group">
                                                                        <option value="1">Nessuna igiene</option>
                                                                        <option value="2">Poca igiene</option>
                                                                        <option value="3" >Moderata igiene</option>
                                                                        <option value="4" selected>Igiene totale</option>
                                                                    </select>
                                                                </center>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <center><h5>Zombie</h5>
                                                                    <div class="form-group">
                                                                        <input type="number" name="population2" value="0" min="0"><br>Zombie
                                                                    </div>
                                                                </center>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="simulation" value="1">
                                                        <input type="hidden" name="zombie" value="1">
                                                        <input class="btn btn-black btn-user btn-block" type="submit" value="Simula battaglia">
                                                    </form>
                                                    <br>

                                                    <form class="user" action="outside.php" method="post">
                                                        <div>
                                                            <input type="hidden" name="men" value="0">
                                                            <input type="hidden" name="zombie" value="0">
                                                            <input class="btn btn-black btn-user btn-block" type="submit" value="Torna indietro">
                                                        </div>
                                                    </form>

                                                    <?php
                                                } else {
                                                    ?>
                                                    <table class="table table-bordered table-hover table-striped  table-dark father">
                                                        <thead>

                                                            <tr>
                                                                <th colspan="3" style="text-align:center" class="p-3 mb-2 btn-black text-white"><strong>Esito scontro</strong></th>
                                                            </tr>
                                                            <tr>
                                                                <th width="5%"></th>
                                                                <th style="text-align:center; vertical-align:middle;" width="10%">Umani</th>
                                                                <th style="text-align:center; vertical-align:middle;" width="10%">Zombie</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td style="text-align:center; vertical-align:middle;"><b>Punteggio</b>
                                                                </td>
                                                                <td style="text-align:center; vertical-align:middle;"><?php echo $res1 + $fortune1; ?>
                                                                </td>
                                                                <td style="text-align:center; vertical-align:middle;"><?php echo $res2 + $fortune2; ?>
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
                                                                <td colspan="3" style="text-align:center; vertical-align:middle;"><b>Vittoria <?php echo $italian0; ?> </b>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3" style="text-align:center; vertical-align:middle;"><?php echo $msg; ?>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>

                                                    <script>
                                                        function RefreshWindow()
                                                        {
                                                            window.location.reload(true);
                                                        }
                                                    </script>
                                                    <center>
                                                        <div class="row">
                                                            <div class="col-lg-4">
                                                                <form class="user" action="outside.php" method="post">
                                                                    <input type="hidden" name="zombie" value="1">
                                                                    <input autofocus="autofocus" class="btn btn-black btn-block"  type="button" value="Ripeti lo scontro" onclick="return RefreshWindow();"/>
                                                                </form>

                                                            </div>
                                                            <div class="col-lg-4">
                                                                <form class="user" action="outside.php" method="post">
                                                                    <input type="hidden" name="zombie" value="1">
                                                                    <input class="btn btn-black btn-user btn-block" type="submit" value="Simula un altro scontro">
                                                                </form>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <form class="user" action="outside.php" method="post">
                                                                    <input type="hidden" name="men" value="0">
                                                                    <input type="hidden" name="zombie" value="0">
                                                                    <input class="btn btn-black btn-user btn-block" type="submit" value="Torna indietro">
                                                                </form>
                                                            </div>
                                                        </div>


                                                    </center>
                                                    <?php
                                                }
                                            }
                                            ?>

                                            <?php
                                        } else {
                                            
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