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
		header('LOCATION:../login.php');
		die();
	}

	use cronmanager\src\CrontabManager;

	$role = $_SESSION["role"];

	$server = $_GET['serverchoose'];

	$path = getcwd();

	$serverpath = "${path}/../server/${server}";

	$serverwebhook = `sed -n '9p' ../config.txt | sed 's/"//g' | cut -c 20-`;
	$serverwebhook = substr($serverwebhook, 0, -1);

	$gitLink = `sed -n '3p' ../server/$server/server-config.txt`;
	$gitLink = substr($gitLink, 0, -1);
	$gitPath = `sed -n '5p' ../server/$server/server-config.txt`;
	$gitPath = substr($gitPath, 0, -1);

	$version = `sed -n '7p' ${serverpath}/server-config.txt | sed 's/"//g'`;

	echo '<a class="btn btn-info" href="';
	echo "launchAdminLoading.php?serverchoose=$server";
	echo '" class="button">Go back to the pannel</a><br>';

	if ($role != "Admin") {
		// limit to admins only
		exit("Sorry you aren't an administartor please go back to the panel");
	} else {
		//Get all variables of the form

		//Auto Perms

		$AutoPermsActive = $_POST['AutoPermsActive'];
		$AutoPermsTimer = $_POST['AutoPermsTimer'];

		//Saves

		$SavesActive = $_POST['SavesActive'];
		$SavesTimer  = $_POST['SavesTimer'];

		//Stats

		$StatsActive = $_POST['StatsActive'];
		$StatsTimer = $_POST['StatsTimer'];

		//Updates

		$UpdatesActive = $_POST['UpdatesActive'];
		$UpdatesTimer = $_POST['UpdatesTimer'];
		$UpdatesTimerDay = $_POST['UpdatesTimerDay'];

		//Restart

		$RestartActive = $_POST['RestartActive'];
		$RestartTimer = $_POST['RestartTimer'];
		$RestartTimerDay = $_POST['RestartTimerDay'];

		//Scenario Update

		$ScenarioUpdateActive = $_POST['ScenarioUpdateActive'];
		$ScenarioUpdateTimer = $_POST['ScenarioUpdateTimer']; //This is in hours and not minutes like the others
		$ScenarioUpdateTimerDay  = $_POST['ScenarioUpdateTimerDay'];

		//Time Message

		$TimeActive = $_POST['TimeActive'];
		$TimeTimer = $_POST['TimeTimer'];

		//Rocket Reset

		$RocketResetActive = $_POST['RocketResetActive'];
		$RocketResetTimer = $_POST['RocketResetTimer'];

		//Reset

		$ResetActive = $_POST['ResetActive'];
		$ResetTimer = $_POST['ResetTimer'];
		$ResetTimerDay = $_POST['ResetTimerDay'];

		//Rocket Reset Information

		$RocketResetInformActive = $_POST['RocketResetInformActive'];
		$RocketResetInformTimer = $_POST['RocketResetInformTimer'];

		//Rocket Info

		$RocketInformActive = $_POST['RocketInformActive'];
		$RocketInformTimer = $_POST['RocketInformTimer'];

		//Info ads

		$InformActive = $_POST['InformActive'];
		$InformTimer = $_POST['InformTimer'];

		//Info reset

		$ResetInformActive = $_POST['ResetInformActive'];
		$ResetInformTimer = $_POST['ResetInformTimer'];

		//Start of the really intressting parts about checks
		echo '<table border="1" class="table" id="LogTable"><tr><td><div id="feed">';

		//Autoperms active ?

		if (empty($AutoPermsActive)) {
			// If empty disable Autoperms

			$exec = `crontab -l | grep -v "${path}/factorio-task.sh --autoperms ${serverpath}" | crontab -`;
			echo "Auto perms has been disabled !<br>";
		} else {
			//remove the current crontabs

			$exec = `crontab -l | grep -v "${path}/factorio-task.sh --autoperms ${serverpath}" | crontab -`;

			//add all crontabs again with the new one

			$exec = `(crontab -l; echo "*/${AutoPermsTimer} * * * * ${path}/factorio-task.sh --autoperms ${serverpath}") | crontab -`;
			echo "Auto perms has been turned on !<br>";
		}


		//Saves active ?

		if (empty($SavesActive)) {
			// If empty disable saves

			$exec = `crontab -l | grep -v "${path}/factorio-task.sh --save ${serverpath} ${server}" | crontab -`;
			echo "Saving has been disabled !<br>";
		} else {
			//remove the current crontabs

			$exec = `crontab -l | grep -v "${path}/factorio-task.sh --save ${serverpath} ${server}" | crontab -`;

			//add all crontabs again with the new one

			$exec = `(crontab -l; echo "*/${SavesTimer} * * * * ${path}/factorio-task.sh --save ${serverpath} ${server}") | crontab -`;
			echo "Saving has been turned on !<br>";
		}

		//Stats active ?

		if (empty($StatsActive)) {
			// If empty disable stats

			$exec = `crontab -l | grep -v "${path}/Factorio-SQL.sh ${server}" | crontab -`;
			echo "Stats has been disabled !<br>";
		} else {
			//remove the current crontabs

			$exec = `crontab -l | grep -v "${path}/Factorio-SQL.sh ${server}" | crontab -`;

			//add all crontabs again with the new one

			$exec = `(crontab -l; echo "*/${StatsTimer} * * * * ${path}/Factorio-SQL.sh ${server}") | crontab -`;
			echo "Stats has been turned on !<br>";
		}

		//Update active ?

		if (empty($UpdatesActive)) {
			// If empty disable updates

			$exec = `crontab -l | grep -v "${path}/factorio-task.sh --update ${serverpath}" | crontab -`;
			echo "Updating has been disabled !<br>";
		} else {
			//remove the current crontabs

			$exec = `crontab -l | grep -v "${path}/factorio-task.sh --update ${serverpath}" | crontab -`;

			//add all crontabs again with the new one

			$exec = `(crontab -l; echo "0 ${UpdatesTimer} * * ${UpdatesTimerDay} ${path}/factorio-task.sh --update ${serverpath} ${serverwebhook} ${server} ${version}") | crontab -`;
			echo "Updating has been turned on !<br>";
		}

		//Restart active ?

		if (empty($RestartActive)) {
			// If empty disable restart

			$exec = `crontab -l | grep -v "${path}/factorio-task.sh --restart ${serverpath}" | crontab -`;
			echo "Restart has been disabled !<br>";
		} else {
			//remove the current crontabs

			$exec = `crontab -l | grep -v "${path}/factorio-task.sh --restart ${serverpath}" | crontab -`;

			//add all crontabs again with the new one

			$exec = `(crontab -l; echo "0 ${RestartTimer} * * ${RestartTimerDay} ${path}/factorio-task.sh --restart ${serverpath} ${server}") | crontab -`;
			echo "Restart has been turned on !<br>";
		}

		//Scenario update active ?

		if (empty($ScenarioUpdateActive)) {
			// If empty disable scenario update

			$exec = `crontab -l | grep -v "${path}/factorio-task.sh --update-scenario ${serverpath}" | crontab -`;
			echo "Scenario Update has been disabled !<br>";
		} else {
			//remove the current crontabs

			$exec = `crontab -l | grep -v "${path}/factorio-task.sh --update-scenario ${serverpath}" | crontab -`;

			//add all crontabs again with the new one

			$exec = `(crontab -l; echo "0 ${ScenarioUpdateTimer} * * ${ScenarioUpdateTimerDay} ${path}/factorio-task.sh --update-scenario ${serverpath} $(echo $gitLink) $(echo $gitPath) $(echo $server)") | crontab -`;
			echo "Scenario Update has been turned on !<br>";
		}

		//Time active ?

		if (empty($TimeActive)) {
			// If empty disable sTime

			$exec = `crontab -l | grep -v "${path}/factorio-task.sh --time ${serverpath}" | crontab -`;
			echo "Time has been disabled !<br>";
		} else {
			//remove the current crontabs

			$exec = `crontab -l | grep -v "${path}/factorio-task.sh --time ${serverpath}" | crontab -`;

			//add all crontabs again with the new one

			$exec = `(crontab -l; echo "*/${TimeTimer} * * * * ${path}/factorio-task.sh --time ${serverpath}") | crontab -`;
			echo "Time has been turned on !<br>";
		}


		//Rocket Reset active ?

		if (empty($RocketResetActive)) {
			// If empty disable Rocket Reset

			$exec = `crontab -l | grep -v "${path}/factorio-task.sh --rocket-reset ${serverpath} ${serverwebhook} ${server}" | crontab -`;
			echo "Rocket Reset has been disabled !<br>";
		} else {
			//remove the current crontabs

			$exec = `crontab -l | grep -v "${path}/factorio-task.sh --rocket-reset ${serverpath} ${serverwebhook} ${server}" | crontab -`;

			//add all crontabs again with the new one

			$exec = `(crontab -l; echo "*/${TimeTimer} * * * * ${path}/factorio-task.sh --rocket-reset ${serverpath} ${serverwebhook} ${server}") | crontab -`;
			echo "Rocket Reset has been turned on !<br>";
		}

		//Reset active ?

		if (empty($ResetActive)) {
			// If empty disable reset

			$exec = `crontab -l | grep -v "${path}/factorio-task.sh --reset ${serverpath} ${serverwebhook} ${server}" | crontab -`;
			echo "Reset has been disabled !<br>";
		} else {
			//remove the current crontabs

			$exec = `crontab -l | grep -v "${path}/factorio-task.sh --reset ${serverpath} ${serverwebhook} ${server}" | crontab -`;

			//add all crontabs again with the new one

			$exec = `(crontab -l; echo "0 ${ResetTimer} * * ${ResetTimerDay} ${path}/factorio-task.sh --reset ${serverpath} ${serverwebhook} ${server}") | crontab -`;
			echo "Reset has been turned on !<br>";
		}

		//Rocket Goal Message active ?

		if (empty($RocketResetInformActive)) {
			// If empty disable Rocket Goal Message

			$exec = `crontab -l | grep -v "${path}/factorio-task.sh --rocket-reset-inform ${serverpath} ${server}" | crontab -`;
			echo "Rocket Goal Message has been disabled !<br>";
		} else {
			//remove the current crontabs

			$exec = `crontab -l | grep -v "${path}/factorio-task.sh --rocket-reset-inform ${serverpath} ${server}" | crontab -`;

			//add all crontabs again with the new one

			$exec = `(crontab -l; echo "*/${RocketResetInformTimer} * * * * ${path}/factorio-task.sh --rocket-reset-inform ${serverpath} ${server}") | crontab -`;
			echo "Rocket Goal Message has been turned on !<br>";
		}

		//Rocket Message active ?

		if (empty($RocketInformActive)) {
			// If empty disable Rocket Message

			$exec = `crontab -l | grep -v "${path}/factorio-task.sh --rocket-sended ${serverpath} ${server}" | crontab -`;
			echo "Rocket Message has been disabled !<br>";
		} else {
			//remove the current crontabs

			$exec = `crontab -l | grep -v "${path}/factorio-task.sh --rocket-sended ${serverpath} ${server}" | crontab -`;

			//add all crontabs again with the new one

			$exec = `(crontab -l; echo "*/${RocketInformTimer} * * * * ${path}/factorio-task.sh --rocket-sended ${serverpath} ${server}") | crontab -`;
			echo "Rocket Message has been turned on !<br>";
		}


		//Inform message active ?

		if (empty($InformActive)) {
			// If empty disable Inform message

			$exec = `crontab -l | grep -v "${path}/factorio-task.sh --inform ${serverpath}" | crontab -`;
			echo "Inform Message has been disabled !<br>";
		} else {
			//remove the current crontabs

			$exec = `crontab -l | grep -v "${path}/factorio-task.sh --inform ${serverpath}" | crontab -`;

			//add all crontabs again with the new one

			$exec = `(crontab -l; echo "*/${InformTimer} * * * * ${path}/factorio-task.sh --inform ${serverpath}") | crontab -`;
			echo "Inform Message has been turned on !<br>";
		}

		//Reset info message active ?

		if (empty($ResetInformActive)) {
			// If empty disable Reset Inform message

			$exec = `crontab -l | grep -v "${path}/factorio-task.sh --inform-reset ${serverpath}" | crontab -`;
			echo "Reset info message has been disabled !<br>";
		} else {
			//remove the current crontabs

			$exec = `crontab -l | grep -v "${path}/factorio-task.sh --inform-reset ${serverpath}" | crontab -`;

			//add all crontabs again with the new one

			//More complicated to get the day we reset

			if (empty($ResetActive)) {
				echo "Reset info message can't be turned on since reset isn't turned on (not rocket reset)";
			} else {

				if ($ResetTimerDay == "*") {
					$ResetInformMessage = "Everyday";
				} elseif ($ResetTimerDay == "MON") {
					$ResetInformMessage = "Monday";
				} elseif ($ResetTimerDay == "TUE") {
					$ResetInformMessage = "Tuesday";
				} elseif ($ResetTimerDay == "WED") {
					$ResetInformMessage = "Wednesday";
				} elseif ($ResetTimerDay == "THU") {
					$ResetInformMessage = "Thursday";
				} elseif ($ResetTimerDay == "FRI") {
					$ResetInformMessage = "Friday";
				} elseif ($ResetTimerDay == "SAT") {
					$ResetInformMessage = "Saturday";
				} elseif ($ResetTimerDay == "SUN") {
					$ResetInformMessage = "Sunday";
				}

				$ResetInformMessage = "'" . "$ResetInformMessage at " . "${ResetTimer}" . "'";

				$exec = `(crontab -l; echo "*/${ResetInformTimer} * * * * ${path}/factorio-task.sh --inform-reset ${serverpath} ${ResetInformMessage}") | crontab -`;
				echo "Reset info message has been turned on !<br>";
			}
		}

		echo '</div></td></tr></table>';
	}

	?>
</div>