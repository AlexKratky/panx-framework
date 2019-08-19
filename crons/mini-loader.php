<?php
if (!file_exists(__DIR__ . "/../.config")) {
    die("You must setup '.config' file. Visit documentation for more info: <a href='https://panx.eu/docs/'>here</a>");
}

$CONFIG = parse_ini_file(__DIR__ . "/../.config", true);
if ($CONFIG["basic"]["APP_DEBUG"] == "1") {
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
}
if(!$IS_REQUIRED_FROM_WEB) {
    function load($class)
    {
        if (file_exists(__DIR__ . "/../app/classes/$class.php")) {
            require __DIR__ . "/../app/classes/$class.php";
        } else if (file_exists(__DIR__ . "/../app/models/$class.php")) {
            require_once __DIR__ . "/../app/models/$class.php";
        }
    }
    spl_autoload_register("load");
    if (!empty($CONFIG["database"]["DB_HOST"])) {
        db::connect($CONFIG["database"]["DB_HOST"], $CONFIG["database"]["DB_USERNAME"], $CONFIG["database"]["DB_PASSWORD"], $CONFIG["database"]["DB_DATABASE"]);
    }

    load('panx'); //not class
}