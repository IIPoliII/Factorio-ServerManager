#!/bin/bash
PATH=/opt/someApp/bin:/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin

#VARIABLES TO MODIFY IF NEEDED

DIRSCRIPT="$(cd "$(dirname "${BASH_SOURCE[0]}")" >/dev/null 2>&1 && pwd)"
DIRSCRIPT="${DIRSCRIPT}/"

ServerName="$1"
HOST="127.0.0.1"
PORT=$(sed -n '7p' ${DIRSCRIPT}../server/${ServerName}/ServerManager/.env | sed 's/"//g' | cut -c 20- | grep -o 'rcon-port .*' | cut -f1,2 -d' ' | awk '{gsub("rcon-port ", "");print}')
GAMEPORT=$(sed -n '7p' ${DIRSCRIPT}../server/${ServerName}/ServerManager/.env | sed 's/"//g' | cut -c 20- | grep -o 'port .*' | cut -f1,2 -d' ' | awk '{gsub("port ", "");print}')
PASSWORD=$(sed -n '7p' ${DIRSCRIPT}../server/${ServerName}/ServerManager/.env | sed 's/"//g' | cut -c 20- | grep -o 'rcon-password .*' | cut -f1,2 -d' ' | awk '{gsub("rcon-password ", "");print}')

#Access to the database here

DBServer=$(sed -n '2p' ${DIRSCRIPT}../config.txt | sed 's/"//g' | cut -c 9-)
DBPort=$(sed -n '3p' ${DIRSCRIPT}../config.txt | sed 's/"//g' | cut -c 11-)
DBUser=$(sed -n '4p' ${DIRSCRIPT}../config.txt | sed 's/"//g' | cut -c 11-)
DBPassword=$(sed -n '5p' ${DIRSCRIPT}../config.txt | sed 's/"//g' | cut -c 10-)
DB=$(sed -n '1p' ${DIRSCRIPT}../config.txt | sed 's/"//g' | cut -c 9-)

export MYSQL_PWD="$DBPassword"

DBLogTable="Log"
DBPermissionsTable="Permissions"
DBPlayersTable="Players"
DBServerTable="Server"
DBTicksTable="Ticks"
DBConnectedServerTable="ConnectedServer"
DBPlayerPlayTimeTable="PlayerPlaytime"
DBPlayersOnlineTable="PlayersOnline"
DBMaterialTable="Material"
DBProductionTable="Production"

#Basis config

#Shity things for shity SQL

#Test the SQL server connection

timeout 5 mysql -sN --user="$DBUser" -h $DBServer -P $DBPort --database="$DB" </dev/null
if [ $? -eq 0 ]; then
	echo "Connection to the SQL server OK"
else
	echo "Connection to the SQL server NOT OK"
	exit 0
fi

runSQL() {
	runSQLOutput=$(timeout 5 mysql -sN --user="$DBUser" -h $DBServer -P $DBPort --database="$DB")
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
		runSQL <<EOF
                UPDATE $DBServerTable
                SET Status = "Off"
                WHERE BINARY ServerName = "$ServerName"
EOF
		exit 1
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
	runSQL <<EOF
                UPDATE $DBServerTable
                SET Status = "On"
                WHERE BINARY ServerName = "$ServerName"
EOF
}

#Factorio functions to get data

getConnectedPlayers() {
	runRcon "/players online"
	ConnectedPlayers=$(echo "$runRconOutput" | wc -l)
	if [ -z "$ConnectedPlayers" ]; then
		ConnectedPlayers=1
	fi
	let ConnectedPlayers--
}

getTotalPlayers() {
	runRcon "/players"
	TotalPlayers=$(echo "$runRconOutput" | wc -l)
	let TotalPlayers--
}

