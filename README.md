Factorio-ServerManager is an Factorio server manager using PHP and bash it only works on linux. It's activly used by joinandplaycoop, but we love to see other communities growing as well profit of it.



Look at config.txt, token.txt, user.csv


WE DO NOT PROVIDE ANY SUPPORT USE IT FOR TESTING PURPOSES !

The last version doesn't include the version manager, so feel free to pull request this repo

Needed :

apt install netcat jq php php-curl php-mysql php-apcu bc composer grep -y
(PHP it depends on your version you can run like PHP7.2-curl)

(in the script folder run)

`composer require xwilarg/discord-oauth2-php`

`sudo chown -R www-data:www-data /var/www`

Other part :
run visudo and put these :

`www-data ALL=(ALL:ALL) /usr/bin/screen * `

If it does nothing when you press on an action run `chmod -R +x *` in the scripts directory and to be sure run `sed -i -e 's/\r$//' factorio-task.sh` as well.
Install mcrcon here `https://github.com/joinandplaycoop/mcrcon`
If you have error not finding your mcrcon instlation make `cp /usr/local/bin/mcrcon /usr/bin/`

`www-data  ALL=(ALL:ALL) NOPASSWD: ALL`

In apache2.conf check that it listen to sub directories i did it like this :

```
<Directory /var/www/>
        Options Indexes FollowSymLinks
        AllowOverride None
        Require all granted
</Directory>

```
Then for the config to hide servers publicly (so they can't get your rcon pass,...)

```
<Directory /var/www/panel/server>
     order deny,allow
     Deny From All
     <FilesMatch ".zip">
          Allow from all
       </FilesMatch>
</Directory>

```


Important if you don't have file upload in php turned on turn it on in (depends on your version) :

make `nano /etc/php/7.2/apache2/php.ini`

FOR CLOUDFLARE USERS !!!!! 

If you are on free plan like i am you can't upload more than 100mb if you wish to upload more simply make your subdomain not pass by cloudflare

If you don't know your php version here i putted 7.2 but to find it you can go on "yourpanelurl.com/scripts/phpinfo.php"

Exemple for the apache2 virtual host :
```
<VirtualHost *:80>
    ServerAdmin admin@poli.fun
    DocumentRoot /var/www/joinandplaycoopcom/panel
    SetEnvIf Request_URI "^/server-status$" dontlog
    SetEnvIf Request_URI "^$OPT_LB_STATS_URI$" dontlog

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined env=!dontlog

    ServerName panel.joinandplaycoop.com
    ServerAlias www.panel.joinandplaycoop.com
    DirectoryIndex admin.php
    <Directory "/var/www/joinandplaycoopcom/panel">
    AllowOverride All
    </Directory>
</VirtualHost>
```

PS : if you want to make the panel faster you can turn on opcache in your php.ini by opening it and finding the line opcach.enable=1 and you need to remove the `;` at the start
I putted my own `opcache.memory_consumption=512` to 512mb for more files cache the scripts works perfectly witout it. You can go in http://yourpanel.com/debug to find a opcache status





0.2.11
* Fix updater
* Use Factorio new API for update checks, with JQ

0.2.10
* Fix the map-gen-settings
* Fix the map-settings

0.2.9
* Added a new button for banning a user globally that didn't joined the server
* Added the map-gen-settings parameter

0.2.8
* Fix the script for player playtime to add a check to prevent player hours to be reseted
* Try to fix to not insert multiple times the matierial
* Moved the playermanager php file to the scripts folder
* Changed the whole player manager to work only with PHP now and not behing a hidden bash script
* Typo corrections
* More fixes for the player playtime

0.2.7
* Corrected indentations on all files with autoindentation
* Now when the page is loaded the console directly goes to the bottom
* Huge optimisation of the admin main page
* Now the left table is automatically refreshed every 5 seconds
* When you send a command it puts you to the bottom of the console and if it was chatting scroll down the console

0.2.6
* Fix to give the fifth parameter for automatic updates (updates fix)
* Fix permissions to be a bit more restrictive about cut and upgrades
* Fix the autoperms to use the correct sql name
* Fixed rocket count to not multiply the counter in case of reset
* Fixed to not create duplicates servers in the database.
* Added a check for the SQL connection in factorio-sql.sh
* Corrected a lot of identation in Factorio-SQL.sh
* Corrected a lot of identation in factorio-task.sh
* Fixed binary operator expected in Factorio-SQL.sh
* Improvement to have more checks for the duplicates creations

0.2.5
* Fix reset message in discord
* Fix reset message when you use automatic reset
* Fix update message in discord
* Fix message that when you delete all the saves it says rollback as title

0.2.4
* Fix bans to now use correctly send names when banning pepoles
* Fix Stats crontab to show correctly in the scheduler
* Fix console chat (for the servermanager)
* Fix to send correctly messages using webhooks in factorio-task.sh

0.2.3
* Fix scheduler manager to also show days now
* Fix Updates to also check every day at a choosen point
* Add that it's required to have stats enabled to have cross server permissions
* Fix the readme to add php-mysql
* Add better tables to ban, kicks, scheduler manager config applier

0.2.2
* Fixed tail to show 350 lines but only intressting ones (double tail correction)
* Mention that you need stats enabled for having autoperms across all servers
* Fix of versions

0.2.1
* Save manager returns to the saves once the job is done
* Added button to remove all saves of every type and all saves in the save manager
* Fixed scripts error in the save manager
* Try some fixes to show the main console in full height (bootstrap 4)
* Fix that the server are inserted multiple times in the database
* Better desing for the left table at the admin page
* Show player online in the player manager

0.2.0
* Mile Stone loads of fixes new major freatures

* Remove more useless thing when you download the log
* Fix all script to use case sensitive server names.
* Add a table to the scheduler config applier
* Added symlinks to all saves so players can download the saves easily and it will be automatically created when a server is created
* Added a save public browser

0.1.30
* Php file upload is now direclty in the .htaccess so no config is required anymore
* The console fully expends automatically again
* Fixed the insert of the production items in the database (still on beta)
* Fixes the multiple permissions group creation
* Try to prevent the server to be unrecoverable after 40 seconds force the exit of factorio when resting, restarting, stoping
* Fix the stats cron to show if turned on or not

0.1.29
* Fixed the server creation to go in full higercase
* Fixed the call of the server delete in the database and factorio-task
* Fixes for the serverConfiguration to show the rocket count correctly

0.1.28
* Fixed .htaccess
* Added some infos to readme

0.1.27
* Fixed some english typo errors in factorio-task.sh
* Corrected some indentations

0.1.26
* Added the file upload in the save manager
* Fixed the readme for the php.ini config for file uploading
* Small tweaks in factorio-task.sh (like chmod for file save on rollback)

0.1.25
* Added more config to the config file instead of setting it manually in scripts/factorio-task.sh
* Added script to gather the stats of the game
* Added to the crontab the script to gather the stats
* Fixes for the script to gather the stats

0.1.24
* Fixed reset page
* Fixed reset that it can't reset while it's already reseting
* Added the changelog
* Get only the version number in version div at admin page
* Added the title to the reset page
* Added link to the change log when you press on the version
* Fixed the false demote
* Fixed the issue when doing /admins with the factorio-task script
* Added a button to return to the panel when you change the scheduler config 
* Added a title to launchServer.php (tasks)

0.1.23
* Added partialy the crontab webinterface
* Add the size of the saves in the save manager
* Add the versions on the bottom left
