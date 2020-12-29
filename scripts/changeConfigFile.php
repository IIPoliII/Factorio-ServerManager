<!doctype html>
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<script src="https://kit.fontawesome.com/d34481d57e.js"></script>

<title>Schedule configuration</title>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<link rel="icon" type="image/png" href="../images/128.png" />

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<link rel=stylesheet href="https://s3-us-west-2.amazonaws.com/colors-css/2.2.0/colors.min.css">

<link rel="stylesheet" type="text/css" href="../styles.css">



<div class="container container-fluid">


	<?php
	session_start();
	if (!isset($_SESSION['login'])) {
		header('LOCATION:login.php');
		die();
	}
	header('Cache-Control: max-age=900');


	$server = $_GET['serverchoose'];
	$action = $_GET['action'];

	if ($action == "json") {
		$serverName = $_POST['serverName'];
		$serverDescription = $_POST['serverDescription'];
		$serverTags =  $_POST['serverTags'];
		$serverMaxPlayers = $_POST['serverMaxPlayers'];
		$serverVisibilityPublic = $_POST['serverVisibilityPublic'];
		$serverVisibilityLan = $_POST['serverVisibilityLan'];

		$serverToken = $_POST['serverToken'];

		if (empty($serverToken)) {
			$serverToken = `sed -n '11p' ../server/${server}/data/fctrserver.json | sed 's/"//g' | cut -c 8-`;
			$serverToken = substr($serverToken, 0, -2);
		}

		$serverFactorioUsername = $_POST['serverFactorioUsername'];

		if (empty($serverFactorioUsername)) {
			$serverFactorioUsername = `sed -n '26p' ../server/${server}/data/fctrserver.json | sed 's/"//g' | cut -c 11-`;
			$serverFactorioUsername = substr($serverFactorioUsername, 0, -2);
		}


		$serverFactorioPassword = $_POST['serverFactorioPassword'];

		if (empty($serverFactorioPassword)) {
			$serverFactorioPassword = `sed -n '27p' ../server/${server}/data/fctrserver.json | sed 's/"//g' | cut -c 11-`;
			$serverFactorioPassword = substr($serverFactorioPassword, 0, -1);
		}


		$serverPassword = $_POST['serverPassword'];
		$serverUserVerification = $_POST['serverUserVerification'];
		$serverUpload = $_POST['serverUpload'];
		$serverLatency = $_POST['serverLatency'];
		$serverIgnorePlayerLimit = $_POST['serverIgnorePlayerLimit'];
		$serverAllowCommands = $_POST['serverAllowCommands'];
		$serverAutoSaveInterval = $_POST['serverAutoSaveInterval'];
		$serverAutoSaveSlots = $_POST['serverAutoSaveSlots'];
		$serverAFK = $_POST['serverAFK'];
		$serverAutoPause = $_POST['serverAutoPause'];
		$serverOnlyAdminPause = $_POST['serverOnlyAdminPause'];
		$serverAutoSaveOnlyOnServer = $_POST['serverAutoSaveOnlyOnServer'];
		$serverNonBlockSave = $_POST['serverNonBlockSave'];

		//shell_exec("rm -rf /tmp/server.config");
		/* Start of correcting variables for the file */

		$serverName = '"name": "' . $serverName . '",';

		$serverDescription = '"description": "' . $serverDescription . '",';

		$serverTags = '"' . str_replace(",", '","', $serverTags) . '"';
		$serverTags = '"tags": [' . $serverTags . '],';

		$serverMaxPlayers = '"max_players": ' . $serverMaxPlayers . ',';

		$serverVisibilityPublic = '"public": ' . $serverVisibilityPublic . ',';
		$serverVisibilityLan = '"lan": ' . $serverVisibilityLan;

		$serverToken = '"token": "' . $serverToken . '",';

		$serverFactorioUsername = '"username": "' . $serverFactorioUsername . '",';

		$serverFactorioPassword = '"password": "' . $serverFactorioPassword . '"';

		$serverPassword = '"game_password": "' . $serverPassword . '",';

		$serverUserVerification = '"require_user_verification": ' . $serverUserVerification . ',';

		$serverUpload = '"max_upload_in_kilobytes_per_second": ' . $serverUpload . ',';

		$serverLatency = '"minimum_latency_in_ticks": ' . $serverLatency . ',';

		$serverIgnorePlayerLimit = '"ignore_player_limit_for_returning_players": ' . $serverIgnorePlayerLimit . ',';

		$serverAllowCommands = '"allow_commands": "' . $serverAllowCommands . '",';

		$serverAutoSaveInterval = '"autosave_interval": ' . $serverAutoSaveInterval . ',';

		$serverAutoSaveSlots = '"autosave_slots": ' . $serverAutoSaveSlots . ',';

		$serverAFK = '"afk_autokick_interval": ' . $serverAFK . ',';

		$serverAutoPause = '"auto_pause": ' . $serverAutoPause . ',';

		$serverOnlyAdminPause = '"only_admins_can_pause_the_game": ' . $serverOnlyAdminPause . ',';

		$serverAutoSaveOnlyOnServer = '"autosave_only_on_server": ' . $serverAutoSaveOnlyOnServer . ',';

		$serverNonBlockSave = '"non_blocking_saving": ' . $serverNonBlockSave . ',';

		/*Stop */


		$fp = fopen('tmp/server.config', 'w') or die("Unable to open file!");
		fwrite($fp, '{');
		fwrite($fp, "\n");
		fwrite($fp, "$serverName\n");
		fwrite($fp, "$serverDescription\n");
		fwrite($fp, "$serverTags\n");
		fwrite($fp, "$serverMaxPlayers\n");
		fwrite($fp, '"visibility":');
		fwrite($fp, "\n");
		fwrite($fp, '{');
		fwrite($fp, "\n");
		fwrite($fp, "$serverVisibilityPublic\n");
		fwrite($fp, "$serverVisibilityLan\n");
		fwrite($fp, '},');
		fwrite($fp, "\n");
		fwrite($fp, "$serverToken\n");
		fwrite($fp, "$serverPassword\n");
		fwrite($fp, "$serverUserVerification\n");
		fwrite($fp, "$serverUpload\n");
		fwrite($fp, "$serverLatency\n");
		fwrite($fp, "$serverIgnorePlayerLimit\n");
		fwrite($fp, "$serverAllowCommands\n");
		fwrite($fp, "$serverAutoSaveInterval\n");
		fwrite($fp, "$serverAutoSaveSlots\n");
		fwrite($fp, "$serverAFK\n");
		fwrite($fp, "$serverAutoPause\n");
		fwrite($fp, "$serverOnlyAdminPause\n");
		fwrite($fp, "$serverAutoSaveOnlyOnServer\n");
		fwrite($fp, "$serverNonBlockSave\n");
		fwrite($fp, '"admins": [],');
		fwrite($fp, "\n");
		fwrite($fp, "$serverFactorioUsername\n");
		fwrite($fp, "$serverFactorioPassword\n");
		fwrite($fp, '}');
		fclose($fp);



		$move1 = `mv tmp/server.config ../server/${server}/data/fctrserver.json`;
	} elseif ($action == "github") {

		$serverGithubScenario = $_POST['serverGithubScenario'];
		$serverGithubScenarioPath = $_POST['serverGithubScenarioPath'];
		$serverVersion = $_POST['serverVersion'];
		$serverRocketResetCounter = $_POST['serverRocketResetCounter'];


		$fp = fopen('tmp/servergithub.config', 'w') or die("Unable to open file!");
		fwrite($fp, '#DO NOT MOVE THE LINES');
		fwrite($fp, "\n");
		fwrite($fp, '#Link to the github put NONE for no scenario');
		fwrite($fp, "\n");
		fwrite($fp, "$serverGithubScenario\n");
		fwrite($fp, '#Directory put . if there is none');
		fwrite($fp, "\n");
		fwrite($fp, "$serverGithubScenarioPath\n");
		fwrite($fp, '#Server version latest or stable');
		fwrite($fp, "\n");
		fwrite($fp, "$serverVersion\n");
		fwrite($fp, '#Rocket Reset Counter');
		fwrite($fp, "\n");
		fwrite($fp, "$serverRocketResetCounter\n");
		fclose($fp);

		$move2 = `mv tmp/servergithub.config ../server/${server}/server-config.txt`;
	} elseif ($action == "createjson") {
		$fp = fopen('tmp/server.config', 'w') or die("Unable to open file!");
		fwrite($fp, '{');
		fwrite($fp, "\n");
		fwrite($fp, '"name": "Change me",');
		fwrite($fp, "\n");
		fwrite($fp, '"description": "Change me",');
		fwrite($fp, "\n");
		fwrite($fp, '"tags": ["tag0","tag1"],');
		fwrite($fp, "\n");
		fwrite($fp, '"max_players": 0,');
		fwrite($fp, "\n");
		fwrite($fp, '"visibility":');
		fwrite($fp, "\n");
		fwrite($fp, '{');
		fwrite($fp, "\n");
		fwrite($fp, '"public": true,');
		fwrite($fp, "\n");
		fwrite($fp, '"lan": true');
		fwrite($fp, "\n");
		fwrite($fp, '},');
		fwrite($fp, "\n");
		fwrite($fp, '"token": "",');
		fwrite($fp, "\n");
		fwrite($fp, '"game_password": "test",');
		fwrite($fp, "\n");
		fwrite($fp, '"require_user_verification": true,');
		fwrite($fp, "\n");
		fwrite($fp, '"max_upload_in_kilobytes_per_second": 0,');
		fwrite($fp, "\n");
		fwrite($fp, '"minimum_latency_in_ticks": 0,');
		fwrite($fp, "\n");
		fwrite($fp, '"ignore_player_limit_for_returning_players": false,');
		fwrite($fp, "\n");
		fwrite($fp, '"allow_commands": "admins-only",');
		fwrite($fp, "\n");
		fwrite($fp, '"autosave_interval": 0,');
		fwrite($fp, "\n");
		fwrite($fp, '"autosave_slots": 0,');
		fwrite($fp, "\n");
		fwrite($fp, '"afk_autokick_interval": 0,');
		fwrite($fp, "\n");
		fwrite($fp, '"auto_pause": true,');
		fwrite($fp, "\n");
		fwrite($fp, '"only_admins_can_pause_the_game": false,');
		fwrite($fp, "\n");
		fwrite($fp, '"autosave_only_on_server": true,');
		fwrite($fp, "\n");
		fwrite($fp, '"non_blocking_saving": true,');
		fwrite($fp, "\n");
		fwrite($fp, '"admins": [],');
		fwrite($fp, "\n");
		fwrite($fp, '"username": "FactorioAccountLogin",');
		fwrite($fp, "\n");
		fwrite($fp, '"password": "FactorioAccountPassword"');
		fwrite($fp, "\n");
		fwrite($fp, '}');
		fclose($fp);

		$move3 = `mv tmp/server.config ../server/${server}/data/fctrserver.json`;

		$fp = fopen('tmp/servergithub.config', 'w') or die("Unable to open file!");
		fwrite($fp, '#DO NOT MOVE THE LINES');
		fwrite($fp, "\n");
		fwrite($fp, '#Link to the github put NONE for no scenario');
		fwrite($fp, "\n");
		fwrite($fp, "https://github.com/joinandplaycoop/Factorio-Scenarios.git\n");
		fwrite($fp, '#Directory put . if there is none');
		fwrite($fp, "\n");
		fwrite($fp, "0.X.X/JoinAndPlayCoop-Event-Handler\n");
		fwrite($fp, '#Server version latest or stable');
		fwrite($fp, "\n");
		fwrite($fp, "stable\n");
		fwrite($fp, '#Rocket Reset Enabled?');
		fwrite($fp, "\n");
		fwrite($fp, "false\n");
		fwrite($fp, '#Rocket Reset Counter');
		fwrite($fp, "\n");
		fwrite($fp, "1000\n");
		fclose($fp);

		$move3 = `mv tmp/servergithub.config ../server/${server}/server-config.txt`;

		$fp = fopen('tmp/serverManager.config', 'w') or die("Unable to open file!");
		fwrite($fp, 'DiscordToken = "InsertTokenOrServerWillNotWork"');
		fwrite($fp, "\n");
		fwrite($fp, 'FactorioChannelID = "InsertChatIdOrServerWillNotWork"');
		fwrite($fp, "\n");
		fwrite($fp, 'FactorioChannelID = "InsertChatIdOrServerWillNotWork"');
		fwrite($fp, "PassConsoleChat = false\n");
		fwrite($fp, 'EnableConsoleChannel = false');
		fwrite($fp, "\n");
		fwrite($fp, 'FactorioConsoleChatID = "InsertConsoleChatIdIfYouWantToEnableConsoleChannelOrItWillNotWork');
		fwrite($fp, "\n");
		fwrite($fp, 'Executable = "../bin/x64/factorio"');
		fwrite($fp, "\n");
		fwrite($fp, 'LaunchParameters = "--start-server ../saves/japc.zip --server-settings ../data/fctrserver.json --port 34198 --rcon-port 34199 --rcon-password ChangeMeNOWWWWW"');
		fwrite($fp, "\n");
		fwrite($fp, 'AdminIDs = "InsertAllAdminsIdHere"');
		fwrite($fp, "\n");
		fwrite($fp, 'Prefix = "!"');
		fwrite($fp, "\n");
		fwrite($fp, 'ModListLocation = "../mods/mod-list.json"');
		fwrite($fp, "\n");
		fwrite($fp, 'ExitGracePeriod = 15');
		fclose($fp);

		$move3 = `mv tmp/serverManager.config ../server/${server}/ServerManager/.env`;

		$fp = fopen('tmp/serverManagerCreate.config', 'w') or die("Unable to open file!");
		fwrite($fp, 'DiscordToken = "InsertTokenOrServerWillNotWork"');
		fwrite($fp, "\n");
		fwrite($fp, 'FactorioChannelID = "InsertChatIdOrServerWillNotWork"');
		fwrite($fp, "\n");
		fwrite($fp, 'FactorioChannelID = "InsertChatIdOrServerWillNotWork"');
		fwrite($fp, "PassConsoleChat = false\n");
		fwrite($fp, 'EnableConsoleChannel = false');
		fwrite($fp, "\n");
		fwrite($fp, 'FactorioConsoleChatID = "InsertConsoleChatIdIfYouWantToEnableConsoleChannelOrItWillNotWork');
		fwrite($fp, "\n");
		fwrite($fp, 'Executable = "../bin/x64/factorio"');
		fwrite($fp, "\n");
		fwrite($fp, 'LaunchParameters = "--start-server-load-scenario JoinAndPlayCoop-Scenario --port 34198 --rcon-port 34199 --rcon-password ChangeMeNOWWWWW"');
		fwrite($fp, "\n");
		fwrite($fp, 'AdminIDs = "InsertAllAdminsIdHere"');
		fwrite($fp, "\n");
		fwrite($fp, 'Prefix = "!"');
		fwrite($fp, "\n");
		fwrite($fp, 'ModListLocation = "../mods/mod-list.json"');
		fwrite($fp, "\n");
		fwrite($fp, 'ExitGracePeriod = 15');
		fclose($fp);

		$move3 = `mv tmp/serverManagerCreate.config ../server/${server}/ServerManager/.createenv`;
	} elseif ($action == "servermanager") {
		$serverManagerToken = $_POST['serverManagerToken'];
		$serverManagerFactorioChatID = $_POST['serverManagerFactorioChatID'];
		$serverManagerConsoleChat = $_POST['serverManagerConsoleChat'];
		$serverManagerConsoleChatID = $_POST['serverManagerConsoleChatID'];
		$serverManagerMapSettigns = $_POST['serverManagerMapSettigns'];
		$serverManagerMapGenSettigns = $_POST['serverManagerMapGenSettigns'];
		$serverManagerPort = $_POST['serverManagerPort'];
		$serverManagerRconPort = $_POST['serverManagerRconPort'];
		$serverManagerRconPassword = $_POST['serverManagerRconPassword'];

		$serverManagerResetChatID = $_POST['serverManagerResetChatID'];

		if (empty($serverManagerRconPassword)) {
			$serverManagerRconPassword = `sed -n '7p' ../server/${server}/ServerManager/.env | sed 's/"//g' | cut -c 20- | grep -o 'rcon-password .*' | cut -f1,2 -d' ' | awk '{gsub("rcon-password ", "");print}'`;
			$serverManagerRconPassword = substr($ManagerRconPassword, 0, -1);
		}


		$serverManagerAdmins = $_POST['serverManagerAdmins'];
		$serverManagerPrefix = $_POST['serverManagerPrefix'];
		$serverManagerModListLocation = $_POST['serverManagerModListLocation'];
		$serverManagerExitGracePeriod = $_POST['serverManagerExitGracePeriod'];


		/*Preparing the variables to insert*/

		$serverManagerToken = 'DiscordToken = "' . $serverManagerToken . '"';

		$serverManagerFactorioChatID = 'FactorioChannelID = "' . $serverManagerFactorioChatID . '"';

		$serverManagerPassConsoleChat = 'PassConsoleChat = false';

		/* Exception to remove console channel id if console chat is not used if you don't do that it will still pass console to the normal channel*/

		if ($serverManagerConsoleChat == "true") {

			$serverManagerConsoleChat = 'EnableConsoleChannel = ' . $serverManagerConsoleChat;

			$serverManagerConsoleChatID = 'FactorioConsoleChatID = "' . $serverManagerConsoleChatID . '"';
		} else {
			$serverManagerConsoleChat = 'EnableConsoleChannel = ' . $serverManagerConsoleChat;

			$serverManagerConsoleChatID = 'FactorioConsoleChatID = ""';
		}

		$serverManagerResetChatID = 'FactorioChannelID = "' . $serverManagerResetChatID . '"';

		$serverManagerExecutable = 'Executable = "../bin/x64/factorio"';

		if (empty($serverManagerMapSettigns)) {
			$serverManagerLaunchParameters = 'LaunchParameters = "--start-server ../saves/japc.zip --server-settings ../data/fctrserver.json --port ' . $serverManagerPort . ' --rcon-port ' . $serverManagerRconPort . ' --rcon-password ' . $serverManagerRconPassword . '"';
			if (empty($serverManagerMapGenSettigns)) {
				$serverManagerLaunchParametersCreate = 'LaunchParameters = "--start-server-load-scenario JoinAndPlayCoop-Scenario --port ' . $serverManagerPort . ' --rcon-port ' . $serverManagerRconPort . ' --rcon-password ' . $serverManagerRconPassword . '"';
			} else {
				$serverManagerLaunchParametersCreate = 'LaunchParameters = "--start-server-load-scenario JoinAndPlayCoop-Scenario --map-gen-settings ' . $serverManagerMapGenSettigns . ' --port ' . $serverManagerPort . ' --rcon-port ' . $serverManagerRconPort . ' --rcon-password ' . $serverManagerRconPassword . '"';
			}
		} else {
			$serverManagerLaunchParameters = 'LaunchParameters = "--start-server ../saves/japc.zip --server-settings ../data/fctrserver.json --map-settings ' . $serverManagerMapSettigns .  ' --port ' . $serverManagerPort . ' --rcon-port ' . $serverManagerRconPort . ' --rcon-password ' . $serverManagerRconPassword . '"';
			if (empty($serverManagerMapGenSettigns)) {
				$serverManagerLaunchParametersCreate = 'LaunchParameters = "--start-server-load-scenario JoinAndPlayCoop-Scenario --map-settings ' . $serverManagerMapSettigns .  ' --port ' . $serverManagerPort . ' --rcon-port ' . $serverManagerRconPort . ' --rcon-password ' . $serverManagerRconPassword . '"';
			} else {
				$serverManagerLaunchParametersCreate = 'LaunchParameters = "--start-server-load-scenario JoinAndPlayCoop-Scenario --map-settings ' . $serverManagerMapSettigns .  ' --map-gen-settings ' . $serverManagerMapGenSettigns . ' --port ' . $serverManagerPort . ' --rcon-port ' . $serverManagerRconPort . ' --rcon-password ' . $serverManagerRconPassword . '"';
			}
		}

		$serverManagerAdmins = 'AdminIDs = "' . $serverManagerAdmins . '"';

		$serverManagerPrefix = 'Prefix = "' . $serverManagerPrefix . '"';

		$serverManagerModListLocation = 'ModListLocation = "' . $serverManagerModListLocation . '"';

		$serverManagerExitGracePeriod = 'ExitGracePeriod = ' . $serverManagerExitGracePeriod;


		/*END*/

		$fp = fopen('tmp/serverManager.config', 'w') or die("Unable to open file!");
		fwrite($fp, "$serverManagerToken\n");
		fwrite($fp, "$serverManagerFactorioChatID\n");
		fwrite($fp, "$serverManagerPassConsoleChat\n");
		fwrite($fp, "$serverManagerConsoleChat\n");
		fwrite($fp, "$serverManagerConsoleChatID\n");
		fwrite($fp, "$serverManagerExecutable\n");
		fwrite($fp, "$serverManagerLaunchParameters\n");
		fwrite($fp, "$serverManagerAdmins\n");
		fwrite($fp, "$serverManagerPrefix\n");
		fwrite($fp, "$serverManagerModListLocation\n");
		fwrite($fp, "$serverManagerExitGracePeriod\n");
		fclose($fp);

		$move4 = `mv tmp/serverManager.config ../server/${server}/ServerManager/.env`;


		$fp = fopen('tmp/serverManagerCreate.config', 'w') or die("Unable to open file!");
		fwrite($fp, "$serverManagerToken\n");
		fwrite($fp, "$serverManagerResetChatID\n");
		fwrite($fp, "$serverManagerPassConsoleChat\n");
		fwrite($fp, "$serverManagerConsoleChat\n");
		fwrite($fp, "$serverManagerConsoleChatID\n");
		fwrite($fp, "$serverManagerExecutable\n");
		fwrite($fp, "$serverManagerLaunchParametersCreate\n");
		fwrite($fp, "$serverManagerAdmins\n");
		fwrite($fp, "$serverManagerPrefix\n");
		fwrite($fp, "$serverManagerModListLocation\n");
		fwrite($fp, "$serverManagerExitGracePeriod\n");
		fclose($fp);

		$move4 = `mv tmp/serverManagerCreate.config ../server/${server}/ServerManager/.createenv`;
	}





	echo "The config file has been changed, to go back to the panel press here <br>";
	echo '<a href="';
	echo "../admin.php?serverchoose=$server";
	echo '" class="btn btn-primary">Go back to the pannel</a>';

	?>
</div>