getPlayTime() {
	runRcon "/silent-command rcon.print(game.tick)"
	PlayTime=$(echo $runRconOutput)
	let "PlayTime=${PlayTime} / 60"
	#echo "Current Tick : $PlayTime"
}
getServerVersion() {
	runRcon "/version"
	ServerVersion=$(echo $runRconOutput)

}
getAlienEvolution() {
	runRcon "/evolution"
	AlienEvolution=$(echo $runRconOutput | sed 's/Evolution factor: //g' | cut -f1,2 -d'.')
}
getServerVersion() {
	runRcon "/version"
	Version=$(echo $runRconOutput)
}
getTotalRocketCount() {
	rockets=0
	runRcon '/silent-command for name, force in pairs (game.forces) do rcon.print(name) end'
	mapfile -t Forces < <(printf "%s\n" "$runRconOutput")
	for Force in "${Forces[@]}"; do
		if [[ ${Force} != "" ]]; then
			runRcon '/silent-command rcon.print(game.forces["'"${Force}"'"].items_launched["satellite"])'
			rockets=$(echo $runRconOutput | sed 's/\x1b\[[0-9;]*m//g')
			if [[ "$rockets" -eq "nil" ]]; then
				rockets=0
			fi
			if ! [ "$rockets" -eq "$rockets" ] 2>/dev/null; then
				rockets=0
			fi
			TotalRocketCount=$(($TotalRocketCount + $rockets))
		else
			TotalRocketCount=$(($TotalRocketCount + 0))
		fi
	done
}
getPlayerPlayTime() {
	#We will create 2 arrays one with the player names another one with the playtime in ticks or minutes
	PlayerTime=()
	runRcon "/players online"
	mapfile -t PlayerList < <(printf "%s\n" "$runRconOutput" | sed 's/ (online)//g' | sed '1d' | tr -d ' ')
	for Player in "${PlayerList[@]}"; do
		Player=$(echo $Player)
		runRcon '/silent-command rcon.print(game.players["'"${Player}"'"].online_time / 60)'
		PlayerTimeString=$(echo "$runRconOutput" | awk '{print int($1+0.5)}')
		PlayerTime+=("PlayerTimeString")
		#echo "$Player $PlayerTimeString"
	done
}

getItemNames() {

	runRcon "/silent-command for name, _ in pairs(game.item_prototypes) do; rcon.print(name); end"
	mapfile -t ItemNames < <(printf "%s\n" "$runRconOutput")
}

getItemProductionCount() {
	getItemNames
	getServerId

	ItemCount=()

	runRcon "/silent-command for name, force in pairs (game.forces) do rcon.print(name) end"
	mapfile -t Forces < <(printf "%s\n" "$runRconOutput")
	for Force in "${Forces[@]}"; do
		ItemArrayNb=0
		for Item in "${ItemNames[@]}"; do
			CurrentItemCount=$(timeout 15 mcrcon -c -H $HOST -P $PORT -p $PASSWORD '/silent-command local count = game.forces["'"${Force}"'"].item_production_statistics.get_output_count("'"${Item}"'") rcon.print(count)')
			if [[ "$CurrentItemCount" == "nil" ]]; then
				CurrentItemCount=0
			fi
			if ! [ "$CurrentItemCount" -eq "$CurrentItemCount" ] 2>/dev/null; then
				runSQL <<EOF
                                        SELECT P.NumberProduced
                                        FROM Production P
                                        INNER JOIN Material AS M ON P.FKMaterialId = M.Id
                                        WHERE M.InGameName = "$Item"
                                        AND P.FKServerId = "$ServerId"
                                        ORDER BY CreatedDate DESC
                                        LIMIT 1
EOF

				CurrentItemCount=$(echo $runSQLOutput)
			fi
			if [[ "$CurrentItemCount" == "" ]]; then
				runSQL <<EOF
	                                SELECT P.NumberProduced
	                                FROM Production P
	                                INNER JOIN Material AS M ON P.FKMaterialId = M.Id
	                                WHERE M.InGameName = "$Item"
	                                AND P.FKServerId = "$ServerId"
	                                ORDER BY CreatedDate DESC
	                                LIMIT 1
EOF

				CurrentItemCount=$(echo $runSQLOutput)

			fi

			let "CurrentItemCount=CurrentItemCount + ItemCount[$ItemArrayNb]"

			ItemCount[$ItemArrayNb]="$CurrentItemCount"

			echo "$Item was produced $CurrentItemCount times by $Force"
			let "ItemArrayNb++"

		done

	done
}
#MySQL functions to get the old data in the database

