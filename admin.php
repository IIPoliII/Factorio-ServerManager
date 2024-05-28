<!doctype html>
<html>

<head>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

  <!-- Sweet alert 2 -->
  <link href="//cdn.jsdelivr.net/npm/@sweetalert2/theme-borderless@3/borderless.css" rel="stylesheet">
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@9/dist/sweetalert2.min.js"></script>

  <link rel=stylesheet href="https://s3-us-west-2.amazonaws.com/colors-css/2.2.0/colors.min.css">

  <link rel="stylesheet" type="text/css" href="styles.css">
  <link rel="icon" type="image/png" href="images/128.png" />
  <script src="https://kit.fontawesome.com/d34481d57e.js"></script>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <script src="js/admin.js"></script>

  <title>Admin Page</title>
</head>

<body>
  <?php
  //Blalal login check

  session_start();
  if (!isset($_SESSION['login'])) {
    header('LOCATION:login.php');
    die();
  }
  header('Cache-Control: max-age=900');

  //set factorio server

  $server = $_GET["serverchoose"];

  //This is for the menu but we want to set the server eariler to show it in the dialgo box

  $Option = `find server/ -maxdepth 1 -printf "%f\n" | grep -v server/ | grep -v .htaccess | sort -n -t _ -k 2`;
  $run = "1";
  foreach (preg_split("/((\r?\n)|(\r\n?))/", $Option) as $line) {
    if ($run == "1" && $server === NULL && $line !== '') {
      $server = "$line";
      $run = "2";
    }
  }

  ?>

  <div>
    <div id=Menu class="container col-sm-12">
      <div id=ServerChooseForm class="dropdown show float-left">
        <a class="btn btn-info dropdown-toggle" href="#" role="button" id="dropdownServer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <?php
          echo "Server : $server";
          ?>
        </a>

        <div class="dropdown-menu" aria-labelledby="dropdownServer">
          <?php
          foreach (preg_split("/((\r?\n)|(\r\n?))/", $Option) as $line) {
            if ($line !== '') {
              echo '<a class="dropdown-item" href="' . "scripts/launchAdminLoading.php?serverchoose=${line}" . '">' . $line . '</a>';
            }
          }
          if ($_SESSION["role"] == "Admin") {
            echo '<a class="dropdown-item" href="scripts/createServerDialog.php">Create a server</a>';
          }

          ?>
        </div>
        <a href="scripts/logout.php" class="btn btn-primary">Logout</a>
      </div>

      <?php
      //start of factorio code

      //Get factorio server path via the choosed server

      $serverpath  = "server/$server";

      //Get the server status

      $GameStateDown = `curl -s -X GET -H "Content-type: application/json" -H "Accept: application/json" "https://multiplayer.factorio.com/get-game-details/9999999999999"`;
      $Servertoken = `grep "Matching server game" "${serverpath}/factorio-current.log" 2> /dev/null | awk '{print $7}' | tail -1 | tail -c +2 | head -c -2`;
      $ServerAPI = `curl -s -X GET -H "Content-type: application/json" -H "Accept: application/json" "https://multiplayer.factorio.com/get-game-details/${Servertoken}"`;

      $ServerVersion = `grep "Loading mod base" ${serverpath}/factorio-current.log 2> /dev/null | awk '{print $5}' | tail -1`;

      $ServerWantedVersion = `sed -n '7p' ${serverpath}/server-config.txt`;
      $ServerWantedVersion = preg_replace('/\s+/', '', $ServerWantedVersion);

      $LastestVersion = `curl -sSf https://factorio.com/api/latest-releases | jq -r '.stable.headless'`;

      //Get the rcon port

      $RconPort = `grep -oP "rcon-port\K.*" "${serverpath}/factorio-current.log" | sed 's/"//g' | cut -c 2- | sed 's/\s.*$//'`;
      $RconPassword = `grep -oP "rcon-password\K.*" "${serverpath}/ServerManager/.env" | sed 's/"//g' | cut -c 2- | sed 's/\s.*$//'`;
      $RconPort = (int) $RconPort;

      $StatusIP = `nc -vz 127.0.0.1 $RconPort 2>&1`;

      if ($ServerAPI != $GameStateDown && strpos($ServerAPI, 'Not Found') === false && strpos($StatusIP, 'Connection refused') === false) {
        $state = "On";
      } else {
        $state = "Off";
      }
      //show Logs
      echo '<div id="TopMenu" class="float-right">
                  	<div id="DownloadLog">
                      <iframe style="display:none;" name="target"></iframe>';

      //end of start the div menu


      echo '<a class="btn btn-secondary" href="scripts/launchLastestLogDownload.php?serverchoose=';
      echo "$server";
      echo '" target="target">Lastest Log</a>';
      echo '<a class="btn btn-secondary" href="scripts/launchOldLogDownload.php?serverchoose=';
      echo "$server";
      echo '" target="target">Old Log</a>';

      //Show Start,stop,restart, update
      echo '<a class="btn btn-success" href="scripts/launchLoadingServer.php';
      echo "?serverchoose=$server&action=Start&state=$state";
      echo '">Start</a>';

      echo '<a class="btn btn-danger" href="scripts/launchLoadingServer.php';
      echo "?serverchoose=$server&action=Stop&state=$state";
      echo '">Stop</a>';

      echo '<a class="btn btn-primary" href="scripts/launchLoadingServer.php';
      echo "?serverchoose=$server&action=Restart&state=$state";
      echo '">Restart</a>';

      if ($ServerVersion == $LastestVersion) {
        echo '<a class="btn btn-info" href="scripts/launchLoadingServer.php';
        echo "?serverchoose=$server&action=Update&state=$state";
        echo '">Update</a>';
      } else {

        echo '<a class="btn btn-warning" style="color:red;" href="scripts/launchLoadingServer.php';
        echo "?serverchoose=$server&action=Update&state=$state";
        echo '">Update';
        echo " $LastestVersion</a>";
      }

      echo '<a class="btn btn-info" href="scripts/launchScenarioUpdate.php';
      echo "?serverchoose=$server";
      echo '">Update the scenario</a>';

      //Show save manager

      echo '<a class="btn btn-info" href="saveManager.php';
      echo "?serverchoose=$server&serverpath=$serverpath";
      echo '">Save Manager</a>';
      echo "</div></div></div>";


      //Get the number of players online

      $NumberPlayersOnline = `curl -s --request GET https://multiplayer.factorio.com/get-game-details/${Servertoken} | jq ".players | length"`;

      //Get the size of the save file

      $SaveFileSize = `du -m ${serverpath}/saves/japc.zip | cut -f1`;

      //Creating the container for the log and the left panel
      echo '<div class="container-fluid row" id="BodyContainer">';

      echo '<div class="col-sm-2" id="LeftTableDiv"> </div>';

      echo '<div class="col-sm-10">';
      echo '<table id="LiveLogTable">
                      <tr><td>
                          <div id="feed" class="feed"></div>
                      </td></tr>';



      ?>
      <tr>
        <td>
          <div id="Command">
            <form id="CommandForm" onsubmit="event.preventDefault(); SumbitCommand();" method="POST">
              <br><input id="CommandBox" type="text" name="Command" /></br>
              <br><input class="btn btn-primary" type="submit" value="Send Command" name="Send Command" /></br>
            </form>
          </div>
        </td>
      </tr>
      <tr>
        <td>
          <div id="CommandResult"></div>
        </td>
      </tr>
    </div>
    </table>
  </div>

  <script>
    function SumbitCommand() {
      var command = $("#CommandBox").val();
      var server = '<?php echo $server; ?>';

      $.post("scripts/sendCommand.php", {
          command: command,
          server: server
        },
        function(data) {
          $('#CommandResult').html(data);
          $('#CommandForm')[0].reset();
        });
      

      function ScrollToBottom() {
        var objDiv = document.getElementById("feed");
        objDiv.scrollTop = objDiv.scrollHeight;
      }
      ScrollToBottom();
      setTimeout(ScrollToBottom, 1000);

      return false;
    }
  </script>



  <div>

    <script type="text/javascript">
      //Automatticly reload the console witout moving the cursor
      //if you stop and move up it stops scrolling automatically

      var refreshtime = 100;

      var server = '<?php echo $server; ?>';

      function tc() {
        asyncAjax("GET", "scripts/getLog.php?serverchoose=" + server + "&", Math.random(), display, {});
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

      //Refresh the left table every 5 seconds and load it
      $('#LeftTableDiv').load("scripts/infoTable.php?serverchoose=" + server)
      var autoRefreshLeftTable = setInterval(
        function() {
          $('#LeftTableDiv').load("scripts/infoTable.php?serverchoose=" + server)
        }, 5000);
    </script>
    <?php
    //Add the version at the bottom left

    $PanelVersion = `cat version.txt | head -n 1`;
    echo '<div class="align-bottom" style="margin-left:10%;"><a href="version.txt">' . "Version $PanelVersion" . "</a></div>";
    ?>
  </div>
  </div>
</body>

</html>
