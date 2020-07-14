<!doctype html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<link rel="icon" type="image/png" href="../images/128.png" />

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<link rel=stylesheet href="https://s3-us-west-2.amazonaws.com/colors-css/2.2.0/colors.min.css">

<link rel="stylesheet" type="text/css" href="../styles.css">


<script src="https://kit.fontawesome.com/d34481d57e.js"></script>
<title>Server creation</title>
<link rel="icon" type="image/png" href="images/128.png" />

<div class="container container-fluid">


  <?php
  session_start();
  if (!isset($_SESSION['login'])) {
    header('LOCATION:login.php');
    die();
  }
  header('Cache-Control: max-age=900');
  $role = $_SESSION["role"];
  if ($role != "Admin") {
    header('LOCATION:../admin.php');
  }
  ?>
  <h2>Sever creation</h2>

  <div class="row">
    <form action="createServer.php" autocomplete="off" method="post">
      Please enter the server name you want all apend (like S1 or like MyServerHere) : <br>
      <input type="text" name="ServerName"><br>
      <br><input class="btn btn-primary" type="submit" value="Create" name="Create" /></br>
    </form>
  </div>

</div>