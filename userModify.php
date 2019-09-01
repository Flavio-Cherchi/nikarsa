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
    $admin = $_SESSION['admin'];
}

if ($admin) {
    $adminChange = 1;
}

if ($_POST['toModify']) {
    $ID_toModify = $_POST['toModify'];
} else {
    $ID_toModify = $ID_user;
}

$sql = " SELECT * FROM users WHERE ID = $ID_toModify; ";
$ris = $conn->query($sql);
$row = $ris->fetch_assoc();
$username = $row['username'];
$password = $row['password'];
//$adminOld = $row['admin'];
//$activeOld = $row['active'];

if ($_POST['modify']) {

    $modify = $_POST['modify'];
    $username = $_POST['username'];
    $pass1 = $_POST['password1'];
    $pass2 = $_POST['password2'];

    if ($pass1 == $pass2) {
        $sql = "SELECT count(ID) as conta FROM users WHERE username = '$username'";
        $ris = $conn->query($sql);
        $row = $ris->fetch_assoc();
        if ($row['conta'] > 1) {
            $error2 = 1;
        } else {
            $password = sha1($pass1);
            $sql = " UPDATE users SET username = '$username', password = '$password', active = '0', admin = '0' WHERE ID = $modify; ";
            $ris = $conn->query($sql);
            $done = 1;
            // header("refresh:3;url=login.php");
        }
    } else {
        $error1 = 1;
    }
}

/* ================================ Sezione centrale ================================ */
?>

<div class="ischidados">

    <?php
    $navbar = new Start();
    $navbar->go();
    ?>

    <div class="container">
        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-2"></div>
                            <div class="col-lg-8">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Modifica profilo: <?php echo $username; ?></h1>
                                    </div>
                                    <?php
                                    if ($adminChange && (!$_POST['modifyAdmin'])) {
                                        ?>
                                        <form class="user" action="userModify.php" method="post">
                                            <div class="form-group">
                                                <span>Scegli l'utente da modificare</span>
                                                <select name="toModify" class="form-control">

                                                    <?php
                                                    $sqlturn = "SELECT users.ID, username FROM users WHERE admin = 0 AND username <> 'neutral' ";

                                                    if ($ris = $conn->query($sqlturn)) {
                                                        while ($row = $ris->fetch_assoc()) {
                                                            ?> 
                                                            <option value="<?php echo $row["ID"] ?>">  <?php echo $row["username"] ?> </option>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <input type="hidden" name="modifyAdmin" value="1">
                                            <input class="btn btn-black btn-user btn-block" type="submit" value="Invia">
                                            <br>
                                        </form>
                                        <?php
                                    } else {
                                        ?>
                                        <form action="userModify.php" method="post">
                                            <div class="form-group">
                                                <input type="text" class="form-control form-control-user" name="username" value="<?php echo $username; ?>">
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-6 mb-3 mb-sm-0">
                                                    <input type="password" class="form-control form-control-user" name="password1" value="<?php echo $password; ?>" placeholder="nuova password">
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="password" class="form-control form-control-user" name="password2" value="<?php echo $password; ?>" placeholder="reinserisci nuova password">
                                                </div>
                                            </div>
                                            <input type="hidden" name="modify" value="<?php echo $ID_toModify; ?>">
                                            <input type="hidden" name="admin" value="<?php echo $admin; ?>">
                                            <input type="hidden" name="active" value="<?php echo $active; ?>">
                                            <button type="submit" class="btn btn-black btn-user btn-block">Modifica</button>
                                        </form>
                                        <?php
                                    }
                                    ?>

                                    <?php
                                    if ($error1) {
                                        ?>
                                        <div class="form-group center">
                                            <p style="color: red;">Le password non coincidono</p>
                                        </div>

                                        <?php
                                    }
                                    if ($error2) {
                                        ?>
                                        <div class="form-group center">
                                            <p style="color: red;">Account già presente in database</p>
                                        </div>

                                        <?php
                                    }
                                    if ($done) {
                                        ?>
                                        <div class="form-group center">
                                            <p style="color: green;">Modifiche effettuate. Verrai indirizzato a breve alla pagina di login</p>
                                        </div>

                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="col-lg-2"></div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>



<?php
/* ================================ Fine sezione centrale ================================ */

$footer = new Footer();
$footer->show("../../");

?>