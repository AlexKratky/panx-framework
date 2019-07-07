<?php
require $PATH . "/app/classes/Cache.php";
require $PATH . "/app/classes/Logger.php";

if (empty($ARGS[1])) {
    error_msg("You need specify what should be cleared.");
    info_msg("Available options:");
    info_msg(" • cache");

} else if($ARGS[1] == "cache") {
    Cache::clearAll($PATH);
}