getServerId() {
	runSQL <<EOF
        SELECT Id
        FROM $DBServerTable
        WHERE BINARY ServerName = "$ServerName"
EOF
	ServerId=$(echo $runSQLOutput | grep -o '^\S*')
}
getFKSeverTicksId() {
	getServerId

	runSQL <<EOF
                SELECT max(Id)
                FROM $DBTicksTable
                WHERE FKServerId = "$ServerId"
EOF
	FKServerTicksId=$(echo $runSQLOutput)
}
getOldTicks() {
	getFKSeverTicksId

	runSQL <<EOF
                SELECT CurrentTicks
                FROM $DBTicksTable
                WHERE Id = "$FKServerTicksId"
EOF
	OldTicks=$(echo $runSQLOutput)
	#echo "Old DB Tick $OldTicks"
}
getTickInterval() {
	getOldTicks
	getPlayTime

	TickInterval=$(echo "$PlayTime-$OldTicks" | bc)
	#echo "Interval $TickInterval"
}

#To consome less data functions to get old data and if it's the same do not reput it in

getOldRocketCount() {
	getServerId

	runSQL <<EOF
		SELECT RocketCount
		FROM $DBLogTable
		WHERE FKServerId = "$ServerId"
		ORDER BY CreatedDate DESC
		LIMIT 1
EOF

	OldRocketCount=$(echo $runSQLOutput)
}
#MySQL functions to send data to database

insertItemNamesIfNotExsits() {
	getItemNames

	runSQL <<EOF
		SELECT InGameName
		FROM $DBMaterialTable
EOF

	echo "${ItemNames[*]}"
	mapfile -t DBMaterialNames < <(printf "%s\n" "$runSQLOutput")

	for Item in "${ItemNames[@]}"; do
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

		arrayCheck=$(array_contains DBMaterialNames "$Item" && echo yes || echo no)
		if [[ $arrayCheck == "no" ]]; then
			runSQL <<EOF
				SELECT InGameName
				FROM $DBMaterialTable
				WHERE InGameName = "$Item"
EOF
			if [[ -z "$runSQLOutput" ]]; then
				echo "added $Item"
				runSQL <<EOF
						INSERT INTO $DBMaterialTable (InGameName)
						VALUES ("$Item")
EOF
			fi
		fi

	done

}

insertProduction() {
	insertServerNameIfNotExsists
	getServerId
	getItemProductionCount
	insertItemNamesIfNotExsits

	ItemCountArrayNb=0

	for ItemCount in "${ItemCount[@]}"; do
		CurrentItemName=$(echo "${ItemNames[$ItemCountArrayNb]}")
		runSQL <<EOF
			SELECT Id
			FROM $DBMaterialTable
			WHERE InGameName = "$CurrentItemName"
EOF

		ItemNumberDB=$(echo $runSQLOutput | sed $'s/[^[:print:]\t]//g')

		runSQL <<EOF
			INSERT INTO $DBProductionTable (FKMaterialId, FKServerId, NumberProduced)
			VALUES ("$ItemNumberDB", "$ServerId", "$ItemCount")
EOF

		let "ItemCountArrayNb++"

	done

}

