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

        $role = $_SESSION["role"];

        $server = $_GET['serverchoose'];

        $while = "1";

        $minutes = "default";

        if ($role != "Admin") {
                // limit to admins only
                exit("Sorry you aren't an administartor please go back to the panel");
        } else {
                echo '<div class="col-sm-4">';

                echo '<table class="table table-borderless"><tr><td>';
                echo '<a href="';
                echo "launchAdminLoading.php?serverchoose=${server}";
                echo '" class="button" style="color:black;"><i class="fas fa-home fa-2x"></i></a>';
                echo '</td>';

                echo '</table>';
                echo '</div>';


                //Start of the form for the action

                echo '<form class="form-group" action="changeSchdulerConfig.php';
                echo "?serverchoose=${server}";
                echo '" autocomplete="off" method="post">';
                echo "<br>";


                //Start of the main table were the jobs are

        ?>
                <table class="table">
                        <tr>
                                <th>Active</th>
                                <th>Action</th>
                                <th>Description</th>
                                <th>Every</th>
                        </tr>
                <?php
                //Autoperms
                echo "<tr>";


                $exsists = `crontab -l | grep -w "factorio-task.sh --autoperms" | grep -q "${server}" && echo 'Yes' || echo 'No'`;
                $exsists = substr($exsists, 0, -1);


                if ($exsists == "Yes") {
                        echo '<td><input type="checkbox" name="AutoPermsActive" value="box" checked></td>';
                        $minutes = `crontab -l | grep -w "factorio-task.sh --autoperms" | grep "${server}" | awk '{ print $1 }' | cut -c 3-`;
                } else {
                        echo '<td><input type="checkbox" name="AutoPermsActive" value="box"></td>';
                }

                echo "<td>Autoperms</td>";

                echo "<td>This is the automatic permissions checker it will check if the players have more than 3 hours (recommanded 1 minute)</td>";

                echo '<td><select class="form-control" name="AutoPermsTimer">';

                while ($while != "60") {
                        if ($while == $minutes) {
                                echo '<option value="' . $while . '" selected>' . $while . ' minutes</option>';
                        } else {
                                echo '<option value="' . $while . '">' . $while . ' minutes</option>';
                        }
                        $while++;
                }

                if ("60" == $minutes) {
                        echo '<option value="60" selected>1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                } elseif ("120" == $minutes) {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120" selected>2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                } elseif ("180" == $minutes) {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180" selected>3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                } elseif ("240" == $minutes) {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240" selected>4 hours</option>';
                } else {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                }

                $while = "1";

                $minutes = "default";

                echo '</select></td>';



                echo "</tr>";

                //End AutoPerms


                //Saves
                echo "<tr>";


                $exsists = `crontab -l | grep -w "factorio-task.sh --save" | grep -q "${server}" && echo 'Yes' || echo 'No'`;
                $exsists = substr($exsists, 0, -1);


                if ($exsists == "Yes") {
                        echo '<td><input type="checkbox" name="SavesActive" value="box" checked></td>';
                        $minutes = `crontab -l | grep -w "factorio-task.sh --save" | grep "${server}" | awk '{ print $1 }' | cut -c 3-`;
                } else {
                        echo '<td><input type="checkbox" name="SavesActive" value="box"></td>';
                }

                echo "<td>Saves</td>";

                echo "<td>This is the automatic save schedule if it's turned off we will never save except on stop</td>";

                echo '<td><select class="form-control" name="SavesTimer">';

                while ($while != "60") {
                        if ($while == $minutes) {
                                echo '<option value="' . $while . '" selected>' . $while . ' minutes</option>';
                        } else {
                                echo '<option value="' . $while . '">' . $while . ' minutes</option>';
                        }
                        $while++;
                }

                if ("60" == $minutes) {
                        echo '<option value="60" selected>1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                } elseif ("120" == $minutes) {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120" selected>2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                } elseif ("180" == $minutes) {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180" selected>3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                } elseif ("240" == $minutes) {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240" selected>4 hours</option>';
                } else {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                }

                $while = "1";

                $minutes = "default";

                echo '</select></td>';



                echo "</tr>";

                //End Saves

                //Stats
                echo "<tr>";


                $exsists = `crontab -l | grep -w "Factorio-SQL.sh" | grep -q "${server}" && echo 'Yes' || echo 'No'`;
                $exsists = substr($exsists, 0, -1);

                if ($exsists == "Yes") {
                        echo '<td><input type="checkbox" name="StatsActive" value="box" checked></td>';
                        $minutes = `crontab -l | grep -w "Factorio-SQL.sh" | grep "${server}" | awk '{ print $1 }' | cut -c 3-`;
                } else {
                        echo '<td><input type="checkbox" name="StatsActive" value="box"></td>';
                }

                echo "<td>Stats</td>";

                echo "<td>This is for stats like metal player stats,.... (it is required to have automatic permissions across the servers)</td>";

                echo '<td><select class="form-control" name="StatsTimer">';

                while ($while != "60") {
                        if ($while == $minutes) {
                                echo '<option value="' . $while . '" selected>' . $while . ' minutes</option>';
                        } else {
                                echo '<option value="' . $while . '">' . $while . ' minutes</option>';
                        }
                        $while++;
                }

                if ("60" == $minutes) {
                        echo '<option value="60" selected>1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                } elseif ("120" == $minutes) {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120" selected>2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                } elseif ("180" == $minutes) {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180" selected>3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                } elseif ("240" == $minutes) {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240" selected>4 hours</option>';
                } else {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                }

                $while = "1";

                $minutes = "default";

                echo '</select></td>';



                echo "</tr>";

                //End Stats

                //Updates
                echo "<tr>";


                $exsists = `crontab -l | grep -w "factorio-task.sh --update" | grep -q "${server}" && echo 'Yes' || echo 'No'`;
                $exsists = substr($exsists, 0, -1);

                if ($exsists == "Yes") {
                        echo '<td><input type="checkbox" name="UpdatesActive" value="box" checked></td>';
                        $minutes = `crontab -l | grep -w "factorio-task.sh --update" | grep "${server}" | awk '{ print $2 }' | head -n 1`;
                        $day = `crontab -l | grep -w "factorio-task.sh --update" | grep "${server}" | awk '{ print $5 }' | head -n 1 | tr -d '\n'`;
                } else {
                        echo '<td><input type="checkbox" name="UpdatesActive" value="box"></td>';
                }

                echo "<td>Updates</td>";

                echo "<td>This is the check for updates if we do it or not and if yes how much times do we check</td>";

                echo '<td><select class="form-control" name="UpdatesTimer">';

                if ($minutes == "*") {
                        echo '<option value="*" selected>Every hour</option>';
                } else {
                        echo '<option value="*">Every hour</option>';
                }

                while ($while != "24") {
                        if ($while == $minutes) {
                                echo '<option value="' . $while . '" selected>At ' . $while . ' hours</option>';
                        } else {
                                echo '<option value="' . $while . '">At ' . $while . ' hours</option>';
                        }
                        $while++;
                }


                echo '</select>';
                echo '<select class="form-control" name="UpdatesTimerDay">';

                if ("$day" == '*') {
                        echo '<option value="*" selected>Every day</option>';
                        echo '<option value="MON">Monday</option>';
                        echo '<option value="TUE">Tuesday</option>';
                        echo '<option value="WED">Wednesday</option>';
                        echo '<option value="THU">Thursday</option>';
                        echo '<option value="FRI">Friday</option>';
                        echo '<option value="SAT">Saturday</option>';
                        echo '<option value="SUN">Sunday</option>';
                } elseif ("$day" == 'MON') {
                        echo '<option value="*">Every day</option>';
                        echo '<option value="MON" selected>Monday</option>';
                        echo '<option value="TUE">Tuesday</option>';
                        echo '<option value="WED">Wednesday</option>';
                        echo '<option value="THU">Thursday</option>';
                        echo '<option value="FRI">Friday</option>';
                        echo '<option value="SAT">Saturday</option>';
                        echo '<option value="SUN">Sunday</option>';
                } elseif ("$day" == 'TUE') {
                        echo '<option value="*">Every day</option>';
                        echo '<option value="MON">Monday</option>';
                        echo '<option value="TUE" selected>Tuesday</option>';
                        echo '<option value="WED">Wednesday</option>';
                        echo '<option value="THU">Thursday</option>';
                        echo '<option value="FRI">Friday</option>';
                        echo '<option value="SAT">Saturday</option>';
                        echo '<option value="SUN">Sunday</option>';
                } elseif ("$day" == "WED") {
                        echo '<option value="*">Every day</option>';
                        echo '<option value="MON">Monday</option>';
                        echo '<option value="TUE">Tuesday</option>';
                        echo '<option value="WED" selected>Wednesday</option>';
                        echo '<option value="THU">Thursday</option>';
                        echo '<option value="FRI">Friday</option>';
                        echo '<option value="SAT">Saturday</option>';
                        echo '<option value="SUN">Sunday</option>';
                } elseif ("$day" == "THU") {
                        echo '<option value="*">Every day</option>';
                        echo '<option value="MON">Monday</option>';
                        echo '<option value="TUE">Tuesday</option>';
                        echo '<option value="WED">Wednesday</option>';
                        echo '<option value="THU" selected>Thursday</option>';
                        echo '<option value="FRI">Friday</option>';
                        echo '<option value="SAT">Saturday</option>';
                        echo '<option value="SUN">Sunday</option>';
                } elseif ("$day" == "FRI") {
                        echo '<option value="*">Every day</option>';
                        echo '<option value="MON">Monday</option>';
                        echo '<option value="TUE">Tuesday</option>';
                        echo '<option value="WED">Wednesday</option>';
                        echo '<option value="THU">Thursday</option>';
                        echo '<option value="FRI" selected>Friday</option>';
                        echo '<option value="SAT">Saturday</option>';
                        echo '<option value="SUN">Sunday</option>';
                } elseif ("$day" == "SAT") {
                        echo '<option value="*">Every day</option>';
                        echo '<option value="MON">Monday</option>';
                        echo '<option value="TUE">Tuesday</option>';
                        echo '<option value="WED">Wednesday</option>';
                        echo '<option value="THU">Thursday</option>';
                        echo '<option value="FRI">Friday</option>';
                        echo '<option value="SAT" selected>Saturday</option>';
                        echo '<option value="SUN">Sunday</option>';
                } elseif ("$day" == "SUN") {
                        echo '<option value="*">Every day</option>';
                        echo '<option value="MON">Monday</option>';
                        echo '<option value="TUE">Tuesday</option>';
                        echo '<option value="WED">Wednesday</option>';
                        echo '<option value="THU">Thursday</option>';
                        echo '<option value="FRI">Friday</option>';
                        echo '<option value="SAT">Saturday</option>';
                        echo '<option value="SUN" selected>Sunday</option>';
                } else {
                        echo '<option value="*">Every day</option>';
                        echo '<option value="MON">Monday</option>';
                        echo '<option value="TUE">Tuesday</option>';
                        echo '<option value="WED">Wednesday</option>';
                        echo '<option value="THU">Thursday</option>';
                        echo '<option value="FRI">Friday</option>';
                        echo '<option value="SAT">Saturday</option>';
                        echo '<option value="SUN">Sunday</option>';
                }

                echo '</select>';

                $while = "1";

                $minutes = "default";

                $day = "default";

                echo '</select></td>';



                echo "</tr>";

                //End Updates

                //Restart

                echo "<tr>";

                $while = "0";

                $exsists = `crontab -l | grep -w "factorio-task.sh --restart" | grep -q "${server}" && echo 'Yes' || echo 'No'`;
                $exsists = substr($exsists, 0, -1);

                if ($exsists == "Yes") {
                        echo '<td><input type="checkbox" name="RestartActive" value="box" checked></td>';
                        $minutes = `crontab -l | grep -w "factorio-task.sh --restart" | grep "${server}" | awk '{ print $2 }' | head -n 1`;
                        $day = `crontab -l | grep -w "factorio-task.sh --restart" | grep "${server}" | awk '{ print $5 }' | head -n 1 | tr -d '\n'`;
                } else {
                        echo '<td><input type="checkbox" name="RestartActive" value="box"></td>';
                }

                echo "<td>Restart</td>";

                echo "<td>When do we restart</td>";

                echo '<td><select class="form-control" name="RestartTimer">';

                while ($while != "24") {
                        if ($while == $minutes) {
                                echo '<option value="' . $while . '" selected>At ' . $while . ' hours</option>';
                        } else {
                                echo '<option value="' . $while . '">At ' . $while . ' hours</option>';
                        }
                        $while++;
                }


                echo '</select>';

                echo '<select class="form-control" name="RestartTimerDay">';

                if ($day == "*") {
                        echo '<option value="*" selected>Every day</option>';
                        echo '<option value="MON">Monday</option>';
                        echo '<option value="TUE">Tuesday</option>';
                        echo '<option value="WED">Wednesday</option>';
                        echo '<option value="THU">Thursday</option>';
                        echo '<option value="FRI">Friday</option>';
                        echo '<option value="SAT">Saturday</option>';
                        echo '<option value="SUN">Sunday</option>';
                } elseif ($day == "MON") {
                        echo '<option value="*">Every day</option>';
                        echo '<option value="MON" selected>Monday</option>';
                        echo '<option value="TUE">Tuesday</option>';
                        echo '<option value="WED">Wednesday</option>';
                        echo '<option value="THU">Thursday</option>';
                        echo '<option value="FRI">Friday</option>';
                        echo '<option value="SAT">Saturday</option>';
                        echo '<option value="SUN">Sunday</option>';
                } elseif ($day == "TUE") {
                        echo '<option value="*">Every day</option>';
                        echo '<option value="MON">Monday</option>';
                        echo '<option value="TUE" selected>Tuesday</option>';
                        echo '<option value="WED">Wednesday</option>';
                        echo '<option value="THU">Thursday</option>';
                        echo '<option value="FRI">Friday</option>';
                        echo '<option value="SAT">Saturday</option>';
                        echo '<option value="SUN">Sunday</option>';
                } elseif ($day == "WED") {
                        echo '<option value="*">Every day</option>';
                        echo '<option value="MON">Monday</option>';
                        echo '<option value="TUE">Tuesday</option>';
                        echo '<option value="WED" selected>Wednesday</option>';
                        echo '<option value="THU">Thursday</option>';
                        echo '<option value="FRI">Friday</option>';
                        echo '<option value="SAT">Saturday</option>';
                        echo '<option value="SUN">Sunday</option>';
                } elseif ($day == "THU") {
                        echo '<option value="*">Every day</option>';
                        echo '<option value="MON">Monday</option>';
                        echo '<option value="TUE">Tuesday</option>';
                        echo '<option value="WED">Wednesday</option>';
                        echo '<option value="THU" selected>Thursday</option>';
                        echo '<option value="FRI">Friday</option>';
                        echo '<option value="SAT">Saturday</option>';
                        echo '<option value="SUN">Sunday</option>';
                } elseif ($day == "FRI") {
                        echo '<option value="*">Every day</option>';
                        echo '<option value="MON">Monday</option>';
                        echo '<option value="TUE">Tuesday</option>';
                        echo '<option value="WED">Wednesday</option>';
                        echo '<option value="THU">Thursday</option>';
                        echo '<option value="FRI" selected>Friday</option>';
                        echo '<option value="SAT">Saturday</option>';
                        echo '<option value="SUN">Sunday</option>';
                } elseif ($day == "SAT") {
                        echo '<option value="*">Every day</option>';
                        echo '<option value="MON">Monday</option>';
                        echo '<option value="TUE">Tuesday</option>';
                        echo '<option value="WED">Wednesday</option>';
                        echo '<option value="THU">Thursday</option>';
                        echo '<option value="FRI">Friday</option>';
                        echo '<option value="SAT" selected>Saturday</option>';
                        echo '<option value="SUN">Sunday</option>';
                } elseif ($day == "SUN") {
                        echo '<option value="*">Every day</option>';
                        echo '<option value="MON">Monday</option>';
                        echo '<option value="TUE">Tuesday</option>';
                        echo '<option value="WED">Wednesday</option>';
                        echo '<option value="THU">Thursday</option>';
                        echo '<option value="FRI">Friday</option>';
                        echo '<option value="SAT">Saturday</option>';
                        echo '<option value="SUN" selected>Sunday</option>';
                } else {
                        echo '<option value="*">Every day</option>';
                        echo '<option value="MON">Monday</option>';
                        echo '<option value="TUE">Tuesday</option>';
                        echo '<option value="WED">Wednesday</option>';
                        echo '<option value="THU">Thursday</option>';
                        echo '<option value="FRI">Friday</option>';
                        echo '<option value="SAT">Saturday</option>';
                        echo '<option value="SUN">Sunday</option>';
                }

                echo '</select>';

                $while = "1";

                $minutes = "default";

                $day = "default";

                echo '</select></td>';



                //Restart Stop

                //Scenario Update

                echo "<tr>";

                $while = "0";

                $exsists = `crontab -l | grep -w "factorio-task.sh --update-scenario" | grep -q "${server}" && echo 'Yes' || echo 'No'`;
                $exsists = substr($exsists, 0, -1);

                if ($exsists == "Yes") {
                        echo '<td><input type="checkbox" name="ScenarioUpdateActive" value="box" checked></td>';
                        $minutes = `crontab -l | grep -w "factorio-task.sh --update-scenario" | grep "${server}" | awk '{ print $1 }' | cut -c 3-`;
                        $day = `crontab -l | grep -w "factorio-task.sh --update-scenario" | grep "${server}" | awk '{ print $2 }'`;
                } else {
                        echo '<td><input type="checkbox" name="ScenarioUpdateActive" value="box"></td>';
                }

                echo "<td>Scenario Update</td>";

                echo "<td>When is the scenario updated</td>";

                echo '<td><select class="form-control" name="ScenarioUpdateTimer">';

                if ($minutes == "*") {
                        echo '<option value="*" selected>Every hour</option>';
                } else {
                        echo '<option value="*">Every hour</option>';
                }

                while ($while != "24") {
                        if ($while == $minutes) {
                                echo '<option value="' . $while . '" selected>At ' . $while . ' hours</option>';
                        } else {
                                echo '<option value="' . $while . '">At ' . $while . ' hours</option>';
                        }
                        $while++;
                }

                echo '</select>';

                echo '<select class="form-control" name="ScenarioUpdateTimerDay">';

                if ($day == "*") {
                        echo '<option value="*" selected>Every day</option>';
                        echo '<option value="MON">Monday</option>';
                        echo '<option value="TUE">Tuesday</option>';
                        echo '<option value="WED">Wednesday</option>';
                        echo '<option value="THU">Thursday</option>';
                        echo '<option value="FRI">Friday</option>';
                        echo '<option value="SAT">Saturday</option>';
                        echo '<option value="SUN">Sunday</option>';
                } elseif ($day == "MON") {
                        echo '<option value="*">Every day</option>';
                        echo '<option value="MON" selected>Monday</option>';
                        echo '<option value="TUE">Tuesday</option>';
                        echo '<option value="WED">Wednesday</option>';
                        echo '<option value="THU">Thursday</option>';
                        echo '<option value="FRI">Friday</option>';
                        echo '<option value="SAT">Saturday</option>';
                        echo '<option value="SUN">Sunday</option>';
                } elseif ($day == "TUE") {
                        echo '<option value="*">Every day</option>';
                        echo '<option value="MON">Monday</option>';
                        echo '<option value="TUE" selected>Tuesday</option>';
                        echo '<option value="WED">Wednesday</option>';
                        echo '<option value="THU">Thursday</option>';
                        echo '<option value="FRI">Friday</option>';
                        echo '<option value="SAT">Saturday</option>';
                        echo '<option value="SUN">Sunday</option>';
                } elseif ($day == "WED") {
                        echo '<option value="*">Every day</option>';
                        echo '<option value="MON">Monday</option>';
                        echo '<option value="TUE">Tuesday</option>';
                        echo '<option value="WED" selected>Wednesday</option>';
                        echo '<option value="THU">Thursday</option>';
                        echo '<option value="FRI">Friday</option>';
                        echo '<option value="SAT">Saturday</option>';
                        echo '<option value="SUN">Sunday</option>';
                } elseif ($day == "THU") {
                        echo '<option value="*">Every day</option>';
                        echo '<option value="MON">Monday</option>';
                        echo '<option value="TUE">Tuesday</option>';
                        echo '<option value="WED">Wednesday</option>';
                        echo '<option value="THU" selected>Thursday</option>';
                        echo '<option value="FRI">Friday</option>';
                        echo '<option value="SAT">Saturday</option>';
                        echo '<option value="SUN">Sunday</option>';
                } elseif ($day == "FRI") {
                        echo '<option value="*">Every day</option>';
                        echo '<option value="MON">Monday</option>';
                        echo '<option value="TUE">Tuesday</option>';
                        echo '<option value="WED">Wednesday</option>';
                        echo '<option value="THU">Thursday</option>';
                        echo '<option value="FRI" selected>Friday</option>';
                        echo '<option value="SAT">Saturday</option>';
                        echo '<option value="SUN">Sunday</option>';
                } elseif ($day == "SAT") {
                        echo '<option value="*">Every day</option>';
                        echo '<option value="MON">Monday</option>';
                        echo '<option value="TUE">Tuesday</option>';
                        echo '<option value="WED">Wednesday</option>';
                        echo '<option value="THU">Thursday</option>';
                        echo '<option value="FRI">Friday</option>';
                        echo '<option value="SAT" selected>Saturday</option>';
                        echo '<option value="SUN">Sunday</option>';
                } elseif ($day == "SUN") {
                        echo '<option value="*">Every day</option>';
                        echo '<option value="MON">Monday</option>';
                        echo '<option value="TUE">Tuesday</option>';
                        echo '<option value="WED">Wednesday</option>';
                        echo '<option value="THU">Thursday</option>';
                        echo '<option value="FRI">Friday</option>';
                        echo '<option value="SAT">Saturday</option>';
                        echo '<option value="SUN" selected>Sunday</option>';
                } else {
                        echo '<option value="*">Every day</option>';
                        echo '<option value="MON">Monday</option>';
                        echo '<option value="TUE">Tuesday</option>';
                        echo '<option value="WED">Wednesday</option>';
                        echo '<option value="THU">Thursday</option>';
                        echo '<option value="FRI">Friday</option>';
                        echo '<option value="SAT">Saturday</option>';
                        echo '<option value="SUN">Sunday</option>';
                }

                echo '</select>';

                $while = "1";

                $minutes = "default";

                echo '</select></td>';



                //Scneario Update Stop

                //Time message

                echo "<tr>";


                $exsists = `crontab -l | grep -w "factorio-task.sh --time" | grep -q "${server}" && echo 'Yes' || echo 'No'`;
                $exsists = substr($exsists, 0, -1);

                if ($exsists == "Yes") {
                        echo '<td><input type="checkbox" name="TimeActive" value="box" checked></td>';
                        $minutes = `crontab -l | grep -w "factorio-task.sh --time" | grep "${server}" | awk '{ print $1 }' | cut -c 3-`;
                } else {
                        echo '<td><input type="checkbox" name="TimeActive" value="box"></td>';
                }

                echo "<td>Time</td>";

                echo "<td>Send to the players the current time</td>";

                echo '<td><select class="form-control" name="TimeTimer">';

                while ($while != "60") {
                        if ($while == $minutes) {
                                echo '<option value="' . $while . '" selected>' . $while . ' minutes</option>';
                        } else {
                                echo '<option value="' . $while . '">' . $while . ' minutes</option>';
                        }
                        $while++;
                }

                if ("60" == $minutes) {
                        echo '<option value="60" selected>1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                } elseif ("120" == $minutes) {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120" selected>2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                } elseif ("180" == $minutes) {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180" selected>3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                } elseif ("240" == $minutes) {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240" selected>4 hours</option>';
                } else {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                }

                $while = "1";

                $minutes = "default";

                echo '</select></td>';



                echo "</tr>";



                //Time message end

                //Rocket Reset
                echo "<tr>";


                $exsists = `crontab -l | grep -w "factorio-task.sh --rocket-reset" | grep -q "${server}" && echo 'Yes' || echo 'No'`;
                $exsists = substr($exsists, 0, -1);

                if ($exsists == "Yes") {
                        echo '<td><input type="checkbox" name="RocketResetActive" value="box" checked></td>';
                        $minutes = `crontab -l | grep -w "factorio-task.sh --rocket-reset" | grep "${server}" | awk '{ print $1 }' | cut -c 3-`;
                } else {
                        echo '<td><input type="checkbox" name="RocketResetActive" value="box"></td>';
                }

                echo "<td>Rocket Reset</td>";

                echo "<td>This is how much per minutes/hours we check the rocket counter and reset in case it's touched</td>";

                echo '<td><select class="form-control" name="RocketResetTimer">';

                while ($while != "60") {
                        if ($while == $minutes) {
                                echo '<option value="' . $while . '" selected>' . $while . ' minutes</option>';
                        } else {
                                echo '<option value="' . $while . '">' . $while . ' minutes</option>';
                        }
                        $while++;
                }

                if ("60" == $minutes) {
                        echo '<option value="60" selected>1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                } elseif ("120" == $minutes) {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120" selected>2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                } elseif ("180" == $minutes) {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180" selected>3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                } elseif ("240" == $minutes) {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240" selected>4 hours</option>';
                } else {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                }

                $while = "1";

                $minutes = "default";

                echo '</select></td>';



                echo "</tr>";

                //End Rocket Reset


                //Reset

                echo "<tr>";

                $while = "0";

                $exsists = `crontab -l | grep -w "factorio-task.sh --reset" | grep -q "${server}" && echo 'Yes' || echo 'No'`;
                $exsists = substr($exsists, 0, -1);

                if ($exsists == "Yes") {
                        echo '<td><input type="checkbox" name="ResetActive" value="box" checked></td>';
                        $minutes = `crontab -l | grep -w "factorio-task.sh --reset" | grep "${server}" | awk '{ print $2 }' | head -n 1`;
                        $day = `crontab -l | grep -w "factorio-task.sh --reset" | grep "${server}" | awk '{ print $5 }' | head -n 1 | tr -d '\n'`;
                } else {
                        echo '<td><input type="checkbox" name="ResetActive" value="box"></td>';
                }

                echo "<td>Reset</td>";

                echo "<td>When do we reset the server</td>";

                echo '<td><select class="form-control" name="ResetTimer">';

                while ($while != "24") {
                        if ($while == $minutes) {
                                echo '<option value="' . $while . '" selected>At ' . $while . ' hours</option>';
                        } else {
                                echo '<option value="' . $while . '">At ' . $while . ' hours</option>';
                        }
                        $while++;
                }


                echo '</select>';

                echo '<select class="form-control" name="ResetTimerDay">';

                if ($day == "*") {
                        echo '<option value="*" selected>Every day</option>';
                        echo '<option value="MON">Monday</option>';
                        echo '<option value="TUE">Tuesday</option>';
                        echo '<option value="WED">Wednesday</option>';
                        echo '<option value="THU">Thursday</option>';
                        echo '<option value="FRI">Friday</option>';
                        echo '<option value="SAT">Saturday</option>';
                        echo '<option value="SUN">Sunday</option>';
                } elseif ($day == "MON") {
                        echo '<option value="*">Every day</option>';
                        echo '<option value="MON" selected>Monday</option>';
                        echo '<option value="TUE">Tuesday</option>';
                        echo '<option value="WED">Wednesday</option>';
                        echo '<option value="THU">Thursday</option>';
                        echo '<option value="FRI">Friday</option>';
                        echo '<option value="SAT">Saturday</option>';
                        echo '<option value="SUN">Sunday</option>';
                } elseif ($day == "TUE") {
                        echo '<option value="*">Every day</option>';
                        echo '<option value="MON">Monday</option>';
                        echo '<option value="TUE" selected>Tuesday</option>';
                        echo '<option value="WED">Wednesday</option>';
                        echo '<option value="THU">Thursday</option>';
                        echo '<option value="FRI">Friday</option>';
                        echo '<option value="SAT">Saturday</option>';
                        echo '<option value="SUN">Sunday</option>';
                } elseif ($day == "WED") {
                        echo '<option value="*">Every day</option>';
                        echo '<option value="MON">Monday</option>';
                        echo '<option value="TUE">Tuesday</option>';
                        echo '<option value="WED" selected>Wednesday</option>';
                        echo '<option value="THU">Thursday</option>';
                        echo '<option value="FRI">Friday</option>';
                        echo '<option value="SAT">Saturday</option>';
                        echo '<option value="SUN">Sunday</option>';
                } elseif ($day == "THU") {
                        echo '<option value="*">Every day</option>';
                        echo '<option value="MON">Monday</option>';
                        echo '<option value="TUE">Tuesday</option>';
                        echo '<option value="WED">Wednesday</option>';
                        echo '<option value="THU" selected>Thursday</option>';
                        echo '<option value="FRI">Friday</option>';
                        echo '<option value="SAT">Saturday</option>';
                        echo '<option value="SUN">Sunday</option>';
                } elseif ($day == "FRI") {
                        echo '<option value="*">Every day</option>';
                        echo '<option value="MON">Monday</option>';
                        echo '<option value="TUE">Tuesday</option>';
                        echo '<option value="WED">Wednesday</option>';
                        echo '<option value="THU">Thursday</option>';
                        echo '<option value="FRI" selected>Friday</option>';
                        echo '<option value="SAT">Saturday</option>';
                        echo '<option value="SUN">Sunday</option>';
                } elseif ($day == "SAT") {
                        echo '<option value="*">Every day</option>';
                        echo '<option value="MON">Monday</option>';
                        echo '<option value="TUE">Tuesday</option>';
                        echo '<option value="WED">Wednesday</option>';
                        echo '<option value="THU">Thursday</option>';
                        echo '<option value="FRI">Friday</option>';
                        echo '<option value="SAT" selected>Saturday</option>';
                        echo '<option value="SUN">Sunday</option>';
                } elseif ($day == "SUN") {
                        echo '<option value="*">Every day</option>';
                        echo '<option value="MON">Monday</option>';
                        echo '<option value="TUE">Tuesday</option>';
                        echo '<option value="WED">Wednesday</option>';
                        echo '<option value="THU">Thursday</option>';
                        echo '<option value="FRI">Friday</option>';
                        echo '<option value="SAT">Saturday</option>';
                        echo '<option value="SUN" selected>Sunday</option>';
                } else {
                        echo '<option value="*">Every day</option>';
                        echo '<option value="MON">Monday</option>';
                        echo '<option value="TUE">Tuesday</option>';
                        echo '<option value="WED">Wednesday</option>';
                        echo '<option value="THU">Thursday</option>';
                        echo '<option value="FRI">Friday</option>';
                        echo '<option value="SAT">Saturday</option>';
                        echo '<option value="SUN">Sunday</option>';
                }

                echo '</select>';

                $while = "1";

                $minutes = "default";

                $day = "default";

                echo '</select></td>';



                //Reset Stop

                //Rocket info message (with goal)

                echo "<tr>";


                $exsists = `crontab -l | grep -w "factorio-task.sh --rocket-reset-inform" | grep -q "${server}" && echo 'Yes' || echo 'No'`;
                $exsists = substr($exsists, 0, -1);

                if ($exsists == "Yes") {
                        echo '<td><input type="checkbox" name="RocketResetInformActive" value="box" checked></td>';
                        $minutes = `crontab -l | grep -w "factorio-task.sh --rocket-reset-inform" | grep "${server}" | awk '{ print $1 }' | cut -c 3-`;
                } else {
                        echo '<td><input type="checkbox" name="RocketResetInformActive" value="box"></td>';
                }

                echo "<td>Rocket Goal Message</td>";

                echo "<td>Send to the players a message about how much rockets were sended and the goal (is prefered with rocket reset enabled)</td>";

                echo '<td><select class="form-control" name="RocketResetInformTimer">';

                while ($while != "60") {
                        if ($while == $minutes) {
                                echo '<option value="' . $while . '" selected>' . $while . ' minutes</option>';
                        } else {
                                echo '<option value="' . $while . '">' . $while . ' minutes</option>';
                        }
                        $while++;
                }

                if ("60" == $minutes) {
                        echo '<option value="60" selected>1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                } elseif ("120" == $minutes) {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120" selected>2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                } elseif ("180" == $minutes) {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180" selected>3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                } elseif ("240" == $minutes) {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240" selected>4 hours</option>';
                } else {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                }

                $while = "1";

                $minutes = "default";

                echo '</select></td>';



                echo "</tr>";



                //End Rocket Info

                //Rocket info message (without goal)

                echo "<tr>";


                $exsists = `crontab -l | grep -w "factorio-task.sh --rocket-sended" | grep -q "${server}" && echo 'Yes' || echo 'No'`;
                $exsists = substr($exsists, 0, -1);

                if ($exsists == "Yes") {
                        echo '<td><input type="checkbox" name="RocketInformActive" value="box" checked></td>';
                        $minutes = `crontab -l | grep -w "factorio-task.sh --rocket-sended" | grep "${server}" | awk '{ print $1 }' | cut -c 3-`;
                } else {
                        echo '<td><input type="checkbox" name="RocketInformActive" value="box"></td>';
                }

                echo "<td>Rocket Message</td>";

                echo "<td>Send to the players a message about how much rockets were sended (recommanded when there is no goal)</td>";

                echo '<td><select class="form-control" name="RocketInformTimer">';

                while ($while != "60") {
                        if ($while == $minutes) {
                                echo '<option value="' . $while . '" selected>' . $while . ' minutes</option>';
                        } else {
                                echo '<option value="' . $while . '">' . $while . ' minutes</option>';
                        }
                        $while++;
                }

                if ("60" == $minutes) {
                        echo '<option value="60" selected>1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                } elseif ("120" == $minutes) {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120" selected>2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                } elseif ("180" == $minutes) {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180" selected>3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                } elseif ("240" == $minutes) {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240" selected>4 hours</option>';
                } else {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                }

                $while = "1";

                $minutes = "default";

                echo '</select></td>';



                echo "</tr>";



                //End Rocket Info


                //Info message ads

                echo "<tr>";


                $exsists = `crontab -l | grep -w "factorio-task.sh --inform" | grep -q "${server}" && echo 'Yes' || echo 'No'`;
                $exsists = substr($exsists, 0, -1);

                if ($exsists == "Yes") {
                        echo '<td><input type="checkbox" name="InformActive" value="box" checked></td>';
                        $minutes = `crontab -l | grep -w "factorio-task.sh --inform" | grep "${server}" | awk '{ print $1 }' | cut -c 3-`;
                } else {
                        echo '<td><input type="checkbox" name="InformActive" value="box"></td>';
                }

                echo "<td>Inform Message</td>";

                echo "<td>Send to the players a message with info's like discord, website,....</td>";

                echo '<td><select class="form-control" name="InformTimer">';

                while ($while != "60") {
                        if ($while == $minutes) {
                                echo '<option value="' . $while . '" selected>' . $while . ' minutes</option>';
                        } else {
                                echo '<option value="' . $while . '">' . $while . ' minutes</option>';
                        }
                        $while++;
                }

                if ("60" == $minutes) {
                        echo '<option value="60" selected>1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                } elseif ("120" == $minutes) {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120" selected>2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                } elseif ("180" == $minutes) {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180" selected>3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                } elseif ("240" == $minutes) {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240" selected>4 hours</option>';
                } else {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                }

                $while = "1";

                $minutes = "default";

                echo '</select></td>';



                echo "</tr>";



                //End Info Ads

                //Info message reset

                echo "<tr>";


                $exsists = `crontab -l | grep -w "factorio-task.sh --inform-reset" | grep "${server}" | awk '{ print $1 }'`;
                $exsists = substr($exsists, 0, -1);

                if ($exsists == "Yes") {
                        echo '<td><input type="checkbox" name="ResetInformActive" value="box" checked></td>';
                        $minutes = `crontab -l | grep -w "factorio-task.sh --inform-reset" | grep "${server}" | awk '{ print $1 }' | cut -c 3-`;
                } else {
                        echo '<td><input type="checkbox" name="ResetInformActive" value="box"></td>';
                }

                echo "<td>Reset Info Message</td>";

                echo "<td>Send to the players a message with the info when we do reset (if reset is turned off this can't be activated).</td>";

                echo '<td><select class="form-control" name="ResetInformTimer">';

                while ($while != "60") {
                        if ($while == $minutes) {
                                echo '<option value="' . $while . '" selected>' . $while . ' minutes</option>';
                        } else {
                                echo '<option value="' . $while . '">' . $while . ' minutes</option>';
                        }
                        $while++;
                }

                if ("60" == $minutes) {
                        echo '<option value="60" selected>1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                } elseif ("120" == $minutes) {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120" selected>2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                } elseif ("180" == $minutes) {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180" selected>3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                } elseif ("240" == $minutes) {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240" selected>4 hours</option>';
                } else {
                        echo '<option value="60">1 hour</option>';
                        echo '<option value="120">2 hours</option>';
                        echo '<option value="180">3 hours</option>';
                        echo '<option value="240">4 hours</option>';
                }

                $while = "1";

                $minutes = "default";

                echo '</select></td>';



                echo "</tr>";



                //End Info reset


                echo "</table>";

                echo '<button type="submit" class="btn btn-primary float-right" style="margin-bottom: 15px;">Apply this schedule</button>';


                echo "</form>";
        }
                ?>
</div>