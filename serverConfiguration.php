<!doctype html>
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<link rel="icon" type="image/png" href="images/128.png" />
<script src="https://kit.fontawesome.com/d34481d57e.js"></script>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<link rel=stylesheet href="https://s3-us-west-2.amazonaws.com/colors-css/2.2.0/colors.min.css">

<link rel="stylesheet" type="text/css" href="styles.css">



<title>Server Configuration</title>
<style>
        .topright {
                position: relative;
                position: absolute;

                top: 8px;

                right: 16px;
        }
</style>
<div class="container container-fluid">
        <?php
        session_start();
        if (!isset($_SESSION['login'])) {
                header('LOCATION:login.php');
                die();
        }
        header('Cache-Control: max-age=900');
        $role = $_SESSION["role"];

        $server = $_GET['serverchoose'];

        /* Start of reading the current config file */

        $serverName = `sed -n '2p' server/${server}/data/fctrserver.json | sed 's/"//g' | cut -c 7- `;
        $serverName = substr($serverName, 0, -2);

        $serverDescription = `sed -n '3p' server/${server}/data/fctrserver.json | sed 's/"//g' | cut -c 14-`;
        $serverDescription = substr($serverDescription, 0, -2);

        $serverTags = `sed -n '4p' server/${server}/data/fctrserver.json | sed 's/"//g' | cut -c 8-`;
        $serverTags = substr($serverTags, 0, -3);

        $serverMaxPlayers = `sed -n '5p' server/${server}/data/fctrserver.json | sed 's/"//g' | cut -c 14-`;
        $serverMaxPlayers = substr($serverMaxPlayers, 0, -2);

        $serverVisibilityPublic = `sed -n '8p' server/${server}/data/fctrserver.json | sed 's/"//g' | cut -c 9-`;
        $serverVisibilityPublic = substr($serverVisibilityPublic, 0, -2);
        $serverVisibilityLan = `sed -n '9p' server/${server}/data/fctrserver.json | sed 's/"//g' | cut -c 6-`;
        $serverVisibilityLan = substr($serverVisibilityLan, 0, -1);

        $serverPassword = `sed -n '12p' server/${server}/data/fctrserver.json | sed 's/"//g' | cut -c 16-`;
        $serverPassword = substr($serverPassword, 0, -2);

        $serverUserVerification = `sed -n '13p' server/${server}/data/fctrserver.json | sed 's/"//g' | cut -c 28-`;
        $serverUserVerification = substr($serverUserVerification, 0, -2);

        $serverUpload = `sed -n '14p' server/${server}/data/fctrserver.json | sed 's/"//g' | cut -c 37-`;
        $serverUpload = substr($serverUpload, 0, -2);

        $serverLatency = `sed -n '15p' server/${server}/data/fctrserver.json | sed 's/"//g' | cut -c 27-`;
        $serverLatency = substr($serverLatency, 0, -2);

        $serverIgnorePlayerLimit = `sed -n '16p' server/${server}/data/fctrserver.json | sed 's/"//g' | cut -c 44-`;
        $serverIgnorePlayerLimit = substr($serverIgnorePlayerLimit, 0, -2);

        $serverAllowCommands = `sed -n '17p' server/${server}/data/fctrserver.json | sed 's/"//g' | cut -c 17-`;
        $serverAllowCommands = substr($serverAllowCommands, 0, -2);

        $serverAutoSaveInterval = `sed -n '18p' server/${server}/data/fctrserver.json | sed 's/"//g' | cut -c 20-`;
        $serverAutoSaveInterval = substr($serverAutoSaveInterval, 0, -2);

        $serverAutoSaveSlots = `sed -n '19p' server/${server}/data/fctrserver.json | sed 's/"//g' | cut -c 17-`;
        $serverAutoSaveSlots = substr($serverAutoSaveSlots, 0, -2);

        $serverAFK = `sed -n '20p' server/${server}/data/fctrserver.json | sed 's/"//g' | cut -c 24-`;
        $serverAFK = substr($serverAFK, 0, -2);

        $serverAutoPause = `sed -n '21p' server/${server}/data/fctrserver.json | sed 's/"//g' | cut -c 13-`;
        $serverAutoPause = substr($serverAutoPause, 0, -2);

        $serverOnlyAdminPause = `sed -n '22p' server/${server}/data/fctrserver.json | sed 's/"//g' | cut -c 33-`;
        $serverOnlyAdminPause = substr($serverOnlyAdminPause, 0, -2);

        $serverAutoSaveOnlyOnServer = `sed -n '23p' server/${server}/data/fctrserver.json | sed 's/"//g' | cut -c 26-`;
        $serverAutoSaveOnlyOnServer = substr($serverAutoSaveOnlyOnServer, 0, -2);

        $serverNonBlockSave = `sed -n '24p' server/${server}/data/fctrserver.json | sed 's/"//g' | cut -c 22-`;
        $serverNonBlockSave = substr($serverNonBlockSave, 0, -2);

        $serverFactorioUsername = `sed -n '26p' server/${server}/data/fctrserver.json | sed 's/"//g' | cut -c 11-`;
        $serverFactorioUsername = substr($serverFactorioUsername, 0, -2);

        $serverFactorioPassword = `sed -n '27p' server/${server}/data/fctrserver.json | sed 's/"//g' | cut -c 11-`;
        $serverFactorioPassword = substr($serverFactorioPassword, 0, -1);

        //Server scenario and all config

        $serverGithubScenario = `sed -n '3p' server/${server}/server-config.txt | sed 's/"//g'`;
        $serverGithubScenario = substr($serverGithubScenario, 0, -1);

        $serverGithubScenarioPath = `sed -n '5p' server/${server}/server-config.txt | sed 's/"//g'`;
        $serverGithubScenarioPath = substr($serverGithubScenarioPath, 0, -1);

        $serverVersion = `sed -n '7p' server/${server}/server-config.txt | sed 's/"//g'`;
        $serverVersion = substr($serverVersion, 0, -1);

        $serverRocketResetCounter = `sed -n '9p' server/${server}/server-config.txt | sed 's/"//g'`;
        $serverRocketResetCounter == substr($serverRocketResetCounter, 0, -1);

        //ServerManager CONFIG

        $serverManagerToken = `sed -n '1p' server/${server}/ServerManager/.env | sed 's/"//g' | cut -c 16-`;
        $serverManagerToken = substr($serverManagerToken, 0, -1);

        $serverManagerFactorioChatID = `sed -n '2p' server/${server}/ServerManager/.env | sed 's/"//g' | cut -c 21-`;
        $serverManagerFactorioChatID = substr($serverManagerFactorioChatID, 0, -1);

        $serverManagerConsoleChat = `sed -n '4p' server/${server}/ServerManager/.env | sed 's/"//g' | cut -c 24-`;
        $serverManagerConsoleChat = substr($serverManagerConsoleChat, 0, -1);

        $serverManagerConsoleChatID = `sed -n '5p' server/${server}/ServerManager/.env | sed 's/"//g' | cut -c 25-`;
        $serverManagerConsoleChatID = substr($serverManagerConsoleChatID, 0, -1);

        $serverManagerMapSettigns = `sed -n '7p' server/${server}/ServerManager/.env | sed 's/"//g' | cut -c 20- | grep -o 'map-settings.*' | cut -f1,2 -d' ' | awk '{gsub("map-settings ", "");print}'`;
        $serverManagerMapSettigns = substr($serverManagerMapSettigns, 0, -1);

        $serverManagerMapGenSettigns = `sed -n '7p' server/${server}/ServerManager/.env | sed 's/"//g' | cut -c 20- | grep -o 'map-gen-settings.*' | cut -f1,2 -d' ' | awk '{gsub("map-gen-settings ", "");print}'`;
        $serverManagerMapGenSettigns = substr($serverManagerMapGenSettigns, 0, -1);

        $serverManagerPort = `sed -n '7p' server/${server}/ServerManager/.env | sed 's/"//g' | cut -c 20- | grep -o 'port .*' | cut -f1,2 -d' ' | awk '{gsub("port ", "");print}'`;
        $serverManagerPort = substr($serverManagerPort, 0, -1);

        $serverManagerRconPort = `sed -n '7p' server/${server}/ServerManager/.env | sed 's/"//g' | cut -c 20- | grep -o 'rcon-port .*' | cut -f1,2 -d' ' | awk '{gsub("rcon-port ", "");print}'`;
        $serverManagerRconPort = substr($serverManagerRconPort, 0, -1);

        $serverManagerRconPassword = `sed -n '7p' server/${server}/ServerManager/.env | sed 's/"//g' | cut -c 20- | grep -o 'rcon-password .*' | cut -f1,2 -d' ' | awk '{gsub("rcon-password ", "");print}'`;
        $serverManagerRconPassword = substr($serverManagerRconPassword, 0, -1);

        $serverManagerAdmins = `sed -n '8p' server/${server}/ServerManager/.env | sed 's/"//g' | cut -c 12-`;
        $serverManagerAdmins = substr($serverManagerAdmins, 0, -1);

        $serverManagerPrefix = `sed -n '9p' server/${server}/ServerManager/.env | sed 's/"//g' | cut -c 10`;
        $serverManagerPrefix = substr($serverManagerPrefix, 0, -1);

        $serverManagerModListLocation = `sed -n '10p' server/${server}/ServerManager/.env | sed 's/"//g' | cut -c 19-`;
        $serverManagerModListLocation = substr($serverManagerModListLocation, 0, -1);

        $serverManagerExitGracePeriod = `sed -n '11p' server/${server}/ServerManager/.env | sed 's/"//g' | cut -c 19-`;
        $serverManagerExitGracePeriod = substr($serverManagerExitGracePeriod, 0, -1);


        $serverManagerResetChatID = `sed -n '2p' server/${server}/ServerManager/.createenv | sed 's/"//g' | cut -c 21-`;
        $serverManagerResetChatID = substr($serverManagerResetChatID, 0, -1);

        /* Stop of reading the config file */

        echo '<div class="col-sm-4">';

        echo '<table class="table table-borderless"><tr><td>';
        echo "Role - $role <br>";
        echo "</td><td>";
        echo '<a href="';
        echo "scripts/launchAdminLoading.php?serverchoose=${server}";
        echo '" class="button" style="color:black;"><i class="fas fa-home fa-2x"></i></span></a>';
        echo '</td>';

        if ($role == "Admin") {

                echo '<td>';
                echo '<form action="scripts/changeConfigFile.php';
                echo "?serverchoose=${server}&action=createjson";
                echo '" autocomplete="off" method="post">';
                echo '<input type="submit" class="btn btn-info" value="Generate Config" onclick="return confirm(\'This will delete all your current config \nAre you sure?\');">';
                echo ' </form>';
                echo '</td>';

                echo '<td>';
                echo '<form action="scripts/removeServer.php';
                echo "?serverchoose=${server}";
                echo '" autocomplete="off" method="post">';
                echo '<input type="submit" class="btn btn-info" value="Delete Server" onclick="return confirm(\'This will completly delete the server \nAre you sure?\');">';
                echo ' </form>';
                echo '</td>';

                echo '<td>';
                echo '<form action="scripts/schedulerConfig.php';
                echo "?serverchoose=${server}";
                echo '" autocomplete="off" method="post">';
                echo '<input type="submit" class="btn btn-info" value="Schedule Configuration">';
                echo ' </form>';
                echo '</td>';
        }
        echo '</table>';
        echo '</div>';

        echo '<table class="table">
                <td>';


        if ($role == "Admin") {

                echo '<form action="scripts/changeConfigFile.php';
                echo "?serverchoose=${server}&action=json";
                echo '" autocomplete="off" method="post">';

                echo "Server name : <br>";
                echo '<input type="text" name="serverName" value="';
                echo "$serverName";
                echo '">';
                echo "<br>";

                echo "Server description : <br>";
                echo '<input type="text" name="serverDescription" value="';
                echo "$serverDescription";
                echo '">';
                echo "<br>";

                echo "Server tags : <br>";
                echo '<input type="text" name="serverTags" value="';
                echo "$serverTags";
                echo '">';
                echo "<br>";

                echo "Max players : <br>";
                echo '<input type="text" name="serverMaxPlayers" value="';
                echo "$serverMaxPlayers";
                echo '">';
                echo "<br>";


                if ($serverVisibilityPublic == "true") {
                        echo "Server Public Visibility : <br>";
                        echo '<select name="serverVisibilityPublic">';
                        echo '<option value="true" selected>True</option>';
                        echo '<option value="false">False</option>';
                        echo '</select>';
                        echo "<br>";
                } elseif ($serverVisibilityPublic == "false") {
                        echo "Server Public Visibility : <br>";
                        echo '<select name="serverVisibilityPublic">';
                        echo '<option value="true">True</option>';
                        echo '<option value="false" selected>False</option>';
                        echo '</select>';
                        echo "<br>";
                }

                if ($serverVisibilityLan == "true") {
                        echo "Server Lan Visibility : <br>";
                        echo '<select name="serverVisibilityLan">';
                        echo '<option value="true" selected>True</option>';
                        echo '<option value="false">False</option>';
                        echo '</select>';
                        echo "<br>";
                } elseif ($serverVisibilityLan == "false") {
                        echo "Server Lan Visibility : <br>";
                        echo '<select name="serverVisibilityLan">';
                        echo '<option value="true">True</option>';
                        echo '<option value="false" selected>False</option>';
                        echo '</select>';
                        echo "<br>";
                }

                echo "Token (hidden) : <br>";
                echo '<input type="password" name="serverToken" >';
                echo "<br>";

                echo "Factorio.com Username : <br>";
                echo '<input type="text" name="serverFactorioUsername" value="';
                echo "$serverFactorioUsername";
                echo '">';
                echo "<br>";

                echo "Factorio.com Password (hidden) : <br>";
                echo '<input type="password" name="serverFactorioPassword" >';
                echo "<br>";


                echo "Server password : <br>";
                echo '<input type="text" name="serverPassword" value="';
                echo "$serverPassword";
                echo '">';
                echo "<br>";

                if ($serverUserVerification == "true") {
                        echo "User verification : <br>";
                        echo '<select name="serverUserVerification">';
                        echo '<option value="true" selected>True</option>';
                        echo '<option value="false">False</option>';
                        echo '</select>';
                        echo "<br>";
                } elseif ($serverUserVerification == "false") {
                        echo "User verification : <br>";
                        echo '<select name="serverUserVerification">';
                        echo '<option value="true">True</option>';
                        echo '<option value="false" selected>False</option>';
                        echo '</select>';
                        echo "<br>";
                }

                echo "Upload limit in kb/s : <br>";
                echo '<input type="text" name="serverUpload" value="';
                echo "$serverUpload";
                echo '">';
                echo "<br>";

                echo "Latency limit in ticks (1 tick = 16ms) : <br>";
                echo '<input type="text" name="serverLatency" value="';
                echo "$serverLatency";
                echo '">';
                echo "<br>";

                if ($serverIgnorePlayerLimit == "true") {
                        echo "Ignore player limit for returning players : <br>";
                        echo '<select name="serverIgnorePlayerLimit">';
                        echo '<option value="true" selected>True</option>';
                        echo '<option value="false">False</option>';
                        echo '</select>';
                        echo "<br>";
                } elseif ($serverIgnorePlayerLimit == "false") {
                        echo "Ignore player limit for returning players : <br>";
                        echo '<select name="serverIgnorePlayerLimit">';
                        echo '<option value="true">True</option>';
                        echo '<option value="false" selected>False</option>';
                        echo '</select>';
                        echo "<br>";
                }

                if ($serverAllowCommands == "true") {
                        echo "Allow commands : <br>";
                        echo '<select name="serverAllowCommands">';
                        echo '<option value="true" selected>True</option>';
                        echo '<option value="false">False</option>';
                        echo '<option value="admins-only">Admins only</option>';
                        echo '</select>';
                        echo "<br>";
                } elseif ($serverAllowCommands == "false") {
                        echo "Allow commands : <br>";
                        echo '<select name="serverAllowCommands">';
                        echo '<option value="true">True</option>';
                        echo '<option value="false" selected>False</option>';
                        echo '<option value="admins-only">Admins only</option>';
                        echo '</select>';
                        echo "<br>";
                } elseif ($serverAllowCommands == "admins-only") {
                        echo "Allow commands : <br>";
                        echo '<select name="serverAllowCommands">';
                        echo '<option value="true">True</option>';
                        echo '<option value="false">False</option>';
                        echo '<option value="admins-only" selected>Admins only</option>';
                        echo '</select>';
                        echo "<br>";
                }

                echo "Autosave interval : <br>";
                echo '<input type="text" name="serverAutoSaveInterval" value="';
                echo "$serverAutoSaveInterval";
                echo '">';
                echo "<br>";

                echo "Autosave slots : <br>";
                echo '<input type="text" name="serverAutoSaveSlots" value="';
                echo "$serverAutoSaveSlots";
                echo '">';
                echo "<br>";


                if ($serverAutoSaveOnlyOnServer == "true") {
                        echo "Auto save only on server : <br>";
                        echo '<select name="serverAutoSaveOnlyOnServer">';
                        echo '<option value="true" selected>True</option>';
                        echo '<option value="false">False</option>';
                        echo '</select>';
                        echo "<br>";
                } elseif ($serverAutoSaveOnlyOnServer == "false") {
                        echo "Auto save only on server : <br>";
                        echo '<select name="serverAutoSaveOnlyOnServer">';
                        echo '<option value="true">True</option>';
                        echo '<option value="false" selected>False</option>';
                        echo '</select>';
                        echo "<br>";
                }

                if ($serverNonBlockSave == "true") {
                        echo "Non block saving (experimental) : <br>";
                        echo '<select name="serverNonBlockSave">';
                        echo '<option value="true" selected>True</option>';
                        echo '<option value="false">False</option>';
                        echo '</select>';
                        echo "<br>";
                } elseif ($serverNonBlockSave == "false") {
                        echo "Non block saving (experimental) : <br>";
                        echo '<select name="serverNonBlockSave">';
                        echo '<option value="true">True</option>';
                        echo '<option value="false" selected>False</option>';
                        echo '</select>';
                        echo "<br>";
                }





                echo "AFK kick (minutes): <br>";
                echo '<input type="text" name="serverAFK" value="';
                echo "$serverAFK";
                echo '">';
                echo "<br>";


                if ($serverAutoPause == "true") {
                        echo "Auto pause : <br>";
                        echo '<select name="serverAutoPause">';
                        echo '<option value="true" selected>True</option>';
                        echo '<option value="false">False</option>';
                        echo '</select>';
                        echo "<br>";
                } elseif ($serverAutoPause == "false") {
                        echo "Auto pause : <br>";
                        echo '<select name="serverAutoPause">';
                        echo '<option value="true">True</option>';
                        echo '<option value="false" selected>False</option>';
                        echo '</select>';
                        echo "<br>";
                }

                if ($serverOnlyAdminPause == "true") {
                        echo "Only admins can pause the game : <br>";
                        echo '<select name="serverOnlyAdminPause">';
                        echo '<option value="true" selected>True</option>';
                        echo '<option value="false">False</option>';
                        echo '</select>';
                        echo "<br>";
                } elseif ($serverOnlyAdminPause == "false") {
                        echo "Only admins can pause the game : <br>";
                        echo '<select name="serverOnlyAdminPause">';
                        echo '<option value="true">True</option>';
                        echo '<option value="false" selected>False</option>';
                        echo '</select>';
                        echo "<br>";
                }
        } elseif ($role == "Moderator") {

                echo '<form action="scripts/changeConfigFile.php';
                echo "?serverchoose=${server}";
                echo '" autocomplete="off" method="post">';


                echo "Server name : <br>";
                echo '<input type="text" name="serverName" readonly="readonly" value="';
                echo "$serverName";
                echo '">';
                echo "<br>";
                echo "Server description : <br>";
                echo '<input type="text" name="serverDescription" readonly="readonly" value="';
                echo "$serverDescription";
                echo '">';
                echo "<br>";
                echo "Server tags : <br>";
                echo '<input type="text" name="serverTags" readonly="readonly" value="';
                echo "$serverTags";
                echo '">';
                echo "<br>";
                echo "Max players : <br>";
                echo '<input type="text" name="serverMaxPlayers" readonly="readonly" value="';
                echo "$serverMaxPlayers";
                echo '">';
                echo "<br>";


                echo "Server Public Visibility: <br>";
                echo '<input type="text" name="serverVisibilityPublic" readonly="readonly" value="';
                echo "$serverVisibilityPublic";
                echo '">';
                echo "<br>";

                echo "Server Lan Visibility: <br>";
                echo '<input type="text" name="serverVisibilityLan" readonly="readonly" value="';
                echo "$serverVisibilityLan";
                echo '">';
                echo "<br>";


                echo "Token (hidden) : <br>";
                echo '<input type="password" name="serverToken" readonly="readonly">';
                echo "<br>";

                echo "Factorio.com Username : <br>";
                echo '<input type="text" name="serverFactorioUsername" readonly="readonly" value="';
                echo "$serverFactorioUsername";
                echo '">';
                echo "<br>";

                echo "Factorio.com Password (hidden) : <br>";
                echo '<input type="password" readonly="readonly" name="serverFactorioPassword" >';
                echo "<br>";


                echo "Server password : <br>";
                echo '<input type="text" name="serverPassword" readonly="readonly" value="';
                echo "$serverPassword";
                echo '">';
                echo "<br>";

                echo "User verification : <br>";
                echo '<input type="text" name="serverUserVerification" readonly="readonly" value="';
                echo "$serverUserVerification";
                echo '">';
                echo "<br>";


                echo "Upload limit in kb/s : <br>";
                echo '<input type="text" name="serverUpload" readonly="readonly" value="';
                echo "$serverUpload";
                echo '">';
                echo "<br>";
                echo "Latency limit in ticks (1 tick = 16ms) : <br>";
                echo '<input type="text" name="serverLatency" readonly="readonly" value="';
                echo "$serverLatency";
                echo '">';
                echo "<br>";


                echo "Ignore player limit for returning players : <br>";
                echo '<input type="text" name="serverIgnorePlayerLimit" readonly="readonly" value="';
                echo "$serverIgnorePlayerLimit";
                echo '">';
                echo "<br>";

                echo "Allow commands : <br>";
                echo '<input type="text" name="serverAllowCommands" readonly="readonly" value="';
                echo "$serverAllowCommands";
                echo '">';
                echo "<br>";

                echo "Autosave interval : <br>";
                echo '<input type="text" name="serverAutoSaveInterval" readonly="readonly" value="';
                echo "$serverAutoSaveInterval";
                echo '">';
                echo "<br>";
                echo "Autosave slots : <br>";
                echo '<input type="text" name="serverAutoSaveSlots" readonly="readonly" value="';
                echo "$serverAutoSaveSlots";
                echo '">';
                echo "<br>";

                echo "Auto save only on server : <br>";
                echo '<input type="text" name="serverAutoSaveOnlyOnServer" readonly="readonly" value="';
                echo "$serverAutoSaveOnlyOnServer";
                echo '">';
                echo "<br>";

                echo "Non block saving (experimental) : <br>";
                echo '<input type="text" name="serverNonBlockSave" readonly="readonly" value="';
                echo "$serverNonBlockSave";
                echo '">';
                echo "<br>";

                echo "AFK kick (minutes): <br>";
                echo '<input type="text" name="serverAFK" value="';
                echo "$serverAFK";
                echo '">';
                echo "<br>";
                if ($serverAutoPause == "true") {
                        echo "Auto pause : <br>";
                        echo '<select name="serverAutoPause">';
                        echo '<option value="true" selected>True</option>';
                        echo '<option value="false">False</option>';
                        echo '</select>';
                        echo "<br>";
                } elseif ($serverAutoPause == "false") {
                        echo "Auto pause : <br>";
                        echo '<select name="serverAutoPause">';
                        echo '<option value="true">True</option>';
                        echo '<option value="false" selected>False</option>';
                        echo '</select>';
                        echo "<br>";
                }

                echo "Only admins can pause the game : <br>";
                echo '<input type="text" name="serverOnlyAdminPause" readonly="readonly" value="';
                echo "$serverOnlyAdminPause";
                echo '">';
                echo "<br>";
        }
        echo '<input type="submit" value="Apply Config">';
        echo ' </form>';

        ?>
        </td>
        <td valign="top">
                <?php
                /* --------------------------------------------------------------------------------*/
                echo '<form action="scripts/changeConfigFile.php';
                echo "?serverchoose=${server}&action=github";
                echo '" autocomplete="off" method="post">';
                echo "<br>";

                if ($role == "Admin") {
                        echo "Github Scenario URL : <br>";
                        echo '<input type="text" name="serverGithubScenario" value="';
                        echo "$serverGithubScenario";
                        echo '">';
                        echo "<br>";
                        echo "Github Scenario PATH : <br>";
                        echo '<input type="text" name="serverGithubScenarioPath" value="';
                        echo "$serverGithubScenarioPath";
                        echo '">';
                        echo "<br>";

                        if ($serverVersion == "stable") {
                                echo "Factorio Version: <br>";
                                echo '<select name="serverVersion">';
                                echo '<option value="stable" selected>Stable</option>';
                                echo '<option value="latest">Latest</option>';
                                echo '</select>';
                                echo "<br>";
                        } elseif ($serverVersion == "latest") {
                                echo "Factorio Version : <br>";
                                echo '<select name="serverVersion">';
                                echo '<option value="stable">Stable</option>';
                                echo '<option value="latest" selected>Latest</option>';
                                echo '</select>';
                                echo "<br>";
                        }

                        echo "Max rocket for reset (if enabled) : <br>";
                        echo '<input type="text" name="serverRocketResetCounter" value="';
                        echo "$serverRocketResetCounter";
                        echo '">';
                        echo "<br>";
                } elseif ($role == "Moderator") {
                        echo "Github Scenario URL : <br>";
                        echo '<input type="text" name="serverGithubScenario" readonly="readonly" value="';
                        echo "$serverGithubScenario";
                        echo '">';
                        echo "<br>";
                        echo "Github Scenario PATH : <br>";
                        echo '<input type="text" name="serverGithubScenarioPath" readonly="readonly" value="';
                        echo "$serverGithubScenarioPath";
                        echo '">';
                        echo "<br>";
                        echo "Factorio Version : <br>";
                        echo '<input type="text" name="serverVersion" readonly="readonly" value="';
                        echo "$serverVersion";
                        echo '">';
                        echo "<br>";
                        echo "Max rocket for reset (if enabled) : <br>";
                        echo '<input type="text" name="serverRocketResetCounter" value="';
                        echo "$serverRocketResetCounter";
                        echo '">';
                        echo "<br>";
                }
                echo '<input type="submit" value="Apply Config">';
                echo '</form>';

                ?>

        </td>
        <td valign="top">
                <?php
                /* --------------------------------------------------------------------------------*/

                echo '<form action="scripts/changeConfigFile.php';
                echo "?serverchoose=${server}&action=servermanager";
                echo '" autocomplete="off" method="post">';
                echo "<br>";

                if ($role == "Admin") {
                        echo "BOT token : <br>";
                        echo '<input type="text" name="serverManagerToken" value="';
                        echo "$serverManagerToken";
                        echo '">';
                        echo "<br>";

                        echo "Chat ID : <br>";
                        echo '<input type="text" name="serverManagerFactorioChatID" value="';
                        echo "$serverManagerFactorioChatID";
                        echo '">';
                        echo "<br>";

                        if ($serverManagerConsoleChat == "false") {
                                echo "Console chat channel : <br>";
                                echo '<select name="serverManagerConsoleChat">';
                                echo '<option value="false" selected>False</option>';
                                echo '<option value="true">True</option>';
                                echo '</select>';
                                echo "<br>";
                        } elseif ($serverManagerConsoleChat == "true") {
                                echo "Console chat channel : <br>";
                                echo '<select name="serverManagerConsoleChat">';
                                echo '<option value="false">False</option>';
                                echo '<option value="true" selected>True</option>';
                                echo '</select>';
                                echo "<br>";
                        }

                        echo "Console chat ID : <br>";
                        echo '<input type="text" name="serverManagerConsoleChatID" value="';
                        echo "$serverManagerConsoleChatID";
                        echo '">';
                        echo "<br>";

                        echo "Map settigns path : <br>";
                        echo '<input type="text" name="serverManagerMapSettigns" value="';
                        echo "$serverManagerMapSettigns";
                        echo '">';
                        echo "<br>";

                        echo "Map gen settigns path : <br>";
                        echo '<input type="text" name="serverManagerMapGenSettigns" value="';
                        echo "$serverManagerMapGenSettigns";
                        echo '">';
                        echo "<br>";

                        echo "Game Port : <br>";
                        echo '<input type="text" name="serverManagerPort" value="';
                        echo "$serverManagerPort";
                        echo '">';
                        echo "<br>";

                        echo "Rcon Port : <br>";
                        echo '<input type="text" name="serverManagerRconPort" value="';
                        echo "$serverManagerRconPort";
                        echo '">';
                        echo "<br>";

                        echo "Rcon Password : <br>";
                        echo '<input type="text" name="serverManagerRconPassword" value="';
                        echo "$serverManagerRconPassword";
                        echo '">';
                        echo "<br>";

                        echo "Admin ID's : <br>";
                        echo '<input type="text" name="serverManagerAdmins" value="';
                        echo "$serverManagerAdmins";
                        echo '">';
                        echo "<br>";

                        echo "Bot prefix : <br>";
                        echo '<input type="text" name="serverManagerPrefix" value="';
                        echo "$serverManagerPrefix";
                        echo '">';
                        echo "<br>";

                        echo "Mod list location : <br>";
                        echo '<input type="text" name="serverManagerModListLocation" value="';
                        echo "$serverManagerModListLocation";
                        echo '">';
                        echo "<br>";

                        echo "Exit grace time (in seconds) : <br>";
                        echo '<input type="text" name="serverManagerExitGracePeriod" value="';
                        echo "$serverManagerExitGracePeriod";
                        echo '">';
                        echo "<br>";

                        echo "Reset channel ID : <br>";
                        echo '<input type="text" name="serverManagerResetChatID" value="';
                        echo "$serverManagerResetChatID";
                        echo '">';
                        echo "<br>";
                } elseif ($role == "Moderator") {
                        echo "BOT token : <br>";
                        echo '<input type="text" name="serverManagerToken" readonly="readonly" value="';
                        echo "$serverManagerToken";
                        echo '">';
                        echo "<br>";

                        echo "Chat ID : <br>";
                        echo '<input type="text" name="serverManagerFactorioChatID" readonly="readonly" value="';
                        echo "$serverManagerFactorioChatID";
                        echo '">';
                        echo "<br>";

                        echo "Console chat channel : <br>";
                        echo '<input type="text" name="serverManagerConsoleChat" readonly="readonly" value="';
                        echo "$serverManagerConsoleChat";
                        echo '">';
                        echo "<br>";

                        echo "Console chat ID : <br>";
                        echo '<input type="text" name="serverManagerConsoleChatID" readonly="readonly" value="';
                        echo "$serverManagerConsoleChatID";
                        echo '">';
                        echo "<br>";

                        echo "Map settigns path : <br>";
                        echo '<input type="text" name="serverManagerMapSettigns" readonly="readonly" value="';
                        echo "$serverManagerMapSettigns";
                        echo '">';
                        echo "<br>";

                        echo "Game Port : <br>";
                        echo '<input type="text" name="serverManagerPort" readonly="readonly" value="';
                        echo "$serverManagerPort";
                        echo '">';
                        echo "<br>";

                        echo "Rcon Port : <br>";
                        echo '<input type="text" name="serverManagerRconPort" readonly="readonly" value="';
                        echo "$serverManagerRconPort";
                        echo '">';
                        echo "<br>";

                        echo "Rcon Password : <br>";
                        echo '<input type="text" name="serverManagerRconPassword" readonly="readonly" value="';
                        echo '">';
                        echo "<br>";

                        echo "Admin ID's : <br>";
                        echo '<input type="text" name="serverManagerAdmins" readonly="readonly" value="';
                        echo "$serverManagerAdmins";
                        echo '">';
                        echo "<br>";

                        echo "Bot prefix : <br>";
                        echo '<input type="text" name="serverManagerPrefix" readonly="readonly" value="';
                        echo "$serverManagerPrefix";
                        echo '">';
                        echo "<br>";

                        echo "Mod list location : <br>";
                        echo '<input type="text" name="serverManagerModListLocation" readonly="readonly" value="';
                        echo "$serverManagerModListLocation";
                        echo '">';
                        echo "<br>";

                        echo "Exit grace time (in seconds) : <br>";
                        echo '<input type="text" name="serverManagerExitGracePeriod" readonly="readonly" value="';
                        echo "$serverManagerExitGracePeriod";
                        echo '">';
                        echo "<br>";


                        echo "Reset channel ID : <br>";
                        echo '<input type="text" name="serverManagerResetChatID" readonly="readonly" value="';
                        echo "$serverManagerResetChatID";
                        echo '">';
                        echo "<br>";
                }
                echo '<input type="submit" value="Apply Config">';
                echo '</form>';
                ?>
        </td>
        </table>
</div>