<?php
require $PATH . "/app/classes/Cache.php";
require $PATH . "/app/classes/Logger.php";

if (empty($ARGS[1])) {
    error_msg("You need specify what should be cleared.");
    info_msg("Available options:");
    info_msg(" • cache");
    info_msg(" • cache old");

} else if($ARGS[1] == "cache") {
    if(!isset($ARGS[2])) {
        Cache::clearAll($PATH);
    } elseif($ARGS[2] == "old") {
        Cache::clearUnused($PATH);
    } else {
        error("Unknown parameter " . $ARGS[2]);
    }
}