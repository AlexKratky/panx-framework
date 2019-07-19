<?php
if (empty($CONFIG["database"]["DB_HOST"])) {
    die('Need to setup SQL connection in .config');
}
require $PATH.'/app/classes/db.php';
db::connect($CONFIG["database"]["DB_HOST"], $CONFIG["database"]["DB_USERNAME"], $CONFIG["database"]["DB_PASSWORD"], $CONFIG["database"]["DB_DATABASE"]);
db::query(file_get_contents($PATH.'/app/panx-worker/auth-resource/table.sql'), array());
info_msg("The table `users` created");
