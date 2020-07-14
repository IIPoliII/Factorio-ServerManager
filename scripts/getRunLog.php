<?php
session_start();
            if(!isset($_SESSION['login'])) {
              header('LOCATION:../login.php'); die();
            }

$log = $_GET["log"];

$filename  = "log/run${log}.log";

$output = `exec tail -n1000 $filename`;

echo str_replace(PHP_EOL, '<br />', $output);
