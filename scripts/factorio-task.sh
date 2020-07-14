#!/bin/bash
run=$1
path=$2
webhook=$3
fourthparm=$4
fifthparm=$5

#This is for cron since cron often doesn't work with the same paths

PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin
LANG=en_US.UTF-8
SHELL=/bin/bash

#Normally you need to check visudo for the server start,stop,restart check also the permissions on the startup file

DIRSCRIPT="$(cd "$(dirname "${BASH_SOURCE[0]}")" >/dev/null 2>&1 && pwd)"
DIRSCRIPT="${DIRSCRIPT}/"

#VARIABLES TO MODIFY IF NEEDED

DBServer=$(sed -n '2p' ${DIRSCRIPT}../config.txt | sed 's/"//g' | cut -c 9-)
DBPort=$(sed -n '3p' ${DIRSCRIPT}../config.txt | sed 's/"//g' | cut -c 11-)
DBUser=$(sed -n '4p' ${DIRSCRIPT}../config.txt | sed 's/"//g' | cut -c 11-)
DBPassword=$(sed -n '5p' ${DIRSCRIPT}../config.txt | sed 's/"//g' | cut -c 10-)
DB=$(sed -n '1p' ${DIRSCRIPT}../config.txt | sed 's/"//g' | cut -c 9-)

export MYSQL_PWD="$DBPassword"

#Mods update data
username=$(sed -n '12p' ${DIRSCRIPT}../config.txt | sed 's/"//g' | cut -c 18-)
token=$(sed -n '13p' ${DIRSCRIPT}../config.txt | sed 's/"//g' | cut -c 15-)

#NO TOUCH

#Functions i will use in the script

discord() {
	message="$1"
	echo "$message"

	## discord webhook
	curl -d "{\"content\": \"$message\"}" -H "Content-Type: application/json" "$webhook"

}
runSQL() {
	runSQLOutput=$(mysql -sN --user="$DBUser" -h $DBServer -P $DBPort --database="$DB")
}
runRcon() {
	testConnection=$({ timeout 2 mcrcon -c -H $HOST -P $PORT -p $PASSWORD "$1" | grep runRconError; } 2>&1)
	exit_status=$?
	if [[ $exit_status -eq 124 ]]; then
		echo "Timed out"
		runTestConnection=0
		while [[ runTestConnection -ne 6 ]]; do
			echo "It looks like rcon answerd but slowly maybe is it saving retrying ${runTestConnection}/6"
			testConnection=$({ timeout 5 mcrcon -c -H $HOST -P $PORT -p $PASSWORD "$1" | grep runRconError; } 2>&1)

			exit_status=$?
			if ! [[ $exit_status -eq 124 ]]; then
				runTestConnection=5
			fi

			let "runTestConnection++"
		done
	fi
	if grep -q "Connection failed." <<<"$testConnection"; then
		echo "There was an error using rcon trying again....."
		echo "> $testConnection <"
	fi

	runRconOutput=$(timeout 2 mcrcon -c -H $HOST -P $PORT -p $PASSWORD "$1")
	if [[ $runRconOutput == "" ]]; then
		echo "We arent sure about the data we got via rcon we will recheck it to be sure"
		runRconOutputCheck=0
		while [[ runRconOutputCheck -ne 6 ]]; do
			echo "Retrying ${runRconOutputCheck}/6"
			runRconOutput=$(timeout 5 mcrcon -c -H $HOST -P $PORT -p $PASSWORD "$1")

			if [[ $runRconOutput != "" ]]; then
				runRconOutputCheck=5
			fi

			let "runRconOutputCheck++"
		done
		if [[ $runRconOutput == "" ]]; then
			echo "Sorry one more test has to be done to check the data this operation can take 5 seconds more"

			sleep 5s
			runRconOutput=$(timeout 5 mcrcon -c -H $HOST -P $PORT -p $PASSWORD "$1")
			echo "The data seems okay"
		fi

	fi
}

#This is a very special function for messages, less checks because sometimes it can send it twice what we exactly dont want

runRconMessage() {
	testConnection=$({ timeout 2 mcrcon -c -H $HOST -P $PORT -p $PASSWORD "/players" | grep runRconError; } 2>&1)
	exit_status=$?
	if [[ $exit_status -eq 124 ]]; then
		echo "Timed out"
	fi
	if grep -q "Connection failed." <<<"$testConnection"; then
		echo "There was an error using rcon trying again....."
		echo "> $testConnection <"
	fi

	runRconOutput=$(timeout 5 mcrcon -c -H $HOST -P $PORT -p $PASSWORD "$1")

}

#Start of function based on this script

help() {
	echo "You can run these following commands in this script"
	echo "--autoperms (runs the permission script)"
	echo "--save (runs the save of the server and put it in a folder)"
	echo "--update (runs a server update if their is one)"
	echo "--install (install the factorio server) DISABLED FOR NOW"
	echo "--install-via-webpanel (this is a very specific function for the webpanel)"
	echo "--remove-via-webpanel (this is a very specific function for the webpanel)"
	echo "--reset (resets the server map) (you need to put as fourht parameter the server name)"
	echo "--rocket-reset (resets the server map if the goal of rockets is reached) (you need to input the server name as fourth parameter) (you need to input the max rockets as fifth parameter)"
	echo "--rocket-reset-inform (inform the players of the number of rockets launched and the goal) (you need to input the server name as third parameter) (you need to input the max rockets as fourth parameter)"
	echo "--update-scenario (updates the scenario of the server) (put the url as fourth parameter of the github and as fifth the path of the scenario if needed)"
	echo "--update-mods (updates the mods of the server if there is some)"
	echo "--start (starts the server) (the second parameter is the user (servername like s1))"
	echo "--stop (stops the server)"
	echo "--restart (restart the server)"
	echo "--run-command (send a command to be ran on the server)"
	echo "--rocket-discord (sends the count of the rockets to discord)"
	echo "--inform (sends the players the discord information and website)"
	echo "--inform-reset (sends the player that we reset on a schedule (third parameter is the day, month of reset)"
	echo "--rocket-sended (sends the player a message on how much rockets we sended)"
	echo "--time (sends the players the time information)"
	echo "--rollback (rollback to a choosen save)"
	echo ""
	echo "The second space permit you to put the server path"
	echo "Like ../server/s1"
	echo ""
	echo "The third parameter permits you to put your webhook link (sometimes the user)"
	echo ""
	echo "Example :"
	echo "./run.sh --start ../server/s1"
	echo './run.sh --update-scenario ../server/s1 https://github.com/poli/secnario.git factorio/senariooarc'
	echo ""
	echo "For the database open the script"
}
#Factorio side

