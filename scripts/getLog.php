<?php
session_start();
            if(!isset($_SESSION['login'])) {
              header('LOCATION:../login.php'); die();
            }
$filename = $_GET['serverchoose'];

$filename  = "../server/${filename}/ServerManager/factorio.log";

$output = `exec tail -n100000 $filename | grep -v "RCON" | tail -n350`;

echo str_replace(PHP_EOL, '<br />', $output);
