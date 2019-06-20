<?php
info_msg("You can set host and port by passing arguments. (php panx-worker serve localhost 8080)");
if (empty($ARGS[1])) {
    $host = "localhost";
} else {
    $host = $ARGS[1];
}

if (empty($ARGS[2])) {
    $port = "8000";
} else {
    $port = $ARGS[2];
}

info_msg("Starting server on http://$host:$port");
echo shell_exec("php -S $host:$port -t $PATH/public/");