insertServerNameIfNotExsists() {
	runSQL <<EOF
        SELECT ServerName
        FROM $DBServerTable
        WHERE BINARY ServerName = "$ServerName"
EOF

	runSQLOutput=$(echo $runSQLOutput | grep -o '^\S*')

	if [[ -z "$runSQLOutput" ]]; then
		runSQL <<EOF
            INSERT INTO $DBServerTable (ServerName)
        	VALUES ("$ServerName")
EOF
	fi
}
insertServerVersion() {
	getServerVersion

	runSQL <<EOF
		SELECT Version
		FROM $DBServerTable
		WHERE BINARY ServerName = "$ServerName"
EOF
	if [[ $runSQLOutput != $Version ]]; then
		runSQL <<EOF
            UPDATE $DBServerTable
            SET Version = "${Version}"
            WHERE BINARY ServerName = "${ServerName}"
EOF
	fi

}
insertServerIp() {
	runSQL <<EOF
		SELECT IP
		FROM $DBServerTable
		WHERE BINARY ServerName = "$ServerName"
EOF
	CurrentIp=$(curl -s ifconfig.me)

	CurrentIp="${CurrentIp}:${GAMEPORT}"

	if [[ $runSQLOutput != $CurrentIp ]]; then
		runSQL <<EOF
			UPDATE $DBServerTable
			SET IP = "${CurrentIp}"
			WHERE BINARY ServerName = "${ServerName}"
EOF
	fi
}
insertServerCurrentTicks() {
	getServerId
	getPlayTime
	getServerId
	getOldTicks

	runSQL <<EOF
		SELECT OldTicks
		FROM $DBTicksTable
		WHERE FKServerId = "$ServerId"
EOF

	if [ -z "$runSQLOutput" ]; then
		OldTicks="$PlayTime"
	fi
	runSQL <<EOF
        INSERT INTO $DBTicksTable (FKServerId, CurrentTicks, OldTicks)
        VALUES ("$ServerId", "$PlayTime", "$OldTicks")
EOF

}
insertMainData() {
	getConnectedPlayers
	getTotalPlayers
	getTotalRocketCount
	getAlienEvolution
	getServerId
	getFKSeverTicksId

	runSQL <<EOF
		INSERT INTO $DBLogTable (FKServerId, FKTicksId, TotalPlayersOnline, TotalPlayers, RocketCount, AlienEvolution)
		VALUES ("$ServerId", "$FKServerTicksId", "$ConnectedPlayers", "$TotalPlayers", "$TotalRocketCount", "$AlienEvolution")
EOF
}
insertPlayer() {
	getPlayerPlayTime

	for Player in "${PlayerList[@]}"; do
		runSQL <<EOF
            SELECT Id
            FROM $DBPlayersTable
            WHERE PlayerName = "$Player"
EOF
		if [[ -z $runSQLOutput ]]; then
			runSQL <<EOF
				INSERT INTO $DBPlayersTable (PlayerName)
				VALUES ("$Player")
EOF
		fi
	done

}

