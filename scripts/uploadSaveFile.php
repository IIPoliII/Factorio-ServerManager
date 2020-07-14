<?php
  session_start();
  if(!isset($_SESSION['login'])) {
    header('LOCATION:login.php'); die();
  }
  header('Cache-Control: max-age=900');

  $exec = shell_exec("rm -rf tmp/*");

  $server = $_GET['serverchoose'];

  $uploadfile=$_FILES["upload_file"]["tmp_name"];
  $save=$_FILES["upload_file"]["name"];
  $folder="tmp/";
  move_uploaded_file($_FILES["upload_file"]["tmp_name"], $folder.$_FILES["upload_file"]["name"]);
  
  $exec = shell_exec("./runfactorio-task.sh --rollback ../server/$server $server tmp/${save}");
