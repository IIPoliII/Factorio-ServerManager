<!doctype html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<link rel="icon" type="image/png" href="images/128.png" />

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

  <link rel=stylesheet href="https://s3-us-west-2.amazonaws.com/colors-css/2.2.0/colors.min.css">

  <link rel="stylesheet" type="text/css" href="styles.css">


<script src="https://kit.fontawesome.com/d34481d57e.js"></script>
<title>Player Manager</title>
<link rel="icon" type="image/png" href="images/128.png" />
<script>
function refreshPage(){
    window.location.reload();
}
</script>
<?php
session_start();
            if(!isset($_SESSION['login'])) {
              header('LOCATION:login.php'); die();
            }
	header('Cache-Control: max-age=900');



$user=$_SESSION["user"];
$serverpath = $_GET['serverpath'];
$server = $_GET['serverchoose'];

$Players = `./scripts/playerManager/generateHTML.sh $serverpath 127.0.0.1 $user`;
	echo '<div class="conatiner container-fluid col-sm-12">';
	echo '<table class="table" id="PlayerManagerMainTable"><tr><td>';
        echo '<div class="float-right">';
        echo '<a href="';
        echo "scripts/launchAdminLoading.php?serverchoose=${server}";
        echo '" class="button" style="color:black;"><i class="fas fa-home fa-2x"></i></span></a>';
	echo '<button type="submit" style="background: none; color: inherit; border: none; font: inherit; outine: inherit;" onClick="refreshPage()"><i class="fas fa-sync fa-2x"></i></button>';
	echo '</div>';
echo "$Players";

echo '</table></div>'

?>
