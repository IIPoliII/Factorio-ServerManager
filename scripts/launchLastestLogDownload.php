<?php
session_start();
            if(!isset($_SESSION['login'])) {
              header('LOCATION:../login.php'); die();
            }

$server = $_GET['serverchoose'];
$cwd = dirname(__FILE__);
$RemoveLog = `rm -rf $cwd/tmp/factorioCurrent.log`;
$CopyLog = `cat ../server/$server/ServerManager/factorio.log | grep -v "RCON" | grep -v "stateChanged" | grep -v "HeartbeatSequenceNumber" | grep -v "Saving process PID" | grep -v "ChildProcessAgent" >> $cwd/tmp/factorioCurrent.log`;

$file = "$cwd/tmp/factorioCurrent.log";

forceDownLoad($file);

function forceDownLoad($filename)
{

	header("Pragma: public");
	header("Expires: 0"); // set expiration time
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/force-download");
	header("Content-Type: application/octet-stream");
	header("Content-Type: application/download");
	header("Content-Disposition: attachment; filename=".basename($filename).";");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".filesize($filename));

	@readfile($filename);
	exit(0);
}
