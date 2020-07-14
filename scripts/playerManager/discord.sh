#!/bin/bash
message="$1"
url="$2"
## format to parse to curl
## echo Sending message: $message
msg_content=\"$message\"

## discord webhook
curl -H "Content-Type: application/json" -X POST -d "{\"content\": $msg_content}" $url

