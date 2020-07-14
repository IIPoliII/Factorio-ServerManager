<!doctype html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<link rel="icon" type="image/png" href="../images/128.png" />

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<link rel=stylesheet href="https://s3-us-west-2.amazonaws.com/colors-css/2.2.0/colors.min.css">

<link rel="stylesheet" type="text/css" href="../styles.css">

<script>
    function goBack() {
        window.history.back();
    }
</script>

<script src="https://kit.fontawesome.com/d34481d57e.js"></script>
<title>Server creation</title>
<link rel="icon" type="image/png" href="images/128.png" />

<div class="container container-fluid">
    <?php
    session_start();
    if (!isset($_SESSION['login'])) {
        header('LOCATION:login.php');
        die();
    }
    header('Cache-Control: max-age=900');
    $role = $_SESSION["role"];
    if ($role != "Admin") {
        header('LOCATION:launchAdminLoading.php');
    }

    $NewServer = $_POST['ServerName'];

    if (strpos(trim($NewServer), ' ') !== false) {
        echo "The server need to have only one word (with number append) and not any special characters";
        echo '<button class="btn btn-info" onclick="goBack()">Go Back</button>';
        exit();
    } else {

        if (preg_match('/^[\w]+$/', $NewServer)) {

            $Option = `find ../server/ -maxdepth 1 -printf "%f\n" | grep -v server/ | grep -v .htaccess | sort -n -t _ -k 2`;

            foreach (preg_split("/((\r?\n)|(\r\n?))/", $Option) as $line) {
                if ("$line" == "$NewServer") {
                    echo 'You cannot create a server with the same name as another';
                    exit();
                }
            }

            echo "Starting to create the new server <br>";

            $exec = `./runfactorio-task.sh --install-via-webpanel ../server $(echo "$NewServer")`;

            $exec = substr($exec, 0, -1);

            echo '<a href="';
            echo "../serverConfiguration.php?serverchoose=$NewServer";
            echo '" class="btn btn-primary">Go back to the pannel</a><br>';

            echo "Remember you need to change settigns in server config for it to run correctly";


            echo "Live log below : <br>";
        } else {
            echo "You need to input only characters like A-Z 0-9 for the server name <br> You entred $NewServer";
            echo '<button class="btn btn-info" onclick="goBack()">Go Back</button>';
            exit();
        }
    }


    ?>

    <table border="1" id="LogTable" class="table">
        <tr>
            <td>
                <div id="feed"></div>
            </td>
        </tr>
    </table>

</div>
<script type="text/javascript">
    var refreshtime = 100;

    var exec = '<?php echo $exec; ?>';

    function tc() {
        asyncAjax("GET", "getRunLog.php?log=" + exec + "&", Math.random(), display, {});
        if (autoScroll) {
            ScrollChat();
        }
        setTimeout(tc, refreshtime);
    }

    function display(xhr, cdat) {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("feed").innerHTML = xhr.responseText;
        }
    }

    function asyncAjax(method, url, qs, callback, callbackData) {
        var xmlhttp = new XMLHttpRequest();
        //xmlhttp.cdat=callbackData;
        if (method == "GET") {
            url += "?" + qs;
        }
        var cb = callback;
        callback = function() {
            var xhr = xmlhttp;
            var cdat2 = callbackData;
            cb(xhr, cdat2);
            return;
        }
        xmlhttp.open(method, url, true);
        xmlhttp.onreadystatechange = callback;
        if (method == "POST") {
            xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xmlhttp.send(qs);
        } else {
            xmlhttp.send(null);
        }
    }
    tc();

    var autoScroll = true;

    function ScrollChat() {
        $('#feed').scrollTop($('#feed')[0].scrollHeight).trigger('scroll');
    }
    ScrollChat();

    $('#feed').on('scroll', function() {
        if ($(this).scrollTop() < this.scrollHeight - $(this).height()) {
            autoScroll = false;
        } else {
            autoScroll = true;
        }
    });
</script>