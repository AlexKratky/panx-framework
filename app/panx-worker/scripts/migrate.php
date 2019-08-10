<?php
$dir = $PATH."/app/migrations/";
if (empty($CONFIG["database"]["DB_HOST"])) {
    die('Need to setup SQL connection in .config');
}
require $PATH . '/app/classes/db.php';
db::connect($CONFIG["database"]["DB_HOST"], $CONFIG["database"]["DB_USERNAME"], $CONFIG["database"]["DB_PASSWORD"], $CONFIG["database"]["DB_DATABASE"]);

require $PATH."/app/classes/TableSchema.php";
if(empty($ARGS[1]) || $ARGS[1] != "drop") {
    $f = scandir($dir);
    foreach ($f as $migration) {
        if($migration == "." || $migration == "..") continue;
        require($dir . $migration);
        $migration = explode("_", $migration, 2)[1];
        $migration = basename($migration, ".php");
        $migration = "table_{$migration}_migration";
        $x = new $migration();
        info_msg($x->create());
    }
} else {
    $f = scandir($dir);
    foreach ($f as $migration) {
        if($migration == "." || $migration == "..") continue;
        require($dir . $migration);
        $migration = explode("_", $migration, 2)[1];
        $migration = basename($migration, ".php");
        $migration = "table_{$migration}_migration";
        $x = new $migration();
        info_msg($x->delete());
    }
}