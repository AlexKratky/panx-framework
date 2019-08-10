<?php
if (empty($CONFIG["database"]["DB_HOST"])) {
    die('Need to setup SQL connection in .config');
}
require $PATH . '/app/classes/db.php';
db::connect($CONFIG["database"]["DB_HOST"], $CONFIG["database"]["DB_USERNAME"], $CONFIG["database"]["DB_PASSWORD"], $CONFIG["database"]["DB_DATABASE"]);
db::query(file_get_contents($PATH . '/app/panx-worker/debug-resource/table_errors.sql'), array());
db::query(file_get_contents($PATH . '/app/panx-worker/debug-resource/table_visits.sql'), array());
info_msg("The tables `debug_errors` and `debug_visits` created");
