<?php
$rustart = getrusage();
$time_start = microtime(true);
session_start();
ob_start();
if(!file_exists($_SERVER['DOCUMENT_ROOT']."/../.config"))
    die("You must setup '.config' file. Visit documentation for more info: <a href='https://panx.eu/docs/'>here</a>");
$CONFIG = parse_ini_file(".config", true);
if($CONFIG["basic"]["APP_DEBUG"] == "1") {
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
    require $_SERVER['DOCUMENT_ROOT'] . "/../app/core/loader/error_handler.php";
    set_error_handler("errorHandler");
}

if (isset($CONFIG["addintional_loader_files_before"]["file"])) {
    foreach ($CONFIG["addintional_loader_files_before"]["file"] as $f) {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/../" . $f)) {
            require $_SERVER['DOCUMENT_ROOT'] . "/../" . $f;
        }
    }
}

function load($class)
{
    if(file_exists($_SERVER['DOCUMENT_ROOT']."/../app/classes/$class.php")) {
        require $_SERVER['DOCUMENT_ROOT']."/../app/classes/$class.php";
    } else if (file_exists($_SERVER['DOCUMENT_ROOT']."/../app/models/$class.php")){
        require_once $_SERVER['DOCUMENT_ROOT'] . "/../app/models/$class.php";
    } else if(file_exists($_SERVER["DOCUMENT_ROOT"] . "/../app/forms/$class.php")) {
        require_once $_SERVER["DOCUMENT_ROOT"] . "/../app/forms/$class.php";
    } else if(file_exists($_SERVER["DOCUMENT_ROOT"] . "/../app/themex/$class.php")) {
        require_once $_SERVER["DOCUMENT_ROOT"] . "/../app/themex/$class.php";
    }
}
if(file_exists($_SERVER["DOCUMENT_ROOT"] . '/../vendor/autoload.php'))
    require_once $_SERVER["DOCUMENT_ROOT"] . '/../vendor/autoload.php';
spl_autoload_register("load", true, true);
if(file_exists($_SERVER["DOCUMENT_ROOT"] . "/../app/classes/R.php")) {
    if(!empty($CONFIG["database"]["DB_HOST"])) {
        load("R.php");
        R::setup( 'mysql:host='.$CONFIG["database"]["DB_HOST"].';dbname=' . $CONFIG["database"]["DB_DATABASE"],
            $CONFIG["database"]["DB_USERNAME"], $CONFIG["database"]["DB_PASSWORD"]);
    }
}
$DI = new DI();
FileStream::init();
if(!empty($CONFIG["database"]["DB_HOST"])) {
    db::connect($CONFIG["database"]["DB_HOST"], $CONFIG["database"]["DB_USERNAME"], $CONFIG["database"]["DB_PASSWORD"], $CONFIG["database"]["DB_DATABASE"]);
    if(($CONFIG["basic"]["APP_DEBUG"] == "1" && $CONFIG["debug"]["DEBUG_VISITS"] == "1") || $CONFIG["debug"]["DEBUG_VISITS_WITHOUT_DEBUG"] == "1") {
        db::query("INSERT INTO debug_visits (`IP`, `USER_USERNAME`, `URL_STRING`) VALUES (?, ?, ?);", array($_SERVER['REMOTE_ADDR'], (isset($_SESSION["username"]) ? $_SESSION["username"] : NULL), "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"));
    }
}
if($CONFIG["basic"]["APP_LOG_ACCESS"] == "1") {
    Logger::log("[{$_SERVER['REMOTE_ADDR']}][".(isset($_SESSION["username"]) ? $_SESSION["username"] : "")."] - http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}". (isset($_SESSION["PREVIOUS_URL"]) ? " from http://{$_SERVER['HTTP_HOST']}{$_SESSION['PREVIOUS_URL']}" : ""), "access.log");
}
load('panx'); //not class

cors();

require_once $_SERVER['DOCUMENT_ROOT']."/../app/handlers/Handler.php";

$route_files = scandir($_SERVER['DOCUMENT_ROOT']."/../routes");
foreach ($route_files as $route_file) {
    if($route_file == "." || $route_file == "..")
        continue;
    if(is_dir($_SERVER['DOCUMENT_ROOT']."/../routes/".$route_file)) {
        continue;
    }
    if(pathinfo($_SERVER['DOCUMENT_ROOT']."/../routes/".$route_file)["extension"] === "php") {
        require($_SERVER['DOCUMENT_ROOT']."/../routes/".$route_file);
    }
}
$request = new Request();
$auth = new Auth();

$auth->loginFromCookies();
$UC = $request->getUrl();

//obtain previous url
$x = $UC->getString();
if (strpos("favicon.ico", $x) === false) {
    //prevent replacing previous url with the same url
    if (!isset($_SESSION["PREVIOUS_URL"])) {
        $_SESSION["PREVIOUS_URL"] = $x;
    } else {
        if ($_SESSION["PREVIOUS_URL_QUEUE"] != $x) {
            $_SESSION["PREVIOUS_URL"] = $_SESSION["PREVIOUS_URL_QUEUE"];
            $_SESSION["PREVIOUS_URL_QUEUE"] = $x;
        }
    }
}

//echo $UC->getString();
$template_files = Route::search($UC->getString());
// code that requires all template files
require $_SERVER['DOCUMENT_ROOT'] . "/../app/core/loader/template_files_loader.php";

$time_end = microtime(true);
$ru = getrusage();


function rutime($ru, $rus, $index)
{
    return ($ru["ru_$index.tv_sec"] * 1000 + intval($ru["ru_$index.tv_usec"] / 1000))
         - ($rus["ru_$index.tv_sec"] * 1000 + intval($rus["ru_$index.tv_usec"] / 1000));
}


if (isset($CONFIG["addintional_loader_files_after"]["file"])) {
    foreach ($CONFIG["addintional_loader_files_after"]["file"] as $f) {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/../" . $f)) {
            require $_SERVER['DOCUMENT_ROOT'] . "/../" . $f;
        }
    }
}


if ($CONFIG["basic"]["APP_DEBUG"] == "1") {
    if($request->getQuery('debug') !== null) {
        echo "<br><hr><br>";
        echo "This process used " . rutime($ru, $rustart, "utime") .
            " ms for its computations<br>\n";
        echo "It spent " . rutime($ru, $rustart, "stime") .
            " ms in system calls<br>\n";
        $execution_time = ($time_end - $time_start) * 1000;

        //execution time of the script
        echo '<b>Total Execution Time:</b> ' . $execution_time . 'ms <br>';
        echo '<b>Previous:</b> ' . ($_SESSION["PREVIOUS_URL"] ?? "null");

    }
}

if ($CONFIG["basic"]["APP_HTML_BEAUTIFY"] == "1") {
    html();
}

if ($CONFIG["basic"]["APP_INFO"] == "1") {
    if($CONFIG["basic"]["APP_INFO_ONLY_HOME"] == "1") {
        if($request->getUrl()->getString() == "/") {
            echo "\n<!-- Powered by panx framework -->";
            echo "\n<!-- https://panx.eu/ -->";
        }
    } else {
        echo "\n<!-- Powered by panx framework -->";
        echo "\n<!-- https://panx.eu/ -->";
    }
}

function getArrayOfParameters($r) {
    $a = array();
    $params = $r->getParameters();
    foreach ($params as $param) {
        if(Route::getValue($param->getName()) === false) {
            error(400);
        }
        array_push($a, Route::getValue($param->getName()));
        
    }
    return $a;
}