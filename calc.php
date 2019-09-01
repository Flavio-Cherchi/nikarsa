<?php
include "./class/ConnessioneDB.php";
include "./class/Starter.php";
include "./class/Head.php";
include "./class/Footer.php";

$conn = connessione::start();
$head = new Head();
$head->show();

session_start();


$imagedata = base64_decode($_POST['imgdata']);
$filename = md5(uniqid(rand(), true));
//path where you want to upload image
$file = 'http://www.denai.it/projects/ischidados/img/screenshots/' . $filename . '.png';
$imageurl = 'http://www.denai.it/projects/ischidados/img/screenshots/' . $filename . '.png';
file_put_contents($file, $imagedata);
echo $imageurl;
?>

<!DOCTYPE html>
<html>
<head>
<title>Convert Div to image</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<script src="http://html2canvas.hertzen.com/build/html2canvas.js"></script>
</head>
<body>
<div id="mainDiv">
<h1>Developergang.com</h1>
<p>Welcome to Developergang.com</p>
<img src="logo.png" alt="Developer gang" />
</div>

<!-- Here add image url after ajax response -->
<div id="image_id">
<img src="" alt="image" />
</div>

<script>
html2canvas([document.getElementById('mainDiv')], {
    onrendered: function (canvas) {
        var imagedata = canvas.toDataURL('image/png');
		var imgdata = imagedata.replace(/^data:image\/(png|jpg);base64,/, "");
		//ajax call to save image inside folder
		$.ajax({
			url: 'calc.php',
			data: {
			       imgdata:imgdata
				   },
			type: 'post',
			success: function (response) {   
               console.log(response);
			   $('#image_id img').attr('src', response);
			}
		});
    }
    })
</script>
<?php

?>


</body>
</html>

<?php
/* ================================ Fine sezione centrale ================================ */


$footer = new Footer();
$footer->show("../../");

session_commit();
?>