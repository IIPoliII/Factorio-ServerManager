<!doctype html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<link rel="icon" type="image/png" href="../../images/128.png" />

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<link rel=stylesheet href="https://s3-us-west-2.amazonaws.com/colors-css/2.2.0/colors.min.css">

<link rel="stylesheet" type="text/css" href="../../styles.css">
<div class="container container-fluid">
  <button class="btn btn-primary" onclick="goBack()">Go Back</button>
  <table border="1" id="LogTable" class="table">
    <tr>
      <td>
        <div id="feed">
          <?php
          session_start();
          if (!isset($_SESSION['login'])) {
            header('LOCATION:../../login.php');
            die();
          }

          $serverpath = $_GET['serverpath'];

          $serverpath = "../../$serverpath";

          $player = $_GET['player'];

          echo "<title>Kicked ${player}</title>";

          $RconPort = `grep -oP "rcon-port\K.*" "${serverpath}/ServerManager/.env" | sed 's/"//g' | cut -c 2- | grep -o '^\S*'`;
          $RconPassword = `grep -oP "rcon-password\K.*" "${serverpath}/ServerManager/.env" | sed 's/"//g' | cut -c 2- | sed 's/\s.*$//'`;
          $RconPort = (int) $RconPort;

          echo "$player is being kicked.....<br><br>";


          $SendCommand = `./mcrcon -H 127.0.0.1 -P $RconPort -p $(echo $RconPassword) "/kick $player You have been kicked for one of the following reason : griefing, cheating, annoying pepoles, insulting,... please go on discord.joinandplaycoop.com"`;

          echo "Normally $player has been kicked out !<br>";

          ?>

        </div>
      </td>
    </tr>
  </table>
  <script>
    function goBack() {
      window.history.back();
    }
  </script>
</div>