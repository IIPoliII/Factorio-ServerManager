<?php
session_start();
echo isset($_SESSION['login']);
if (isset($_SESSION['login'])) {
  header('LOCATION:admin.php');
  die();
}
?>
<!DOCTYPE html>


<html>

<head>
  <meta http-equiv='content-type' content='text/html;charset=utf-8' />
  <title>Login</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fork-awesome@1.1.7/css/fork-awesome.min.css" integrity="sha256-gsmEoJAws/Kd3CjuOQzLie5Q3yshhvmo7YNtBG7aaEY=" crossorigin="anonymous">
  <link rel="icon" type="image/png" href="images/128.png" />
  <title>Login - JAPC Web management panel</title>
  <link rel="stylesheet" type="text/css" href="styles.css">
</head>

<body>
  <div class="container">
    <div style="display: flex; justify-content: center;">
      <img src="images/128.png" alt="Join and play coop logo" align="middle">
    </div>
    <h3 class="text-center">JAPC Web Management panel login</h3>
    <?php
    if (isset($_POST['submit'])) {
      $username = $_POST['username'];
      $password = $_POST['password'];

      $handle = fopen("user.csv", "r");

      while (($data = fgetcsv($handle)) !== FALSE) {
        if ($data[0] == $username && $data[1] == $password) {
          $success = true;
          $_SESSION["user"] = $data[0];
          $_SESSION["role"] = $data[2];
          break;
        }
      }

      fclose($handle);

      if ($success) {
        $_SESSION['login'] = true;
        header('LOCATION:scripts/launchAdminLoading.php');
        die();
      } else {
        echo "<div class='alert alert-danger'>Username and Password do not match.</div>";
      }
    }
    ?>
    <form action="" method="post">
      <div class="form-group">
        <label for="username">Username:</label>
        <input type="text" class="form-control" id="username" name="username" required>
      </div>
      <div class="form-group">
        <label for="pwd">Password:</label>
        <input type="password" class="form-control" id="pwd" name="password" required>
      </div>
      <button type="submit" name="submit" class="btn btn-default">Login</button>
      <a href="scripts/discordLogin.php" class="discord-logo-btn btn btn-default"><img src="images/discord-logo.png" style="width:17px;height:17px;" class="discord-logo-btn fa-discord"> Discord Login</a>
    </form>
  </div>
</body>

</html>