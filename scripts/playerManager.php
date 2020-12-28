<!doctype html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<link rel="icon" type="image/png" href="../images/128.png" />

<link rel=stylesheet href="https://s3-us-west-2.amazonaws.com/colors-css/2.2.0/colors.min.css">

<link rel="stylesheet" type="text/css" href="../styles.css">


<script src="https://kit.fontawesome.com/d34481d57e.js"></script>
<title>Player Manager</title>
<link rel="icon" type="image/png" href="../images/128.png" />
<script>
  function refreshPage() {
    window.location.reload();
  }
</script>
<?php
session_start();
if (!isset($_SESSION['login'])) {
  header('LOCATION:login.php');
  die();
}
header('Cache-Control: max-age=900');


//get all info to connect the server
$user = $_SESSION["user"];
$serverpath = $_GET['serverpath'];
$server = $_GET['serverchoose'];

//create a container, refresh button, home button
echo '<div class="conatiner container-fluid col-sm-12">';
echo '<table class="table" id="PlayerManagerMainTable"><tr><td>';
echo '<div class="float-right">';
echo '<a href="';
echo "launchAdminLoading.php?serverchoose=${server}";
echo '" class="button" style="color:black;"><i class="fas fa-home fa-2x"></i></span></a>';
echo '<button type="submit" style="background: none; color: inherit; border: none; font: inherit; outine: inherit;" onClick="refreshPage()"><i class="fas fa-sync fa-2x"></i></button>';
echo '</div>';

//$Players = `./playerManager/generateHTML.sh $serverpath 127.0.0.1 $user`;

//Predefine the ip
$IP = "127.0.0.1";

//get the port and the password of rcon
$Port = `grep -oP "rcon-port\K.*" "${serverpath}/ServerManager/.env" | sed 's/"//g' | cut -c 2- | grep -o '^\S*'`;
$Password = `grep -oP "rcon-password\K.*" "${serverpath}/ServerManager/.env" | sed 's/"//g' | cut -c 2- | sed 's/\s.*$//'`;

//Get the current playtime of the server
$Playtime = `./mcrcon -c -H $(echo $IP) -P $(echo $Port) -p $(echo $Password) "/time"`;
$Playtime = `echo $Playtime | sed -r "s/\x1B\[[0-9;]*[a-zA-Z]//g"`;

//show the playtime
echo "<div class=PlayTime>	Current map ran for : $Playtime</div></td></tr>";

//count the players currently online
$PlayersOnline = `./mcrcon -c -H $(echo $IP) -P $(echo $Port) -p $(echo $Password) "/players online"`;
$PlayersOnline = substr_count($PlayersOnline, "\n") - 1;

//show the count of the players currently online
echo "<tr><td><div class=PlayersOnline>Players online : $PlayersOnline</div></td></tr>";

//get the count of the players who joined the server
$TotalPlayersCount = `./mcrcon -c -H $(echo $IP) -P $(echo $Port) -p $(echo $Password) "/players"`;
$TotalPlayersCount = substr_count($TotalPlayersCount, "\n") - 1;

//show the count of the player who joined the server
echo "<tr><td><div class=TotalPlayers>Total players : $TotalPlayersCount</div></td></tr>";

//Create the player table
echo '<tr><td><table class="table" border="1">
	<tr>
	<th class="col-sm-3">Player Name</th>
  <th class="col-sm-3">PlayTime</th>
  <th class="col-sm-3"></th>';

  echo '<th class="col-sm-3">';
  echo '<a class="btn btn-danger" onclick="editCustomPlayer()" href="#">Ban another player</a>';

  echo '<script type="text/javascript">';
  echo 'var link="' . "playerManager/ban.php?serverpath=../${serverpath}&user=${user}" . '";';
  echo "function editCustomPlayer() {";
  echo 'var y = prompt("Player:", "");'; 
  echo 'if (y === "" || y === null) {';
  echo 'alert("You entred no player please retry to ban this player with a pseudo");';
  echo "} else {";
  echo 'var x = prompt("Reason:", "");';
  echo 'if (x === "" || x === null) {';
  echo 'alert("You entred no reason please retry to ban this player with a reason");';
  echo "} else {";
  echo 'link+="&reason=" +x;';
  echo 'link+="&player=" +y;';
  echo 'window.location=link;';
  echo "}";
  echo "}"; 
  echo "}";
  echo '</script>';
  echo '</th>';

echo '</tr>';

//Get all players and put it in a array
exec("./mcrcon -c -H $(echo $IP) -P $(echo $Port) -p $(echo $Password)" . ' "/players"' . ' | sed ' . "'1d'" . " 2>&1 &", $output);

foreach ($output as $Player) {

  //Get the complete name of the player to show it later
  $PlayerComplete = $Player;

  //Remove the online part and any spaces for other rcon commands we will use
  $Player = str_replace(' (online)', '', $Player);

  //Remove white spaces if there are some
  $Player = trim($Player," ");

  //Get the player playtime of the current player
  $PlayerPlayTime = "./mcrcon -c -H $(echo $IP) -P $(echo $Port) -p $(echo $Password)" . " '/silent-command rcon.print(game.players[" . '"' . $Player .  '"' . "].online_time / 60 / 3600)'";
  $PlayerPlayTime = `$PlayerPlayTime`;

  //Clean the playtime to put it in hours
  $Hours = substr($PlayerPlayTime, 0, strpos($PlayerPlayTime, "."));

  //Clean the playtime to put it in minutes
  $Minutes = substr($PlayerPlayTime, strpos($PlayerPlayTime, "."));

  //Method to get the exact minutes
  $Minutes = $Minutes * 60;

  //Remove all things after the point of the minutes to make it cleaner
  $Minutes = substr($Minutes, 0, strpos($Minutes, "."));

  //Show the Player name, minutes, hours
  echo "<tr>
  <td>$PlayerComplete</td>
    <td>$Hours H $Minutes M</td>";

  //Create the part to kick the player
  echo "<td>";

  echo '<a href="playerManager/kick.php?player=' . "${Player}&serverpath=${serverpath}&user=${user}" . '"  onclick="return confirm(' . "'" . 'Are you sure?' . "'" . ');">Kick</a></td>';

  //Create the part to ban the player
  echo "<td>";

  //Create a javascript to have a little window later on
  echo '<script type="text/javascript">';

  //Create the link for the button
  echo 'var link="' . "playerManager/ban.php?serverpath=../${serverpath}&user=${user}" . '";';

  //Create a function for each players
  echo "function editLink${Player}() {";

  //Create a variable for when we will enter a reason for the player
  echo 'var x = prompt("Reason:", "");';

  //Create an if statmeent so we can detect of the reason is null
  echo 'if (x === "" || x === null) {';

  //Have a popup if the user didn't entered any reason for the ban
  echo 'alert("You entred no reason please retry to ban this player with a reason");';

  //If the reason is not null
  echo "} else {";

  //Add the reason to the link of the button redirection
  echo 'link+="&reason=" +x;';

  //Add wich player to ban to the link of the button redirection
  echo 'link+="&player=' . $Player . '";';

  //add the link when pressing the button
  echo 'window.location=link;';

  //Close the if statmenet
  echo "}";

  //Close the function
  echo "}";

  //End the javascript
  echo '</script>';

  //Show the button for ban
  echo '<a onclick="' . "editLink${Player}()" . '" href="#">Ban</a>';

  //End of the line and end of the ban case
  echo '</td></tr>';
}

//End of the player table
echo "</table></td></tr>";

//End of the main table
echo '</table></div>';

?>