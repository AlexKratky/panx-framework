<?php
session_start();
ob_start();
$CONFIG = parse_ini_file(".config", true);
if($CONFIG["basic"]["APP_DEBUG"] == "true") {
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
}
function load($class)
{
    require __DIR__."/app/classes/$class.php";
}
spl_autoload_register("load");
if(!empty($CONFIG["database"]["DB_HOST"]))
    db::connect($CONFIG["database"]["DB_HOST"], $CONFIG["database"]["DB_USERNAME"], $CONFIG["database"]["DB_PASSWORD"], $CONFIG["database"]["DB_DATABASE"]);

load('panx'); //not class

$route_files = scandir(__DIR__."/routes");
foreach ($route_files as $route_file) {
    if($route_file == "." || $route_file == "..")
        continue;
    if(is_dir(__DIR__."/routes/".$route_file)) {
        continue;
    }
    require(__DIR__."/routes/".$route_file);
}

$UC = new URL();
//echo $UC->getString();
$template_files = Route::search($UC->getString());

//is function
if (is_callable($template_files)) {
    $template_files();
}

if($template_files == Route::ERROR_NOT_FOUND)
    $template_files = Route::searchError(Route::ERROR_NOT_FOUND);

if(!is_array($template_files))
    require(__DIR__."/template/".$template_files);
else {
    for($i = 0; $i < count($template_files); $i++) {
        require(__DIR__."/template/".$template_files[$i]);
    }
}