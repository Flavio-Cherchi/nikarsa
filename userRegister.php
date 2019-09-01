<?php
include "./class/ConnessioneDB.php";
include "./class/Starter.php";
include "./class/Head.php";
include "./class/Footer.php";

$conn = connessione::start();
$head = new Head();
$head->show();

session_start();


if ($_POST['registration']) {
    $username = $_POST['username'];
    $pass1 = $_POST['password1'];
    $pass2 = $_POST['password2'];

    if ($pass1 == $pass2) {
        $sql = "SELECT ID FROM users WHERE username = '$username'";
        $ris = $conn->query($sql);
        $row = $ris->fetch_assoc();
            if($row['ID']){
            $error2 = 1; 
        } else {
            $password = sha1(strtolower($pass1));
            $sql = " INSERT INTO users (username, password, admin, active) VALUES ('$username', '$password', 0, 0); ";
            $ris = $conn->query($sql);
            $done = 1;
            header( "refresh:3;url=login.php" );
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

                <div class="card o-hidden border-0 shadow-lg my-5 imgRadiusDark">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-2"></div>
                            <div class="col-lg-8">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Crea un nuovo account!</h1>
                                    </div>
                                    <form action="userRegister.php" method="post">
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-user" name="username" placeholder="Username">
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-6 mb-3 mb-sm-0">
                                                <input type="text" class="form-control form-control-user" name="password1" placeholder="Password">
                                            </div>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control form-control-user" name="password2" placeholder="Ripeti password">
                                            </div>
                                        </div>
                                        <input type="hidden" name="registration" value="1">
                                        <button type="submit" class="btn btn-black btn-user btn-block">Registrati con un nuovo account</button>
                                    </form>
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
                                            <p style="color: green;">Account registrato. Verrai indirizzato a breve alla pagina di login</p>
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