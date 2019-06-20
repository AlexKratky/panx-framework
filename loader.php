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
}
function load($class)
{
    require $_SERVER['DOCUMENT_ROOT']."/../app/classes/$class.php";
}
spl_autoload_register("load");
if(!empty($CONFIG["database"]["DB_HOST"]))
    db::connect($CONFIG["database"]["DB_HOST"], $CONFIG["database"]["DB_USERNAME"], $CONFIG["database"]["DB_PASSWORD"], $CONFIG["database"]["DB_DATABASE"]);

load('panx'); //not class

$route_files = scandir($_SERVER['DOCUMENT_ROOT']."/../routes");
foreach ($route_files as $route_file) {
    if($route_file == "." || $route_file == "..")
        continue;
    if(is_dir($_SERVER['DOCUMENT_ROOT']."/../routes/".$route_file)) {
        continue;
    }
    require($_SERVER['DOCUMENT_ROOT']."/../routes/".$route_file);
}
$request = new Request();

$UC = $request->getUrl();
//echo $UC->getString();
$template_files = Route::search($UC->getString());

//is function
if (is_callable($template_files)) {
    $template_files();
} else {
    $include = true;
    switch($template_files){
        case Route::DO_NOT_INCLUDE_ANY_FILE:
            $include = false;
            break;
        case Route::ERROR_MIDDLEWARE:
            $template_files = Route::searchError(Route::ERROR_MIDDLEWARE);
            break;
        case Route::ERROR_NOT_FOUND:
            $template_files = Route::searchError(Route::ERROR_NOT_FOUND);
            break;
        case Route::ERROR_BAD_REQUEST:
            $template_files = Route::searchError(Route::ERROR_BAD_REQUEST);
            break;
        case Route::ERROR_FORBIDDEN:
            $template_files = Route::searchError(Route::ERROR_FORBIDDEN);
            break;
    }
    if($include) {
        if(!is_array($template_files))
            require($_SERVER['DOCUMENT_ROOT']."/../template/".$template_files);
        else {
            if($template_files !== null) {
                for($i = 0; $i < count($template_files); $i++) {
                    require($_SERVER['DOCUMENT_ROOT']."/../template/".$template_files[$i]);
                }
            }
        }
    }
}
$time_end = microtime(true);
$ru = getrusage();


function rutime($ru, $rus, $index)
{
    return ($ru["ru_$index.tv_sec"] * 1000 + intval($ru["ru_$index.tv_usec"] / 1000))
         - ($rus["ru_$index.tv_sec"] * 1000 + intval($rus["ru_$index.tv_usec"] / 1000));
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

    }
}

if ($CONFIG["basic"]["APP_HTML_BEAUTIFY"] == "1") {
    html();
}

if ($CONFIG["basic"]["APP_INFO"] == "1") {
    echo "\n<!-- Powered by panx framework -->";
    echo "\n<!-- https://panx.eu/ -->";
}