insertPlayerPlayTime() {
	getPlayerPlayTime
	getTickInterval
	getServerId

	currentArray=0

	for Player in "${PlayerList[@]}"; do
		runSQL <<EOF
			SELECT Id
			FROM $DBPlayersTable
			WHERE PlayerName = "$Player"
EOF
		echo "$Player"
		IdPlayer=$runSQLOutput

		#Check if the player already connected the server

		runSQL <<EOF
			SELECT Id
			FROM $DBConnectedServerTable
			WHERE FKPlayerId = "$IdPlayer"
			AND FKServerId = "$ServerId"
EOF

		if [[ -z $runSQLOutput ]]; then
			#If no then add that he connected
			echo "Player is not in the database here was the output of the sql query : $runSQLOutput"
			runSQL <<EOF
                INSERT INTO $DBConnectedServerTable (FKPlayerId, FKServerId)
				VALUES ("$IdPlayer", "$ServerId")
EOF
			#Search for Ticks if he already have some
			runSQL <<EOF
				SELECT Ticks
				FROM $DBPlayerPlayTimeTable
				WHERE FKPlayerId = "$IdPlayer"
				ORDER BY Id
				DESC LIMIT 1
EOF
			if [ -z $runSQLOutput ]; then
				#If no then simply add his current game time
				runSQL <<EOF
					INSERT INTO $DBPlayerPlayTimeTable (FKPlayerId, Ticks)
					VALUES ("$IdPlayer", "${PlayerTime[$currentArray]}")
EOF
			else
				#if yes add his current game time + the lastest ticks but do a check if it's bigger than the older ticks if not do nothing

				PlayerPlayTimeMoreLastest=$(echo "$runSQLOutput+${PlayerTime[$currentArray]}" | bc)
				if [[ $PlayerPlayTimeMoreLastest -gt $runSQLOutput ]]; then
					runSQL <<EOF
						INSERT INTO $DBPlayerPlayTimeTable (FKPlayerId, ticks)
						VALUES ("$IdPlayer", "$PlayerPlayTimeMoreLastest")
EOF
				fi
			fi
		else
			#If the player already connected the server add his playtime to the lastest one with the tick difference

			runSQL <<EOF
                SELECT ticks
                FROM $DBPlayerPlayTimeTable
                WHERE FKPlayerId = "$IdPlayer"
				ORDER BY Id
                DESC LIMIT 1
EOF
			let "PlayerPlayTimeMoreLastest=$runSQLOutput+$TickInterval"

			if [[ $PlayerPlayTimeMoreLastest -gt $runSQLOutput ]]; then
				runSQL <<EOF
						INSERT INTO $DBPlayerPlayTimeTable (FKPlayerId, ticks)
						VALUES ("$IdPlayer", "$PlayerPlayTimeMoreLastest")
EOF
			fi
		fi
		let "currentArray++"
	done
}

insertPlayersOnline() {
	getPlayerPlayTime
	getServerId

	for Player in "${PlayerList[@]}"; do

		runSQL <<EOF
                        SELECT Id
                        FROM $DBPlayersTable
                        WHERE PlayerName = "$Player"
EOF
		IdPlayer=$runSQLOutput

		runSQL <<EOF
                        SELECT FKPlayerId
                        FROM $DBPlayersOnlineTable
                        WHERE FKPlayerId = "$IdPlayer"
EOF

		if [[ -z $runSQLOutput ]]; then

			runSQL <<EOF
				INSERT INTO $DBPlayersOnlineTable (FKPlayerId, FKServerId)
				VALUES ("$IdPlayer", "$ServerId")
EOF

		fi
	done

	runSQL <<EOF
                SELECT P.PlayerName
                FROM $DBPlayersOnlineTable
                INNER JOIN $DBPlayersTable as P ON PlayersOnline.FKPlayerId = P.Id
EOF

	mapfile -t PlayersCurrentlyOnlineOnDB < <(printf "%s\n" "$runSQLOutput")
	for PlayersCurrentlyOnlineOnDB in "${PlayersCurrentlyOnlineOnDB[@]}"; do

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

		arrayCheck=$(array_contains PlayerList "$PlayersCurrentlyOnlineOnDB" && echo yes || echo no)
		if [[ $arrayCheck == "no" ]]; then
			#Remove from DB

			runSQL <<EOF
                       			SELECT Id
                      			FROM $DBPlayersTable
                	        	WHERE PlayerName = "$PlayersCurrentlyOnlineOnDB"
EOF
			IdPlayer=$runSQLOutput

			runSQL <<EOF
                                   DELETE FROM $DBPlayersOnlineTable
                                   WHERE FKPlayerId = "$IdPlayer" AND FKServerId = "$ServerId"
EOF
		fi

	done

}

#There is an order insert dont go in funciton so its diveded table by table

insertServerNameIfNotExsists
insertMainData
insertPlayer
insertPlayerPlayTime
insertServerCurrentTicks
insertPlayersOnline
insertServerIp
insertServerVersion
insertProduction
