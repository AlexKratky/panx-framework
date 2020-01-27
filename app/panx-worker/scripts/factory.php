<?php
if (empty($ARGS[1])) {
    error("You need to specify the factory");
}
$factory = $ARGS[1];
if (empty($CONFIG["database"]["DB_HOST"])) {
    die('Need to setup SQL connection in .config');
}
require $PATH . '/app/classes/db.php';
db::connect($CONFIG["database"]["DB_HOST"], $CONFIG["database"]["DB_USERNAME"], $CONFIG["database"]["DB_PASSWORD"], $CONFIG["database"]["DB_DATABASE"]);


if(!file_exists($PATH."/app/resources/factory/$factory.php")) {
    error("The factory does not exists");
}
require_once $PATH."/app/resources/factory/Factory.php";
require_once $PATH."/app/resources/factory/$factory.php";
$a = $ARGS;
unset($a[1]);
unset($a[0]);
$a = array_values($a);

$x = new $factory();
$x->generate($a);