#AUTOPERMS START
autoperms() {
	declare PlayerTimeHours
	echo "$path"
	runRcon "/players"
	mapfile -t PlayerList < <(printf "%s\n" "$runRconOutput" | sed 's/ (online)//g' | sed '1d' | tr -d ' ')
	for Player in "${PlayerList[@]}"; do
		runnedTrust="0"
		Player=$(echo $Player)
		runRcon '/silent-command rcon.print(game.players["'"${Player}"'"].online_time / 60)'
		PlayerTimeString=$(echo "$runRconOutput" | awk '{print int($1+0.5)}')
		let "PlayerTimeHours=$PlayerTimeString / 60 / 60"
		echo "$Player : $PlayerTimeHours"

		#Things to check admin in database

		runSQL <<EOF
	                        SELECT IsAdmin
        	                FROM Players
                	        WHERE PlayerName = "$Player"
EOF
		if [[ $runSQLOutput == "1" ]]; then
			runRcon "/permissions add-player Admin $Player"
			runRcon "/promote $Player"
			runnedTrust="1"
			echo "$Player was promoted admin and was added to the permission group Admin"
		elif [[ $(bc -l <<<"$PlayerTimeHours >= 3") = 1 ]]; then
			echo "Trusted $Player"
			runRcon "/permissions add-player Trusted $Player"
			runnedTrust="1"
		fi

		if [[ $runnedTrust == "1" ]]; then
			:
		else
			echo "Checking via database"
			runSQL <<EOF
                                SELECT Id
                                FROM Players
                                WHERE PlayerName = "$Player"
EOF
			IdPlayer=$runSQLOutput
			if [ -z $runSQLOutput ]; then
				echo "Player is not in the database for the moment"
			else
				runSQL <<EOF
                                        SELECT Ticks
                                        FROM PlayerPlaytime
                                        WHERE FKPlayerId = "$runSQLOutput"
                                        ORDER BY Id
                                        DESC LIMIT 1
EOF
				let "PlayerTimeHours=$runSQLOutput / 3600"
				echo "Player hours via DB : $Player, $PlayerTimeHours"
				if [[ $(bc -l <<<"$PlayerTimeHours >= 3") = 1 ]]; then
					echo "Trusted $Player"
					runRcon "/permissions add-player Trusted $Player"
					runnedTrust="1"
				fi
			fi
		fi
	done
	runRconMessage "/admins"
	mapfile -t AdminList < <(printf "%s\n" "$runRconOutput" | sed 's/ (online)//g' | sed '1d' | tr -d ' ' | tr -d '')
	for Admin in "${AdminList[@]}"; do
		runSQL <<EOF
	                        SELECT IsAdmin
        	                FROM Players
                	        WHERE PlayerName = "$Admin"
EOF
		if [[ -z $runSQLOutput || $runSQLOutput == "0" || $Admin == " " ]]; then
			runRconMessage "/demote $Admin"
			echo "$Admin was not written as admin in the database he has been demoted"
			runRconMessage "$Admin was demoted because he was not written as admin in the database if a moderator promoted you contact the server admin (nothing bad just to make you admin if needed)"
		fi

	done

	#Ban via database

	runSQL <<EOF
			SELECT P.PlayerName
			FROM Ban
			INNER JOIN Players as P ON Ban.FKPlayerId = P.Id
EOF
	#Convert the ban file of factorio to lower case to be sure

	mapfile -t ServerBannedPlayer < <(cat $path/server-banlist.json | sed '1d' | head -n -1 | sed "s/^[ \t]*//" | sed 's/,$//' | tr -d \" | grep -ve "reason:" | grep -v "address: " | grep -v "{" | grep -v "}" | sed 's/username: //g' | sed 's/\[//' | sed 's/\]//' | sed '/^$/d' | tr '[:upper:]' '[:lower:]')

	mapfile -t DBBannedPlayer < <(printf "%s\n" "$runSQLOutput")
	for DBBannedPlayer in "${DBBannedPlayer[@]}"; do
		#Converts to lower case to be sure since it's stored in lower case in the factorio ban list
		DBBannedPlayer=$(echo "$DBBannedPlayer" | tr '[:upper:]' '[:lower:]')
		if ! [[ -z "$DBBannedPlayer" ]]; then
			array_contains() {
				local array="$1[@]"
				local seeking=$2
				local in=1
				for element in "${!array}"; do
					if [[ $element == $seeking ]]; then
						in=0
						break
					fi
				done
				return $in
			}

			arrayCheck=$(array_contains ServerBannedPlayer "$DBBannedPlayer" && echo yes || echo no)

			echo "$DBBannedPlayer was already banned : $arrayCheck"

			if [[ $arrayCheck == "no" ]]; then

				runSQL <<EOF
						SELECT Id
						FROM Players
						WHERE PlayerName = "$DBBannedPlayer"
EOF

				PlayerBannedId=$(echo $runSQLOutput)
				#ban
				runSQL <<EOF
				      	     SELECT Reason
	   	 	                     FROM Ban
	                        	     WHERE FKPlayerId = "$PlayerBannedId"
EOF

				runRconMessage "/ban $DBBannedPlayer $runSQLOutput"
				echo "Banned $DBBannedPlayer on the server with the reason : $runSQLOutput"
			fi
		fi

	done
	#Unban via DB

	mapfile -t ServerBannedPlayer < <(cat $path/server-banlist.json | sed '1d' | head -n -1 | sed "s/^[ \t]*//" | sed 's/,$//' | tr -d \" | grep -ve "reason:" | grep -v "address: " | grep -v "{" | grep -v "}" | sed 's/username: //g' | sed 's/\[//' | sed 's/\]//' | sed '/^$/d' | tr '[:upper:]' '[:lower:]')

	#Due to the amout of possible players we won't do a big request requesting all players like below
	echo "$ServerBannedPlayer"

	for ServerBannedPlayer in "${ServerBannedPlayer[@]}"; do
		runSQL <<EOF
				SELECT P.PlayerName
				FROM Ban
				INNER JOIN Players as P ON Ban.FKPlayerId = P.Id
				WHERE P.PlayerName = "$ServerBannedPlayer"
EOF
		if [ -z $runSQLOutput ]; then

			#For some reasons i needed to do the code twice because sometimes it did not sended anything.

			runSQL <<EOF
					SELECT P.PlayerName
					FROM Ban
					INNER JOIN Players as P ON Ban.FKPlayerId = P.Id
					WHERE P.PlayerName = "$ServerBannedPlayer"
EOF
			if [ -z $runSQLOutput ]; then
				#Unban player
				runRconMessage "/unban $ServerBannedPlayer"
				runRconMessage "$ServerBannedPlayer was unbanned from this server"
			fi
		fi
	done

}
#AUTOPERMS STOP

#SAVE START
save() {
	if [ -z $webhook ]; then
		echo "For help run this script with --help"
		echo "You need to input a server name (third parameter) (like s1)"

	else
		server=$(echo "$webhook" | tr '[:upper:]' '[:lower:]')

		date=$(date +%Y-%m-%d-%H:%M)
		runRcon "/server-save"
		runRconMessage "The server has been saved !"
		echo "Saved moving the files"
		mkdir -p $path/LastestSaves
		mkdir -p $path/OldSaves

		chmod 777 -R $path/LastestSaves
		chmod 777 -R $path/OldSaves
		cp $savelocation $path/LastestSaves/$date.zip

		#find changes its directory as part of its internal operation. When you run the command, youre sitting in a directory that the user doesnt have permission to go to

		cd /tmp

		#Move the files older than a day
		find $path/LastestSaves -type f -name '*.zip' -mtime +1 -exec mv {} $path/OldSaves \;
		echo "Finished"
	fi

}
#SAVE STOP

#ROCKET START
rocketsCount() {
	rockets=0
	TotalRocketCount=0
	runRcon "/silent-command for name, force in pairs (game.forces) do rcon.print(name) end"
	mapfile -t Forces < <(printf "%s\n" "$runRconOutput")
	for Force in "${Forces[@]}"; do
		runRcon '/silent-command rcon.print(game.forces["'"${Force}"'"].items_launched["satellite"])'
		rockets=$(echo $runRconOutput | sed 's/\x1b\[[0-9;]*m//g')
		if [[ "$rockets" -eq "nil" ]]; then
			rockets=0
		fi
		if ! [ "$rockets" -eq "$rockets" ] 2>/dev/null; then
			rockets=0
		fi
		TotalRocketCount=$(($TotalRocketCount + $rockets))
	done
}
#ROCKET STOP

#RESET START
startForReset() {
	CurrentDirectory=$PWD
	cd ServerManager
	Screen=$(screen -ls)
	if [[ $Screen == *ServerManager${server}* ]]; then
		echo "Game already Started"
	else
		echo "Game was started"
		screen -dmS ServerManager${server} ./ServerManager
	fi
	cd $CurrentDirectory

}
stopForReset() {
	echo "If you have errors here it's because the server is probably stoped"
	screen=$(screen -S ServerManager${server} -X stuff "/quit^M")
	screenls=$(screen -ls)
	while [[ $screenls == *ServerManager${server}* ]]; do
		screenls=$(screen -ls)
		screenquit=$(screen -S ServerManager${server} -X stuff "^M")
		let "seconds++"
		echo -ne "Checking if factorio is still running : $seconds"\\r
		sleep 1s
		if [[ $seconds -ge "60" ]]; then
			screen=$(screen -S ServerManager${server} -X stuff ^C)
		fi
	done
	echo ""
	echo "Stoped"
}

reset() {

	if [ -z "$webhook" ]; then
		echo "For help run this script with --help"
		echo "You need to input a webhook url (third parameter)"
	else
		if [ -z "$fourthparm" ]; then
			echo "For help run this script with --help"
			echo "You need to input a server name (like s1) (fourth parameter)"
		else
			server=$(echo "$fourthparm")
			date=$(date +%Y-%m-%d-%H:%M)

			runSQL <<EOF
		    	SELECT IsReseting
		        FROM Server
	        	WHERE BINARY ServerName = "${server}"
EOF

			if [[ $runSQLOutput == "0" ]]; then

				mkdir -p $path/ResetSaves
				chmod 777 -R $path/ResetSaves

				echo "Reset starting sending message to players and waiting 15 seconds"
				runRconMessage "This server will be reseted in the next 15 seconds"
				runRconMessage "You will be able to find the map at https://saves.joinandplaycoop.com/$server/ResetSaves/$date.zip"

				rocketsCount

				runRconMessage "Rockets sent accross all forces: $TotalRocketCount"

				discord "The Map on $server will be reseted currently the players sent $TotalRocketCount rockets accross all forces"

				runSQL <<EOF
						UPDATE Server
						SET Status = "Off"
						WHERE BINARY ServerName = "${server}"
EOF
				runRconMessage "Saving a last time .... "
				runRcon "/server-save"

				sleep 15s
				stopForReset
				sleep 1s

				mv $savelocation $path/ResetSaves/$date.zip
				echo "Moved the save"

				cd $path

				newsavereset=$(grep -oP "start-server-load-scenario\K.*" "ServerManager/.createenv" | sed 's/"//g' | cut -c 2- | sed 's/\s.*$//')
				newsavereset=$(echo "saves/${newsavereset}.zip")

				mv ServerManager/.env ServerManager/.tempenv
				mv ServerManager/.createenv ServerManager/.env
				echo "Stoped and changed the env to create the save"
				echo "Starting to create new map"
				startForReset

				sleep 20s

				echo "Stoping map gen"
				stopForReset

				mv ServerManager/.env ServerManager/.createenv
				mv ServerManager/.tempenv ServerManager/.env

				saveaskednamemap=$(grep -oP "start-server\K.*" "ServerManager/.env" | sed 's/"//g' | cut -c 2- | sed 's/\s.*$//' | cut -c 4-)

				mv $newsavereset $saveaskednamemap
				echo "$newsavereset $saveaskednamemap"
				startForReset
				sleep 30s

				runRconMessage '/permissions edit-group Default add_permission_group false'
				runRconMessage '/permissions edit-group Default edit_permission_group false'
				runRconMessage '/permissions edit-group Default delete_permission_group false'
				runRconMessage '/permissions edit-group Default deconstruct false'
				runRconMessage '/permissions edit-group Default upgrade false'
				runRconMessage '/permissions edit-group Default activate_cut false'
				runRconMessage '/permissions create-group Trusted'
				runRconMessage '/permissions edit-group Trusted add_permission_group false'
				runRconMessage '/permissions edit-group Trusted edit_permission_group false'
				runRconMessage '/permissions edit-group Trusted delete_permission_group false'
				runRconMessage '/permissions create-group Admin'
				runRconMessage '/silent-command game.map_settings.enemy_expansion.enabled = false'

				discord "The map has been reseted on $server you can find the map at https://saves.joinandplaycoop.com/$server/ResetSaves/$date.zip"
			else
				echo "You cannot reset a server that is actually reseting please look at the database if it's not the case."
			fi
		fi
	fi
	echo "Finished"
	exit 1
}
#RESET STOP

#START START
start() {
	if [ -z "$webhook" ]; then
		echo "For help run this script with --help"
		echo "You need to input the server name (s1) (third parameter)"
	else
		server=$(echo "$webhook")
		runSQL <<EOF
			SELECT IsReseting
			FROM Server
			WHERE BINARY ServerName = "$server"
EOF

		echo "$server"

		if [[ $runSQLOutput == "0" ]]; then
			cd $path/ServerManager
			Screen=$(screen -ls)
			if [[ $Screen == *ServerManager${server}* ]]; then
				echo "Game already Started"
			else
				echo "Game was started"
				screen -dmS ServerManager${server} ./ServerManager
			fi
			runSQL <<EOF
			                UPDATE Server
        	    		    SET Status = "On"
             				WHERE BINARY ServerName = "$server"
EOF

		else
			echo "The server is currently reseting or the data in the database is wrong please be patient or correct it"
		fi
	fi
	echo "Finished"
	exit 1
}
#START STOP

#STOP START
stop() {
	if [ -z "$webhook" ]; then
		echo "For help run this script with --help"
		echo "You need to input the server name (s1) (third parameter)"
	else
		server=$(echo "$webhook")
		runSQL <<EOF
	    	SELECT IsReseting
			FROM Server
			WHERE BINARY ServerName = "$server"
EOF
		if [[ $runSQLOutput == "0" ]]; then
			runRconMessage "The server is currently stopping"
			echo "If you have errors here it's because the server is probably stoped"
			screen=$(screen -S ServerManager${server} -X stuff "/quit^M")
			screenls=$(screen -ls)
			while [[ $screenls == *ServerManager${server}* ]]; do
				screenls=$(screen -ls)
				screenquit=$(screen -S ServerManager${server} -X stuff "^M")
				let "seconds++"
				echo -ne "Checking if factorio is still running : $seconds"\\r
				sleep 1s

				if [[ $seconds -ge "60" ]]; then
					screen=$(screen -S ServerManager${server} -X stuff ^C)
				fi

			done
			echo ""
			echo "Stoped"
			runSQL <<EOF
            	UPDATE Server
                SET Status = "Off"
                WHERE BINARY ServerName = "$server"
EOF
		else
			echo "The server is currently reseting or the data in the database is wrong please be patient or correct it"
		fi

	fi
	echo "Finished"
	exit 1
}
#STOP STOP

#RESTART START
startForRestart() {
	if [ -z "$webhook" ]; then
		echo "For help run this script with --help"
		echo "You need to input the server name (s1) (third parameter)"
		echo "Finished"
		exit 1
	else
		server=$(echo "$webhook")
		runSQL <<EOF
	        	SELECT IsReseting
        	    FROM Server
               	WHERE BINARY ServerName = "$server"
EOF
		runSQLOutput="0"
		if [[ $runSQLOutput == "0" ]]; then
			cd $path/ServerManager
			Screen=$(screen -ls)
			if [[ $Screen == *ServerManager${server}* ]]; then
				echo "Game already Started"
			else
				echo "Game was started"
				screen -dmS ServerManager${server} ./ServerManager
			fi
			runSQL <<EOF
                UPDATE Server
                SET Status = "On"
                WHERE BINARY ServerName = "$server"
EOF

		else
			echo "The server is currently reseting or the data in the database is wrong please be patient or correct it"
		fi
	fi
}
stopForRestart() {
	if [ -z "$webhook" ]; then
		echo "For help run this script with --help"
		echo "You need to input the server name (s1) (third parameter)"
		echo "Finished"
		exit 1
	else
		server=$(echo "$webhook")
		runSQL <<EOF
	        	SELECT IsReseting
        	    FROM Server
            	WHERE BINARY ServerName = "$server"
EOF
		runSQLOutput="0"
		if [[ $runSQLOutput == "0" ]]; then
			echo "If you have errors here it's because the server is probably stoped"
			screen=$(screen -S ServerManager${server} -X stuff "/quit^M")
			screenls=$(screen -ls)
			while [[ $screenls == *ServerManager${server}* ]]; do
				screenls=$(screen -ls)
				screenquit=$(screen -S ServerManager${server} -X stuff "^M")
				let "seconds++"
				echo -ne "Checking if factorio is still running : $seconds"\\r
				sleep 1s
				if [[ $seconds -ge "60" ]]; then
					screen=$(screen -S ServerManager${server} -X stuff ^C)
				fi
			done
			echo ""
			echo "Stoped"
			runSQL <<EOF
                    UPDATE Server
	                SET Status = "Off"
        	        WHERE BINARY ServerName = "$server"
EOF

		else
			echo "The server is currently reseting or the data in the database is wrong please be patient or correct it"
		fi

	fi

}
restart() {
	runRconMessage "The server is currently restarting join back in a few seconds !"
	stopForRestart
	echo "Starting again in 5 seconds"
	sleep 5s
	startForRestart
	sleep 1s
	echo "Finished"
	exit 1
}
#RESTART STOP

#UPDATE START
startForUpdate() {
	server=$(echo "$fourthparm")
	runSQL <<EOF
	    SELECT IsReseting
        FROM Server
        WHERE BINARY ServerName = "$server"
EOF
	runSQLOutput="0"
	if [[ $runSQLOutput == "0" ]]; then
		cd ServerManager
		Screen=$(screen -ls)
		if [[ $Screen == *ServerManager${server}* ]]; then
			echo "Game already Started"
		else
			echo "Game was started"
			screen -dmS ServerManager${server} ./ServerManager
		fi
		runSQL <<EOF
        UPDATE Server
	    SET Status = "On"
        WHERE BINARY ServerName = "$server"
EOF
	else
		echo "The server is currently reseting or the data in the database is wrong please be patient or correct it"
	fi

}
stopForUpdate() {
	server=$(echo "$fourthparm")
	runSQL <<EOF
	    SELECT IsReseting
       	FROM Server
	    WHERE BINARY ServerName = "$server"
EOF
	runSQLOutput="0"
	if [[ $runSQLOutput == "0" ]]; then
		echo "If you have errors here it's because the server is probably stoped"
		screen=$(screen -S ServerManager${server} -X stuff "/quit^M")
		screenls=$(screen -ls)
		while [[ $screenls == *ServerManager${server}* ]]; do
			screenls=$(screen -ls)
			screenquit=$(screen -S ServerManager${server} -X stuff "^M")
			let "seconds++"
			echo -ne "Checking if factorio is still running : $seconds"\\r
			sleep 1s
			if [[ $seconds -ge "60" ]]; then
				screen=$(screen -S ServerManager${server} -X stuff ^C)
			fi

		done
		echo ""
		echo "Stoped"
		runSQL <<EOF
        	UPDATE Server
        	SET Status = "Off"
			WHERE BINARY ServerName = "$server"
EOF

	else
		echo "The server is currently reseting or the data in the database is wrong please be patient or correct it"
	fi
}

update() {
	if [ -z "$webhook" ]; then
		echo "For help run this script with --help"
		echo "You need to input the webhook (third parameter)"
		echo "Finished"
		exit 1
	else
		if [ -z "$fourthparm" ]; then
			echo "For help run this script with --help"
			echo "You need to input a server name (like s1) (fourth parameter)"
		else
			if [ -z "$fifthparm" ]; then
				echo "For help run this script with --help"
				echo "You need to input a version (latest or stable) (fifth parameter)"
			elif [[ "$fifthparm" == "stable" || "$fifthparm" == "latest" ]]; then
				server=$(echo ${fourthparm})
				runSQL <<EOF
		      		SELECT IsReseting
		        	FROM Server
	        		WHERE BINARY ServerName = "$server"
EOF
				runSQLOutput="0"
				if [[ $runSQLOutput == "0" ]]; then
					cd $path
					VersionServer=$(grep "Loading mod base" factorio-current.log 2>/dev/null | awk '{print $5}' | tail -1)
					LastestVersion=$(curl -s https://factorio.com/get-download/${fifthparm}/headless/linux64 | grep -o '[0-9]\.[0-9]\{1,\}\.[0-9]\{1,\}' | head -1)
					if [[ $VersionServer == $LastestVersion ]]; then
						echo "The server is at the lastest version : $LastestVersion"
					else
						echo "The server is not at the lastest version current : $VersionServer new one $LastestVersion"
						echo "updating....."
						runRconMessage "The server will be updated to the lastest version in 15 seconds"
						runRconMessage "Actual version : $VersionServer"
						runRconMessage "New version who will be installed : $LastestVersion"
						runRconMessage "Update by your side and continue playing right where you are !"

						sleep 15s

						stopForUpdate

						rm -rf bin/x64/factorio
						rm -rf data/core
						rm -rf data/base

						wget -O factorio_headless.tar.xz https://www.factorio.com/get-download/${fifthparm}/headless/linux64
						tar -vxf factorio_headless.tar.xz

						mv factorio/data/core data/core
						mv factorio/data/base data/base
						mv factorio/bin/x64/factorio bin/x64/factorio

						rm -rf factorio
						rm factorio_headless.tar.xz

						sleep 1s

						startForUpdate
						echo "The server has been updated to $LastestVersion and is now starting"
						discord "The server $server was updated to the lastest version : $LastestVersion"
					fi
				else
					echo "The server is currently reseting or the data in the database is wrong please be patient or correct it"
				fi
			else
				echo "Please enter stable or latest"
			fi
		fi
	fi
	echo "Finished"
	exit 1
}
#UPDATE STOP

#UPDATESCENARIO START
updateScenario() {
	if [ -z "$webhook" ]; then
		echo "For help run this script with --help"
		echo "You need the repo url (https://github.com/youruser/repo.git) (third parameter)"
	else
		if [ -z "$fourthparm" ]; then
			echo "For help run this script with --help"
			echo "You need to input the folder where the scenario is (fourth parameter) (if there is none put . ) (example it's in the factorio folder you put factorio)"
		else
			mkdir -p $path/tmp

			rm -rf $path/tmp/factorioScenarioDownload

			mkdir -p $path/tmp/factorioScenarioDownload

			git clone --quiet $webhook $path/tmp/factorioScenarioDownload

			rm -rf $path/scenarios/JoinAndPlayCoop-Scenario

			mkdir -p $path/scenarios

			mkdir -p $path/scenarios/JoinAndPlayCoop-Scenario

			mv -v $path/tmp/factorioScenarioDownload/${fourthparm}/* $path/scenarios/JoinAndPlayCoop-Scenario

			echo "Scenario copied"

			rm -rf $path/tmp/factorioScenarioDownload

			echo "Removed temp files"
		fi
	fi
	echo "Finished"
	exit 1
}
#UPDATESCENARIO STOP

#UPDATEMODS START
updateMods() {
	if [ -z "$webhook" ]; then
		echo "For help run this script with --help"
		echo "You need to input the server name (s1) (third parameter)"
	else
		cd $path
		mkdir -p tmp/modsDL

		stopForRestart

		#Variables

		tmpdir="tmp/modsDL"
		serverfiles="."

		declare ver
		declare installed_mods

		mkdir -p -p ${tmpdir}/factorio
		rm -rf ${tmpdir}/factorio/mods-download
		mkdir -p -p ${tmpdir}/factorio/mods-download

		fn_check_download_factorio_mods() {
			installed_mods=$(grep -Po '"name":.*?[^\\]",' ${serverfiles}/mods/mod-list.json | tr -d '"' | cut -c 7- | sed 's/.$//')
			while read -r installed_mods; do
				(
					while [[ ${get_mod_version} != "null" ]]; do
						ver=$((ver + 1))
						get_mod_version=$(curl -s --request GET https://mods.factorio.com/api/mods/${installed_mods} | jq ".releases[${ver}] .info_json .factorio_version" | sed -e 's/^"//' -e 's/"$//')
						download_url="${installed_mods}:"
						echo "${get_mod_version}:${ver}"
					done
				) | while read ver_check; do
					currentbuild=$(grep "Loading mod base" "${serverfiles}/factorio-current.log" 2>/dev/null | awk '{print $5}' | tail -1 | cut -f1,2 -d'.')
					ver=$(cut -d ":" -f 2 <<<"$ver_check")
					mod_ver=$(echo "${ver_check}" | cut -f1 -d":")
					echo -ne "\r                                                                                                                                                  \r$installed_mods Current version : $currentbuild Version : $mod_ver Checking array : $ver\r"
					if [[ ${mod_ver} == ${currentbuild} ]]; then
						get_mod_download=$(curl -s --request GET https://mods.factorio.com/api/mods/${installed_mods} | jq ".releases[${ver}] .download_url" | sed -e 's/^"//' -e 's/"$//')
						echo "https://mods.factorio.com${get_mod_download}" >${tmpdir}/factorio/mods-download/$installed_mods
					fi
				done
			done <<<"${installed_mods}"
		}
		fn_download_factorio_mods() {
			mv ${serverfiles}/mods/mod-list.json ${tmpdir}/factorio/mod-list.json
			login_data="?username=${username}&token=${token}"
			for mod_dl in "${tmpdir}/factorio/mods-download"/*; do
				download=$(cat "$mod_dl")
				download_full=$(echo "${download}${login_data}")
				wget --content-disposition -qP ${serverfiles}/mods/ ${download_full}
				echo "Downloading ${download}"
			done
			find ${serverfiles}/mods -type f -name "*\?*" -exec sh -c 'mv $1 $(echo $1 | cut -d\? -f1)' mv {} \;
			mv ${tmpdir}/factorio/mod-list.json ${serverfiles}/mods/mod-list.json
			rm -rf ${tmpdir}/factorio
		}
		fn_check_download_factorio_mods
		files=(${tmpdir}/factorio/mods-download/*)
		if [ ${#files[@]} -gt 0 ]; then
			fn_download_factorio_mods
			echo "Finished downloaded the mods"
		fi

		startForRestart

	fi
	echo "Finished"
	exit 1

}
#UPDATEMODS STOP

#RUNCOMMAND START
runCommand() {
	if [ -z "$webhook" ]; then
		echo "For help run this script with --help"
		echo "You need to input the command (third parameter)"
	else
		runRconMessage ''"${webhook}"''
		echo "$runRconOutput"
	fi
}
#RUNCOMMAND STOP

#ROCKETCOUNTERDISCORD START
rocketCounterDiscord() {
	if [ -z "$webhook" ]; then
		echo "For help run this script with --help"
		echo "You need to input a webhook url (third parameter)"
	else
		rocketsCount
		discord "Total number of rockets sent accross all forces : $TotalRocketCount"
	fi
}
#ROCKETCOUNTERDISCORD STOP

#INFORM START
inform() {
	message="Join us at discord.joinandplaycoop.com !"
	message1="Take a look at our website joinandplaycoop.com. And also stats : stats.joinandplaycoop.com"
	message2="If you need an administrator mention @Admin or @Moderator in this chat and we will come as soon as possible"
	message3="Have fun playing here !"

	if [ -n "$message" ]; then
		runRconMessage "$message"
	elif [ -n "$message1" ]; then
		runRconMessage "$message1"
	elif [ -n "$message2" ]; then
		runRconMessage "$message2"
	elif [ -n "$message3" ]; then
		runRconMessage "$message3"
	fi
}
#INFORM STOP

#INFORMRESET START
informReset() {
	if [ -z "$webhook" ]; then
		echo "For help run this script with --help"
		echo "You need to input the reset schedule (third parameter) like Friday at 9AM"
	else
		message="Remember we reset this server $webhook"
		runRconMessage "$message"
	fi
}
#INFORMRESET STOP

#ROCKETRESET START
rocketReset() {
	rocketResetInternalFunc() {
		server=$(echo "$fourthparm")
		rocketsCount
		if [[ $TotalRocketCount -ge $fifthparm ]]; then
			echo "Rockets sent $TotalRocketCount reseting (goal $fifthparm)"
			discord "The players of $server launched $TotalRocketCount the goal of $fifthparm has been touched the server will now reset"
			runRconMessage "Dear players, GG! you touched the goal of $fifthparm (Rockets sent : $TotalRocketCount)"
			runRconMessage "The server will now reset"
			reset
			echo "Finished"
		else
			echo "Rockets sent $TotalRocketCount"
			echo "Finished"
		fi
	}

	if [ -z "$webhook" ]; then
		echo "For help run this script with --help"
		echo "You need to input the webhook (third parameter)"
		echo "Finished"
		exit 1
	else
		if [ -z "$fourthparm" ]; then
			echo "For help run this script with --help"
			echo "You need to input a server name (like s1) (fourth parameter)"
			echo "Finished"
			exit 1
		fi
		if [ -z "$fifthparm" ]; then
			fifthparm=$(sed -n '9p' $path/server-config.txt | sed 's/"//g')

			if [ -z "$fifthparm" ]; then
				echo "For help run this script with --help"
				echo "You need to input a rocket goal (like 1000) (fifth parameter)"
			else
				rocketResetInternalFunc
			fi

		else
			rocketResetInternalFunc
		fi
	fi

}
#ROCKETRESET STOP

#ROCKETRESETINFO START
rocketResetInfo() {
	if [ -z "$webhook" ]; then
		echo "For help run this script with --help"
		echo "You need to input the server name (like s1) (third parameter)"
		echo "Finished"
		exit 1
	else
		if [ -z "$fourthparm" ]; then
			fourthparm=$(sed -n '9p' ${path}/server-config.txt | sed 's/"//g')

			if [ -z "$fourthparm" ]; then
				echo "For help run this script with --help"
				echo "You need to input the rocket goal (like 1000) (fourth parameter)"
				echo "Finished"
				exit 1
			else
				rocketsCount
				server=$(echo "$webhook")

				#if [[ $TotalRocketCount -ge $fourthparm ]]
				#then
				#	echo "Rockets sended $TotalRocketCount reseting"
				#	discord "The players of $serverfordiscord launched $TotalRocketCount the goal of $fifthparm has been touched the server will now reset"
				#	runRconMessage "Dear players, GG! you touched the goal of $fifthparm (Rockets sended : $TotalRocketCount)"
				#	runRconMessage "The server will now reset"
				#	reset
				#	echo "Finished"
				#else
				echo "Rockets sent accross all forces $TotalRocketCount (Goal : $fourthparm rockets)"
				runRconMessage "Rockets sent accross all forces $TotalRocketCount (Goal : $fourthparm rockets)"

				echo "Finished"
				#fi
			fi
		fi
	fi

}
#ROCKETRESETINFO STOP

#ROCKETINFO START
rocketInfo() {
	rocketsCount
	echo "Rockets sent accross all forces $TotalRocketCount"
	runRconMessage "Rockets sent accross all forces $TotalRocketCount"
	echo "Finished"

}
#ROCKETINFO STOP

#SENDTIME START
sendTime() {
	currentTime=$(date "+%H:%M")
	runRconMessage "Current time : $currentTime (Central Europe)"
}
#SENDTIME STOP

#ROLLBACK | Upload START
rollBack() {

	if [ -z "$fourthparm" ]; then
		echo "For help run this script with --help"
		echo "You need to input the rollback save path (fourth parameter) (like ../server/s1/oldsave/mysave.zip)"
		echo "Finished"
		exit 1
	else
		runRconMessage "The server is currently behing rolled back ! Or a new game save has been uploaded ! Come back in a few seconds"
		stopForRestart
		server=$(echo "$webhook")
		newSavePath=$(grep "start-server" ../server/${server}/ServerManager/.env | sed 's/"//g' | cut -c 20- | grep -o 'start-server .*' | cut -f1,2 -d' ' | awk '{gsub("start-server ", "");print}' | cut -c 3-)
		newSavePath=$(echo "${path}${newSavePath}")

		echo "Coyping the save to rollback | Upload"

		cp $fourthparm $newSavePath

		chmod 777 -R $newSavePath

		echo "Normally the save has been moved, we will now start the server"

		startForRestart

		echo "Finished"
		exit 1
	fi

}
#ROLLBACK | Upload STOP

#INSTALLVIAWEBPANEL START

installViaWebPanel() {
	if [ -z "$webhook" ]; then
		echo "For help run this script with --help"
		echo "You need to input the server name (like s1) (third parameter)"
		echo "Finished"
		exit 1
	else

		echo "The grep errors where checks"

		server=$(echo "$webhook")

		echo "Copying the template files"

		cp -R ../servertemplate ../server/${server}

		cd ${path}/${server}

		cd ../../

		currentdir="$(cd "$(dirname "${BASH_SOURCE[0]}")" >/dev/null 2>&1 && pwd)"

		mkdir saves/${server}

		ln -s ../../server/${server}/LastestSaves saves/${server}/LastestSaves
		ln -s ../../server/${server}/OldSaves saves/${server}/OldSaves
		ln -s ../../server/${server}/ResetSaves saves/${server}/ResetSaves

		cd server/${server}

		echo "Downloading factorio"

		wget -O factorio_headless.tar.xz https://www.factorio.com/get-download/latest/headless/linux64

		echo "Decompressing factorio"

		tar -xf factorio_headless.tar.xz

		mv factorio/* .

		echo "Removing factorio downloaded files and folders and moving a config file"

		rm factorio_headless.tar.xz
		rm -rf factorio/

		mv fctrserver.json data/

		echo "Copying the template again to be sure of the config"

		yes | cp -Rf ../../servertemplate/* .

		chmod 777 -R ../*

		echo "Finished"

		#DB part to add the new server if needed

		runSQL <<EOF
        	SELECT ServerName
            FROM Server
	        WHERE BINARY ServerName = "$server"
EOF
		if [ -z "$runSQLOutput" ]; then
			runSQL <<EOF
                INSERT INTO Server (ServerName)
                VALUES ("$server")
EOF
		fi

		exit 1
	fi

}

#INSTALLVIAWEBPANEL STOP

#REMOVEVIAWEBPANEL START

removeViaWebPanel() {
	if [ -z "$webhook" ]; then
		echo "For help run this script with --help"
		echo "You need to input the server name (like s1) (third parameter)"
		echo "Finished"
		exit 1
	else
		server=$(echo "$webhook")

		#Turning the server off part

		screen=$(screen -S ServerManager${server} -X stuff "/quit^M")
		screenls=$(screen -ls)
		while [[ $screenls == *ServerManager${server}* ]]; do
			screenls=$(screen -ls)
			screenquit=$(screen -S ServerManager${server} -X stuff "^M")
			let "seconds++"
			echo -ne "Checking if factorio is still running : $seconds"\\r
			sleep 1s
		done
		echo ""
		echo "Stoped"

		echo "Removing the server in the database"

		#DB remove part

		runSQL <<EOF
			SELECT Id
			FROM Server
			WHERE BINARY ServerName = "$server"
EOF

		if ! [ -z "$runSQLOutput" ]; then
			runSQL <<EOF
				CALL DeleteServer("$runSQLOutput",1)
EOF
		fi

		# Folder removal

		echo "Removing the folder"

		cd ${path}/${server}

		cd ../../

		rm -rf saves/${server}

		rm -rf server/${server}

		echo "Finished"
	fi
}

#REMOVEVIAWEBPANEL STOP

#Test if the user putted at least the function and at least the home path
if [[ $run == "--help" ]]; then
	help
elif [ -z "$run" ]; then
	echo "For help run this script with --help"
elif [ -z "$path" ]; then
	echo "For help run this script with --help"
	echo "You need to input a path"
else
	path=$(echo $path | sed 's/\/$//')
	#OTHER VAR
	#Saves
	savelocation=$(grep -oP "start-server\K.*" "${path}/ServerManager/.env" | sed 's/"//g' | cut -c 2- | sed 's/\s.*$//' | cut -c 3-)
	savelocation=$(echo "${path}${savelocation}")
	#RCON
	HOST="127.0.0.1"
	PORT=$(grep -oP "rcon-port\K.*" "${path}/factorio-current.log" | sed 's/"//g' | cut -c 2- | sed 's/\s.*$//')
	PASSWORD=$(grep -oP "rcon-password\K.*" "${path}/ServerManager/.env" | sed 's/"//g' | cut -c 2- | sed 's/\s.*$//')

	if [[ $run == "--help" ]]; then
		help
	elif [[ $run == "--start" ]]; then
		start
	elif [[ $run == "--stop" ]]; then
		stop
	elif [[ $run == "--restart" ]]; then
		restart
	elif [[ $run == "--update" ]]; then
		update
	elif [[ $run == "--update-scenario" ]]; then
		updateScenario
	elif [[ $run == "--update-mods" ]]; then
		updateMods
	elif [[ $run == "--reset" ]]; then
		reset
	elif [[ $run == "--run-command" ]]; then
		runCommand
	elif [[ $run == "--rollback" ]]; then
		rollBack
	elif [[ $run == "--install-via-webpanel" ]]; then
		installViaWebPanel
	elif [[ $run == "--remove-via-webpanel" ]]; then
		removeViaWebPanel
	fi

	#Everything before here does not need the rcon to work (server stoped)

	testConnection=$({ timeout 2 mcrcon -c -H $HOST -P $PORT -p $PASSWORD "/version" | grep runRconError; } 2>&1)
	exit_status=$?
	if [[ $exit_status -eq 124 ]]; then
		#status off, no other data
		echo "Finished"
		exit 1
	fi
	if grep -q "Connection failed." <<<"$testConnection"; then
		echo "There was an error using rcon"
		echo "> $testConnection <"
		echo "Finished"
		exit 1
	fi

	#Everything after here need rcon to work (server on)

	if [[ $run == "--help" ]]; then
		:
	elif [[ $run == "--start" ]]; then
		:
	elif [[ $run == "--stop" ]]; then
		:
	elif [[ $run == "--restart" ]]; then
		:
	elif [[ $run == "--update" ]]; then
		:
	elif [[ $run == "--update-scenario" ]]; then
		:
	elif [[ $run == "--update-mods" ]]; then
		:
	elif [[ $run == "--reset" ]]; then
		:
	elif [[ $run == "--rollback" ]]; then
		:
	elif [[ $run == "--install-via-webpanel" ]]; then
		:
	elif [[ $run == "--remove-via-webpanel" ]]; then
		:
	elif [[ $run == "--rocket-reset" ]]; then
		rocketReset
	elif [[ $run == "--run-command" ]]; then
		:
	elif [[ $run == "--autoperms" ]]; then
		autoperms
	elif [[ $run == "--save" ]]; then
		save
	elif [[ $run == "--rocket-discord" ]]; then
		rocketCounterDiscord
	elif [[ $run == "--inform" ]]; then
		inform
	elif [[ $run == "--inform-reset" ]]; then
		informReset
	elif [[ $run == "--time" ]]; then
		sendTime
	elif [[ $run == "--rocket-sended" ]]; then
		rocketInfo
	elif [[ $run == "--rocket-reset-inform" ]]; then
		rocketResetInfo
	else
		echo "For help run this script with --help"
	fi
fi

chmod 777 run.log
echo "Finished you can now go back to the pannel" >>run.log
