<!doctype html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.js"></script>

<script type="text/javascript" src="js/saveUploadProgress.js"></script>

<link rel=stylesheet href="https://s3-us-west-2.amazonaws.com/colors-css/2.2.0/colors.min.css">

<link rel="stylesheet" type="text/css" href="styles.css">

<link rel="icon" type="image/png" href="images/128.png" />
<script src="https://kit.fontawesome.com/d34481d57e.js"></script>
<title>Save Manager</title>
<link rel="icon" type="image/png" href="images/128.png" />
<style>
        .topright {
                position: relative;
                position: absolute;

                top: 8px;

                right: 16px;
        }
</style>
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



$user = $_SESSION["user"];
$serverpath = $_GET['serverpath'];

$server = $_GET['serverchoose'];
/* All this show the houses and the buttons in the top tab*/
echo '<div class="conatiner container-fluid">';
echo '<table class="table" id="PlayerManagerMainTable"><tr><td>';
echo '<div class="float-right">';

echo '<a href="';
echo "scripts/launchAdminLoading.php?serverchoose=${server}";
echo '" class="button""><i class="fas fa-home fa-2x"></i></span></a>';
echo '<button type="submit" style="background: none; color: inherit; border: none; font: inherit; outine: inherit;" onClick="refreshPage()"><i class="fas fa-sync fa-2x"></i></button>';
echo '</div>';


echo '<div class="float-left">';
echo '<a class="btn btn-danger btn-sm" href="';
echo "scripts/rollBack.php?serverchoose=${server}&action=deleteAll&serverpath=${serverpath}";
echo '" onclick="return confirm(\'Are you sure?\');">';
echo "Delete all the saves";
echo '</span></a>';
echo '</div>';

/*To get the file size by Mogilev Arseny*/

function FileSizeConvert($bytes)
{
        $bytes = floatval($bytes);
        $arBytes = array(
                0 => array(
                        "UNIT" => "TB",
                        "VALUE" => pow(1024, 4)
                ),
                1 => array(
                        "UNIT" => "GB",
                        "VALUE" => pow(1024, 3)
                ),
                2 => array(
                        "UNIT" => "MB",
                        "VALUE" => pow(1024, 2)
                ),
                3 => array(
                        "UNIT" => "KB",
                        "VALUE" => 1024
                ),
                4 => array(
                        "UNIT" => "B",
                        "VALUE" => 1
                ),
        );

        foreach ($arBytes as $arItem) {
                if ($bytes >= $arItem["VALUE"]) {
                        $result = $bytes / $arItem["VALUE"];
                        $result = str_replace(".", ",", strval(round($result, 2))) . " " . $arItem["UNIT"];
                        break;
                }
        }
        return $result;
}

$lastestSaves = "${serverpath}/LastestSaves";
$lastestSavesDirectory = array_diff(scandir($lastestSaves, 1), array('..', '.'));

$oldSaves = "${serverpath}/OldSaves";
$oldSavesDirectory = array_diff(scandir($oldSaves, 1), array('..', '.'));

$resetSaves = "${serverpath}/ResetSaves";
$resetSavesDirectory = array_diff(scandir($resetSaves, 1), array('..', '.'));

echo '<h2>Save Manager</h2></td></tr></table>';
?>
<h2 class="center">Upload FileSave</h2>
<?php
echo '<form style="text-align: center;" action="' . "scripts/uploadSaveFile.php?serverchoose=${server}&serverpath=${serverpath}" . '" id="saveUploadForm" name="saveUploadForm" method="post" enctype="multipart/form-data">';
?>
<input class="btn btn-info" type="file" id="upload_file" class="btn btn-info btn-sm" name="upload_file" />
<input class="btn btn-primary" type="submit" name='submitSave' value="Upload" onclick='uploadSave();' />
</form>
<div class='progress' id="saveProgressBar" style="width: 0%; margin-left: auto; margin-right: auto; background-color: coral;">
        <div id='bar1' class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
</div>
<div id="textbar" class="center"></div>
<?php
echo '<h2 class="center">Lastest Saves</h2>';

echo '<table border="1" class="center table" align="center"><tr><th>Save Name</th>';

echo '<td></td><td></td>';

echo "<th>";

echo '<a class="btn btn-warning btn-sm" href="';
echo "scripts/rollBack.php?serverchoose=${server}&action=deleteFolder&serverpath=${serverpath}&save=LastestSaves";
echo '" onclick="return confirm(\'Are you sure?\');">';
echo "Delete all the lastest saves";
echo '</span></a>';

echo "</th></tr>";

