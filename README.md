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