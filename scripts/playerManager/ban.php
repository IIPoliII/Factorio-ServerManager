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
  <button class="btn btn-primary" onclick="goBack()"><i class="fas fa-step-backward"></i>Go back</button>
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

          $user = $_GET['user'];
          $reason = $_GET['reason'];
          $player = $_GET['player'];

          echo "<title>Baned ${player}</title>";

          $RconPort = `grep -oP "rcon-port\K.*" "${serverpath}/ServerManager/.env" | sed 's/"//g' | cut -c 2- | grep -o '^\S*'`;
          $RconPassword = `grep -oP "rcon-password\K.*" "${serverpath}/ServerManager/.env" | sed 's/"//g' | cut -c 2- | sed 's/\s.*$//'`;
          $RconPort = (int) $RconPort;
          $DiscordUrl = `sed -n '8p' ../../config.txt | sed 's/"//g' | cut -c 19-`;
          $DiscordUrl = substr($DiscordUrl, 0, -1);


          $DBServer = `sed -n '2p' ../../config.txt | sed 's/"//g' | cut -c 9-`;
          $DBServer = substr($DBServer, 0, -1);
          $DBPort = `sed -n '3p' ../../config.txt | sed 's/"//g' | cut -c 11-`;
          $DBPort = substr($DBPort, 0, -1);
          $DBUser = `sed -n '4p' ../../config.txt | sed 's/"//g' | cut -c 11-`;
          $DBUser = substr($DBUser, 0, -1);
          $DBPassword = `sed -n '5p' ../../config.txt | sed 's/"//g' | cut -c 10-`;
          $DBPassword = substr($DBPassword, 0, -1);
          $DB = `sed -n '1p' ../../config.txt | sed 's/"//g' | cut -c 9-`;
          $DB = substr($DB, 0, -1);

          $SendCommand = `./mcrcon -H 127.0.0.1 -P $RconPort -p $(echo $RconPassword) "/ban $player You have been banned by $user for $reason please go on discord.joinandplaycoop.com to revoke your ban"`;

          $SendDiscord = `./discord.sh "$player has been banned on all servers by $user for $reason" "$DiscordUrl"`;


          $IdPlayer = `mysql -sN --user=$(echo $DBUser) --password=$(echo $DBPassword) --host $(echo $DBServer) -P $(echo $DBPort) --database=$(echo $DB) -e 'SELECT Id FROM Players WHERE PlayerName = "'$player'"'`;

          // Create connection
          $conn = new mysqli($DBServer, $DBUser, $DBPassword, $DB, $DBPort);

          // Check connection
          if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
            echo "Connection to the database failed";
          }


          if ($IdPlayer != "") {
            $IdBan = `mysql -sN --user=$(echo $DBUser) --password=$(echo $DBPassword) --host $(echo $DBServer) -P $(echo $DBPort) --database=$(echo $DB) -e "SELECT Id FROM Ban WHERE FKPlayerId = ${IdPlayer}"`;

            if ($IdBan != '') {
              echo "User was already banned changed the reason<br>";
              $UpdateBan = 'UPDATE Ban
			      SET Reason = "' . $reason . '"
			      WHERE Id = "' . $IdBan . '"';

              if ($conn->query($UpdateBan) === TRUE) {
                echo "New record created successfully<br>";
              } else {
                echo "Error: " . $UpdateBan . "<br>" . $conn->error;
              }
            } else {
              echo "User wasn't banned adding him and the reason to the database !<br>";
              $InsertBan = 'INSERT INTO Ban (FKPlayerId, Reason) VALUES ("' . $IdPlayer . '", "' . $reason . '")';

              if ($conn->query($InsertBan) === TRUE) {
                echo "New record created successfully<br>";
              } else {
                echo "Error: " . $InsertBan . "<br>" . $conn->error;
              }
            }
          }  else {
            $InsertPlayer = 'INSERT INTO Players (PlayerName, IsAdmin) VALUES ("' . $player . '", "0")';

            if ($conn->query($InsertPlayer) === TRUE) {
              echo "New record created successfully<br>";
            } else {
              echo "Error: " . $InsertPlayer . "<br>" . $conn->error;
            }

            $IdPlayer = `mysql -sN --user=$(echo $DBUser) --password=$(echo $DBPassword) --host $(echo $DBServer) -P $(echo $DBPort) --database=$(echo $DB) -e 'SELECT Id FROM Players WHERE PlayerName = "'$player'"'`;

            $IdBan = `mysql -sN --user=$(echo $DBUser) --password=$(echo $DBPassword) --host $(echo $DBServer) -P $(echo $DBPort) --database=$(echo $DB) -e "SELECT Id FROM Ban WHERE FKPlayerId = ${IdPlayer}"`;

            if ($IdBan != '') {
              echo "User was already banned changed the reason<br>";
              $UpdateBan = 'UPDATE Ban
			      SET Reason = "' . $reason . '"
			      WHERE Id = "' . $IdBan . '"';

              if ($conn->query($UpdateBan) === TRUE) {
                echo "New record created successfully<br>";
              } else {
                echo "Error: " . $UpdateBan . "<br>" . $conn->error;
              }
            } else {
              echo "User wasn't banned adding him and the reason to the database !<br>";
              $InsertBan = 'INSERT INTO Ban (FKPlayerId, Reason) VALUES ("' . $IdPlayer . '", "' . $reason . '")';

              if ($conn->query($InsertBan) === TRUE) {
                echo "New record created successfully<br>";
              } else {
                echo "Error: " . $InsertBan . "<br>" . $conn->error;
              }
            }
          }

          $conn->close();

          echo "Normally $player has been baned out ! <br>";

          echo "Output of the command :<br> $SendCommand<br>";
            
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