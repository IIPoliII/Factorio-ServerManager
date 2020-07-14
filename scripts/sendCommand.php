<?php
session_start();
            if(!isset($_SESSION['login'])) {
              header('LOCATION:../login.php'); die();
            }

$server = $_POST['server'];
$command = $_POST['command'];

if ($command === "") {
	echo "You need to input a command";
} else {
	$runcommand = `./factorio-task.sh --run-command "../server/$server" "$command"`;


	if ($runcommand === "") {
		echo "The command you ran didn't throwed any output";
	} elseif (empty($runcommand)) {
		echo "The command you ran didn't throwed any output";
	} elseif (!isset($runcommand)) {
		echo "The command you ran didn't throwed any output";
	} else {
		echo "Ouput of the command : $runcommand";
	}
}