foreach ($lastestSavesDirectory as $lastestSave) {
        echo '<tr>';

        echo '<td>';

        echo '<a href="' . "${lastestSaves}/${lastestSave}" . '" >' . " $lastestSave " .  "</a>";

        echo '</td>';

        echo '<td>';

        $lastestSaveFileSize = filesize("${lastestSaves}/${lastestSave}");

        $lastestSaveFileSize = FileSizeConvert($lastestSaveFileSize);

        echo "$lastestSaveFileSize";

        echo '</td>';

        echo '<td>';

        echo '<a class="btn btn-info btn-sm" href="';
        echo "scripts/rollBack.php?serverchoose=${server}&save=${lastestSaves}/${lastestSave}&action=rollback&serverpath=${serverpath}";
        echo '" >';
        echo " Rollback to this save ";
        echo '</span></a>';

        echo '</td>';

        echo '<td>';

        echo '<a class="btn btn-danger btn-sm" href="';
        echo "scripts/rollBack.php?serverchoose=${server}&save=${lastestSaves}/${lastestSave}&action=delete&serverpath=${serverpath}";
        echo '" >';
        echo " Delete this save ";
        echo '</span></a>';

        echo '</td>';


        echo '</tr>';
}
echo '</table>';

echo '<h2 class="center">Old Saves</h2>';
echo '<table border="1" class="center table" align="center"><tr><th>Save Name</th>';

echo '<td></td><td></td>';

echo "<th>";

echo '<a class="btn btn-warning btn-sm" href="';
echo "scripts/rollBack.php?serverchoose=${server}&action=deleteFolder&serverpath=${serverpath}&save=OldSaves";
echo '" onclick="return confirm(\'Are you sure?\');">';
echo "Delete all the old saves";
echo '</span></a>';

echo "</th></tr>";

foreach ($oldSavesDirectory as $oldSave) {
        echo '<tr>';

        echo '<td>';

        echo '<a href="' . "${oldSaves}/${oldSave}" . '" >' . " $oldSave " .  "</a>";

        echo '</td>';

        echo '<td>';

        $oldSaveFileSize = filesize("${oldSaves}/${oldSave}");

        $oldSaveFileSize = FileSizeConvert($oldSaveFileSize);

        echo "$oldSaveFileSize";

        echo '</td>';

        echo '<td>';

        echo '<a class="btn btn-info btn-sm" href="';
        echo "scripts/rollBack.php?serverchoose=${server}&save=${oldSaves}/${oldSave}&action=rollback&serverpath=${serverpath}";
        echo '" >';
        echo " Rollback to this save ";
        echo '</span></a>';

        echo '</td>';

        echo '<td>';

        echo '<a class="btn btn-danger btn-sm" href="';
        echo "scripts/rollBack.php?serverchoose=${server}&save=${oldSaves}/${oldSave}&action=delete&serverpath=${serverpath}";
        echo '" >';
        echo " Delete this save ";
        echo '</span></a>';

        echo '</td>';

        echo '</tr>';
}

echo '</table>';

echo '<h2 class="center">Reset Saves</h2>';
echo '<table border="1" class="center table" align="center"><tr><th>Save Name</th>';


echo '<td></td><td></td>';

echo "<th>";

echo '<a class="btn btn-warning btn-sm" href="';
echo "scripts/rollBack.php?serverchoose=${server}&action=deleteFolder&serverpath=${serverpath}&save=ResetSaves";
echo '" onclick="return confirm(\'Are you sure?\');">';
echo "Delete all the reset saves";
echo '</span></a>';

echo "</th></tr>";

foreach ($resetSavesDirectory as $resetSave) {
        echo '<tr>';

        echo '<td>';

        echo '<a href="' . "${resetSaves}/${resetSave}" . '" >' . " $resetSave " .  "</a>";

        echo '</td>';

        echo '<td>';

        $resetSaveFileSize = filesize("${resetSaves}/${resetSave}");

        $resetSaveFileSize = FileSizeConvert($resetSaveFileSize);

        echo "$resetSaveFileSize";

        echo '</td>';

        echo '<td>';

        echo '<a class="btn btn-info btn-sm" href="';
        echo "scripts/rollBack.php?serverchoose=${server}&save=${resetSaves}/${resetSave}&action=rollback&serverpath=${serverpath}";
        echo '" >';
        echo " Rollback to this save ";
        echo '</span></a>';

        echo '</td>';

        echo '<td>';

        echo '<a class="btn btn-danger btn-sm" href="';
        echo "scripts/rollBack.php?serverchoose=${server}&save=${resetSaves}/${resetSave}&action=delete&serverpath=${serverpath}";
        echo '" >';
        echo " Delete this save ";
        echo '</span></a>';

        echo '</td>';

        echo '</tr>';
}

echo '</table>';

echo '</div>';


?>