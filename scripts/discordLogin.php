<?php
    session_start();
    echo isset($_SESSION['login']);
    if(isset($_SESSION['login'])) {
      header('LOCATION:../admin.php'); die();
    }

require __DIR__ . '/vendor/autoload.php';
use Xwilarg\Discord\OAuth2;


// Sample configuration file, contains the following strings:
// clientId: Client ID of the application
// secret: Secret of the application
// url: The redirect URL (URL called after the user is logged in, must be registered in https://discordapp.com/developers/applications/[YourAppId]/oauth)
$auth = json_decode(file_get_contents('../token.json'), true);

$webhookurl = $auth["webhook"];

$oauth2 = new OAuth2($auth["clientId"], $auth["secret"], $auth["url"]);
if ($oauth2->isRedirected() === false) { // Did the client already logged in ?
    // The parameter can be a combination of the following: connections, email, identity or guilds
    // More information about it here: https://discordapp.com/developers/docs/topics/oauth2#shared-resources-oauth2-scopes
    $oauth2->startRedirection(['identify', 'guilds']);
} else {
    // If preload the token to see if everything happen without error
    $ok = $oauth2->loadToken();
    if ($ok !== true) {
        // A common error can be to reload the page because the code returned by Discord would still be present in the URL
        // If this happen, isRedirected will return true and we will come here with an invalid code
        // So if there is a problem, we redirect the user to Discord authentification
        $oauth2->startRedirection(['identify', 'guilds']);
    } else {
        // ---------- USER INFORMATION
        $user = $oauth2->getUserInformation(); // Same as $oauth2->getCustomInformation('users/@me')
        // ---------- CONNECTIONS INFORMATION
        $guild = $oauth2->getGuildsInformation();
        if (array_key_exists("code", $answer)) {
            exit("An error occured: " . $answer["message"]);
        } else {
                        foreach ($guild as $a) {
                                if ($a["id"] == $auth["guildId"]) {
                                        if ($a["permissions"] & "8") {
                                                echo "Welcome " . $user["username"] . " you are behing logged in as an administrator";
						$_SESSION["user"]=$user["username"];
						$_SESSION["role"]="Admin";

                                                $_SESSION['login'] = true;
                                                header('LOCATION:launchAdminLoading.php');
                                                die();
                                        } elseif ($a["permissions"] & "8198") {
                                                echo "Welcome " . $user["username"] . "you are behing logged in as an moderator";
                                                $_SESSION["user"]=$user["username"];
                                                $_SESSION["role"]="Moderator";

				                $_SESSION['login'] = true;
				                header('LOCATION:launchAdminLoading.php');
				                die();


                                        } else {
                                                $exit = "Sorry " . $user["username"] . " you aren't a moderator or an administartor, this has been logged";
                                                $msg = $user["username"] . "#" . $user["discriminator"] . " from discord has tried to login to the webpanel using discord";


                                                $json_data = array ('content'=>"$msg");

                                                $make_json = json_encode($json_data);


                                                $ch = curl_init( $webhookurl );

                                                curl_setopt( $ch, CURLOPT_POST, 1);

                                                curl_setopt( $ch, CURLOPT_POSTFIELDS, $make_json);

                                                curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);

                                                curl_setopt( $ch, CURLOPT_HEADER, 0);

                                                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
                                                $response = curl_exec( $ch );

						echo "$exit <br>";

						echo '<a href="../login.php">Go back to login</a>';


                                                exit("");
                                        }
                                }
                        }
                echo "<br> It seems you aren't on the discord.......";
            }
        }
    }
