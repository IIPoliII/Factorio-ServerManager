#!/bin/bash
CurrentHome=$PWD
Directory=$1
IP=$2
User=$3
#Password=$5

Port=$(grep -oP "rcon-port\K.*" "${Directory}/ServerManager/.env" | sed 's/"//g' | cut -c 2- | grep -o '^\S*')
Password=$(grep -oP "rcon-password\K.*" "${Directory}/ServerManager/.env" | sed 's/"//g' | cut -c 2- | sed 's/\s.*$//')

qote='"'

Playtime=$($CurrentHome/mcrcon -H $IP -P $Port -p $Password '/time')
Playtime=$(echo $Playtime | sed -r "s/\x1B\[[0-9;]*[a-zA-Z]//g")

echo "<div class=PlayTime>	Current map ran for : $Playtime</div></td></tr>"

PlayersOnline=$($CurrentHome/mcrcon -c -H $IP -P $Port -p $Password "/players online")
PlayersOnline=$(echo "$PlayersOnline" | wc -l)
let "PlayersOnline --"

echo "<tr><td><div class=PlayersOnline>Players online : $PlayersOnline</div></td></tr>"

TotalPlayersCount=$($CurrentHome/mcrcon -c -H $IP -P $Port -p $Password "/players")
TotalPlayersCount=$(echo "$TotalPlayersCount" | wc -l)
let "TotalPlayersCount--"

echo "<tr><td><div class=TotalPlayers>Total players : $TotalPlayersCount</div></td></tr>"

echo '<tr><td><table class="table" border="1">
	<tr>
	<th class="col-sm-3">Player Name</th>
	<th class="col-sm-3">PlayTime</th>
	<th class="col-sm-3"></th>
	<th class="col-sm-3"></th>
	</tr>'

mapfile -t PlayerList < <($CurrentHome/mcrcon -c -H $IP -P $Port -p $Password "/players" | sed '1d')
for Player in "${PlayerList[@]}"; do
	PlayerAll=$Player
	Player=$(echo $Player | sed 's/ (online)//g' | tr -d ' ')
	PlayerQote=${qote}$Player${qote}
	PlayerPlayTime=$($CurrentHome/mcrcon -c -H $IP -P $Port -p $Password "/silent-command rcon.print(game.players[$PlayerQote].online_time / 60 / 3600)")
	hour=$(echo $PlayerPlayTime | cut -d. -f1)
	minutes=$(echo $PlayerPlayTime | cut -d "." -f 2)
	minutes=$(echo 0.$minutes)
	minutes=$(echo "$minutes*60" | bc -l)
	minutes=$(echo $minutes | cut -f1 -d".")
	echo "<tr>
			        <td>$PlayerAll</td>
       	 			<td>$hour H $minutes M</td>
       		 		<td>"
	#Kick

	echo -n '<a href="playerManager/kick.php?player='
	echo -n "$Player&serverpath=$Directory&user=$User"
	echo -n '"  onclick="return confirm('
	echo -n "'"
	echo -n 'Are you sure?'
	echo -n "'"
	echo -n ');">Kick</a></td>'
	echo ""
	echo "<td>"

	#Ban

	printf '<script type="text/javascript">'

	printf "\n"

	printf 'var link="'
	printf "playerManager/ban.php?serverpath=${Directory}&user=${User}"
	printf '";'

	printf "\n"

	printf "function editLink${Player}() {"

	printf "\n"

	printf 'var x = prompt("Reason:", "");'

	printf "\n"

	printf 'if (x === "" || x === null) {'

	printf "\n"

	printf 'alert("You entred no reason please retry to ban this player with a reason");'

	printf "\n"

	printf "} else {"

	printf "\n"

	printf 'link+="&reason=" +x;'

	printf "\n"

	printf 'link+="&player='

	printf "${Player}"

	printf '";'

	printf "\n"

	printf 'window.location=link;'

	printf "\n"

	printf "}"

	printf "\n"

	printf "}"

	printf "\n"

	printf '</script>'

	printf '<a onclick="'
	printf "editLink${Player}()"
	printf '" href="#">Ban</a>'

	echo -n '</td>'
	echo ""
	echo "</tr>"

done

echo "</table></td></tr>"
