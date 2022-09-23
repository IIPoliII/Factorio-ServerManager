<?php

session_start();
if (!isset($_SESSION['login'])) {
  header('LOCATION:../login.php');
  die();
}

$server = $_GET["serverchoose"];

//Get factorio server path via the choosed server

$serverpath  = "../server/$server";

//Get the server status

$GameStateDown = `curl -s -X GET -H "Accept: application/json" "https://multiplayer.factorio.com/get-game-details/9999999999999"`;
$Servertoken = `grep "Matching server game" "${serverpath}/factorio-current.log" 2> /dev/null | awk '{print $7}' | tail -1 | tail -c +2 | head -c -2`;
$ServerAPI = `curl -s -X GET -H "Accept: application/json" "https://multiplayer.factorio.com/get-game-details/${Servertoken}"`;

$ServerVersion = `grep "Loading mod base" ${serverpath}/factorio-current.log 2> /dev/null | awk '{print $5}' | tail -1`;

$ServerWantedVersion = `sed -n '7p' ${serverpath}/server-config.txt`;
$ServerWantedVersion = preg_replace('/\s+/', '', $ServerWantedVersion);

$LastestVersion = `curl -s https://factorio.com/get-download/${ServerWantedVersion}/headless/linux64 | grep -o '[0-9]\.[0-9]\{1,\}\.[0-9]\{1,\}' | head -1`;

//Get the rcon port

$RconPort = `grep -oP "rcon-port\K.*" "${serverpath}/factorio-current.log" | sed 's/"//g' | cut -c 2- | sed 's/\s.*$//'`;
$RconPassword = `grep -oP "rcon-password\K.*" "${serverpath}/ServerManager/.env" | sed 's/"//g' | cut -c 2- | sed 's/\s.*$//'`;
$RconPort = (int) $RconPort;

$StatusIP = `nc -vz 127.0.0.1 $RconPort 2>&1`;

if ($ServerAPI != $GameStateDown && strpos($ServerAPI, 'Not Found') === false && strpos($StatusIP, 'Connection refused') === false) {
    $state = "On";
} else {
    $state = "Off";
}

//Get the number of players online

$NumberPlayersOnline = `curl -s --request GET https://multiplayer.factorio.com/get-game-details/${Servertoken} | jq ".players | length"`;

//Get the size of the save file

$SaveFileSize = `du -m ${serverpath}/saves/japc.zip | cut -f1`;

//creating the table for the left panel

echo '<table id="LeftTable" class="table">';

//creating the table for the left panel - Info Server

echo '<div id="ServerInfo">';
echo "<tr><td>";
echo "$server";
echo "</td></tr>";
echo "</div>";

//creating the table for the left panel - status

//Show if up/down and show an image

if ($ServerAPI != $GameStateDown && strpos($ServerAPI, 'Not Found') === false && strpos($StatusIP, 'Connection refused') === false) {
    echo '<div id="ServerStatus">';
    echo "<tr><td>";
    echo '<img src="images/on.png" style="width:50px;height:50px;">
        <br>Status ON</br>';
    echo "</td></tr>";
    echo "</div>";
} else {
    echo '<div id="ServerStatus">';
    echo "<tr><td>";
    echo '<img src="images/off.png" style="width:50px;height:50px;">
        <br>Status OFF</br>';
    echo "</td></tr>";
    echo "</div>";
}
//Creating the table for the left panel - version

echo '<div id="ServerVersion">';
echo "<tr><td>";
echo "Server Version : $ServerVersion";
echo "</td></tr>";
echo "</div>";

//creating the table for the left panel - players online

if ($ServerAPI != $GameStateDown && strpos($ServerAPI, 'Not Found') === false && strpos($StatusIP, 'Connection refused') === false) {
    echo '<div id="NumberOfPlayersOnline">';
    echo "<tr><td>";
    echo "Numbers of players online : $NumberPlayersOnline";
    echo "</td></tr>";
    echo "</div>";
} else {
    echo '<div id="NumberOfPlayersOnline">';
    echo "<tr><td>";
    echo "Server turned off no info about number of players online";
    echo "</td></tr>";
    echo "</div>";
}

//players Manager


if ($ServerAPI != $GameStateDown && strpos($ServerAPI, 'Not Found') === false && strpos($StatusIP, 'Connection refused') === false) {
    echo '<div id="PlayerManager">';
    echo "<tr><td>";
    echo '<a href="scripts/playerManager.php?serverchoose=';
    echo "$server";
    echo '&serverpath=';
    echo "../server/$server";
    echo '">Player Manager</a>';
    echo "</td></tr>";
    echo "</div>";
} else {
    echo '<div id="PlayerManager">';
    echo "<tr><td>";
    echo "Server turned off can't use player manager";
    echo "</td></tr>";
    echo "</div>";
}


//creating the table for the left panel - Save file size

echo '<div id="SaveFileSize">';
echo "<tr><td>";
echo "Size of the save file : $SaveFileSize MB";
echo "</td></tr>";
echo "</div>";

//Add the reset button on the left panel - Reset

echo '<div id="Reset">';
echo "<tr><td>";
echo '<a href="scripts/launchLoadingReset.php?serverchoose=';
echo "$server";
echo '" onclick="return confirm(\'Are you sure?\');">Reset</a>';

//Add the server config button to the left of the panel - Server Config

echo '<div id="ServerConfig">';
echo "<tr><td>";
echo '<a href="serverConfiguration.php?serverchoose=';
echo "$server";
echo '">Server Config</a>';
echo "</td></tr>";
echo "</div>";

//Add user info to left of the panel

echo '<div id="User">';
echo "<tr><td>";
echo $_SESSION["user"] . " - " . $_SESSION["role"];

//Finishing the table for the left panel

echo '</table>';
