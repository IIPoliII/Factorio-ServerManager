#!/bin/bash
random=$(echo $((RANDOM)))
echo "${random}"
mkdir -p log
rm -rf log/run${random}.log
screen -dm -S runtolog${random} -L -Logfile log/run${random}.log ./factorio-task.sh $1 $2 $3 $4 $5
screen -S runtolog${random} -X colon "logfile flush 0^M"
