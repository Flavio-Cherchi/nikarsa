<?php
include "./class/ConnessioneDB.php";
include "./class/Starter.php";
include "./class/Head.php";
include "./class/Footer.php";

$conn = connessione::start();
$head = new Head();
$head->show();

session_start();

//var_dump($_SESSION['redirect']);

if ($_SESSION['error']) {
    $error = $_SESSION['error'];
}

if ($_SESSION['ID_user']) {
    $_SESSION['ID_user'] = NULL;
    $_SESSION['username'] = NULL;
    $_SESSION['password'] = NULL;
    $_SESSION['admin'] = NULL;
}

session_commit();

/* ================================ Sezione centrale ================================ */
?>
<body>
    <div class="">

        <?php
        $navbar = new Start();
        $navbar->go();
        ?>

        <div class="container">
            <!-- Outer Row -->
            <div class="row justify-content-center">
                <div class="col-xl-10 col-lg-12 col-md-9">
                        <div class="card-body p-0">
                            <!-- Nested Row within Card Body -->
                            <div class="row">
                                <div class="col-lg-2"></div>
                                <div class="col-lg-8">
                                    <div class="p-5">

                                        <!-- temporaneo!!! -->
                                        <form action="index.php" method="post">
                                            <input type="hidden" class="form-control" name="logging" value="1">
                                            <input type="hidden" class="form-control" name="loginName" value="admin">
                                            <input type="hidden" class="form-control" name="loginPassord" value="admin">
                                            <button type="submit" class="btn-user btn-warning center">(temporaneo) entra come admin</button>
                                        </form>
                                                                                <form action="index.php" method="post">
                                            <input type="hidden" class="form-control" name="logging" value="1">
                                            <input type="hidden" class="form-control" name="loginName" value="kataskematico">
                                            <input type="hidden" class="form-control" name="loginPassord" value="kataskematico">
                                            <button type="submit" class="btn-user btn-warning center">(temporaneo) entra come utente</button>
                                        </form>



                                        <form action="index.php" method="post">
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="loginName" placeholder="Nome utente">
                                            </div>
                                            <div class="form-group">
                                                <input type="password" class="form-control" name="loginPassord" placeholder="Password">
                                            </div>
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox small">
                                                  <!--<input type="checkbox" class="custom-control-input" id="customCheck">
                                                   <label class="custom-control-label" for="customCheck">Remember Me</label>  -->
                                                </div>
                                            </div>
                                            <input type="hidden" class="form-control" name="logging" value="1">
                                            <button type="submit" class="btn-user btn-black center">Login</button>
                                            <div class="text-center">
                                                <a class="small" href="userRegister.php">Crea un account!</a>
                                            </div>
                                        </form>
                                        <?php
                                        if ($error) {
                                            ?>
                                            <div class="form-group center">
                                                <p style="color: red;">Nome utente o password errati</p>
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
</body>


<?php
/* ================================ Fine sezione centrale ================================ */

$footer = new Footer();
$footer->show("../../");
?>