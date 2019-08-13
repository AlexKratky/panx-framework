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
    set_error_handler("errorHandler");

}
function load($class)
{
    if(file_exists($_SERVER['DOCUMENT_ROOT']."/../app/classes/$class.php")) {
        require $_SERVER['DOCUMENT_ROOT']."/../app/classes/$class.php";
    } else if (file_exists($_SERVER['DOCUMENT_ROOT']."/../app/models/$class.php")){
        require_once $_SERVER['DOCUMENT_ROOT'] . "/../app/models/$class.php";
    }
}
spl_autoload_register("load");
FileStream::init();
if(!empty($CONFIG["database"]["DB_HOST"])) {
    db::connect($CONFIG["database"]["DB_HOST"], $CONFIG["database"]["DB_USERNAME"], $CONFIG["database"]["DB_PASSWORD"], $CONFIG["database"]["DB_DATABASE"]);
    if(($CONFIG["basic"]["APP_DEBUG"] == "1" && $CONFIG["debug"]["DEBUG_VISITS"] == "1") || $CONFIG["debug"]["DEBUG_VISITS_WITHOUT_DEBUG"] == "1") {
        db::query("INSERT INTO debug_visits (`IP`, `USER_USERNAME`, `URL_STRING`) VALUES (?, ?, ?);", array($_SERVER['REMOTE_ADDR'], (isset($_SESSION["username"]) ? $_SESSION["username"] : NULL), "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"));
    }
}

load('panx'); //not class


require_once $_SERVER['DOCUMENT_ROOT']."/../app/handlers/Handler.php";

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
$auth = new Auth();
$auth->loginFromCookies();
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
    if(file_exists($_SERVER['DOCUMENT_ROOT']."/../app/core/handlers.php")) {
        require $_SERVER['DOCUMENT_ROOT']."/../app/core/handlers.php";
    }
    if($include) {
        if(!is_array($template_files)) {
            $ext = pathinfo($_SERVER['DOCUMENT_ROOT']."/../template/".$template_files)["extension"];
            if($ext == "php") {
                require $_SERVER['DOCUMENT_ROOT'] . "/../template/" . $template_files;
            } else {
                //Need to custom handler
                if(!empty($handlers[$ext])) {
                    require_once $_SERVER['DOCUMENT_ROOT']."/../app/handlers/$handlers[$ext].php";
                    $controller = Route::getController();
                    if($controller === null) {
                        $controller = Route::getRouteController();
                    }
                    if($controller !== null) {
                        if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/../app/controllers/$controller.php")) {
                            require_once $_SERVER['DOCUMENT_ROOT'] . "/../app/controllers/$controller.php";
                        } else {
                            $controller = ucfirst(strtolower($controller)) . "Controller";
                            if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/../app/controllers/$controller.php")) {
                                require_once $_SERVER['DOCUMENT_ROOT'] . "/../app/controllers/$controller.php";
                            } else {
                                error($CONFIG["basic"]["APP_ERROR_CODE_OF_MISSING_CONTROLLER_OR_ACTION"]);
                            }
                        }
                        $controller::main($handlers[$ext]);
                        $action = Route::getRouteAction();
                        if($action !== null) {
                            if(method_exists($controller, $action)) {
                                call_user_func_array(array($controller, $action), getArrayOfParameters(new ReflectionMethod($controller, $action)));                               
                            } else {
                                error($CONFIG["basic"]["APP_ERROR_CODE_OF_MISSING_CONTROLLER_OR_ACTION"]);
                            }
                        }
                    }
                    $handlers[$ext]::handle($template_files);
                } else {
                    $ext = ucfirst(strtolower($ext));
                    if(file_exists($_SERVER['DOCUMENT_ROOT']."/../app/handlers/".$ext."Handler.php")) {
                        require_once $_SERVER['DOCUMENT_ROOT']."/../app/handlers/".$ext."Handler.php";
                        $ext = $ext."Handler";
                        $controller = Route::getController();
                        if($controller === null) {
                            $controller = Route::getRouteController();
                        }
                        if($controller !== null) {
                            if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/../app/controllers/$controller.php")) {
                                require_once $_SERVER['DOCUMENT_ROOT'] . "/../app/controllers/$controller.php";
                            } else {
                                $controller = ucfirst(strtolower($controller)) . "Controller";
                                if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/../app/controllers/$controller.php")) {
                                    require_once $_SERVER['DOCUMENT_ROOT'] . "/../app/controllers/$controller.php";
                                } else {
                                    error($CONFIG["basic"]["APP_ERROR_CODE_OF_MISSING_CONTROLLER_OR_ACTION"]);
                                }
                            }
                            $controller::main($ext);
                            $action = Route::getRouteAction();
                            if($action !== null) {
                                if(method_exists($controller, $action)) {
                                    call_user_func_array(array($controller, $action), getArrayOfParameters(new ReflectionMethod($controller, $action)));                                   
                                } else {
                                    error($CONFIG["basic"]["APP_ERROR_CODE_OF_MISSING_CONTROLLER_OR_ACTION"]);
                                }
                            }
                        }
                        $ext::handle($template_files);
                    }
                }
            }
        } else {
            if($template_files !== null) {
                for($i = 0; $i < count($template_files); $i++) {
                    $ext = pathinfo($_SERVER['DOCUMENT_ROOT']."/../template/".$template_files[$i])["extension"];
                    if ($ext == "php") {
                        require $_SERVER['DOCUMENT_ROOT']."/../template/".$template_files[$i];
                    } else {
                        //Need to custom handler
                        if(!empty($handlers[$ext])) {
                            require_once $_SERVER['DOCUMENT_ROOT']."/../app/handlers/$handlers[$ext].php";
                            $controller = Route::getController();
                            if ($controller === null) {
                                $controller = Route::getRouteController();
                            }
                            if ($controller !== null) {
                                if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/../app/controllers/$controller.php")) {
                                    require_once $_SERVER['DOCUMENT_ROOT'] . "/../app/controllers/$controller.php";
                                } else {
                                    $controller = ucfirst(strtolower($controller)) . "Controller";
                                    if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/../app/controllers/$controller.php")) {
                                        require_once $_SERVER['DOCUMENT_ROOT'] . "/../app/controllers/$controller.php";
                                    } else {
                                        error($CONFIG["basic"]["APP_ERROR_CODE_OF_MISSING_CONTROLLER_OR_ACTION"]);
                                    }
                                }
                                $controller::main($handlers[$ext]);
                                $action = Route::getRouteAction();
                                if($action !== null) {
                                    if(method_exists($controller, $action)) {
                                           call_user_func_array(array($controller, $action), getArrayOfParameters(new ReflectionMethod($controller, $action)));                                                                          
                                    } else {
                                        error($CONFIG["basic"]["APP_ERROR_CODE_OF_MISSING_CONTROLLER_OR_ACTION"]);
                                    }
                                }
                            }
                            $handlers[$ext]::handle($template_files[$i]);
                        } else {
                            $ext = ucfirst(strtolower($ext));
                            if(file_exists($_SERVER['DOCUMENT_ROOT']."/../app/handlers/".$ext."Handler.php")) {
                                require_once $_SERVER['DOCUMENT_ROOT']."/../app/handlers/".$ext."Handler.php";
                                $ext = $ext."Handler";
                                $controller = Route::getController();
                                if($controller === null) {
                                    $controller = Route::getRouteController();
                                }
                                if ($controller !== null) {
                                    if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/../app/controllers/$controller.php")) {
                                        require_once $_SERVER['DOCUMENT_ROOT'] . "/../app/controllers/$controller.php";
                                    } else {
                                        $controller = ucfirst(strtolower($controller)) . "Controller";
                                        if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/../app/controllers/$controller.php")) {
                                            require_once $_SERVER['DOCUMENT_ROOT'] . "/../app/controllers/$controller.php";
                                        } else {
                                            error($CONFIG["basic"]["APP_ERROR_CODE_OF_MISSING_CONTROLLER_OR_ACTION"]);
                                        }
                                    }
                                    $controller::main($ext);
                                    $action = Route::getRouteAction();
                                    if($action !== null) {
                                        if(method_exists($controller, $action)) {
                                           call_user_func_array(array($controller, $action), getArrayOfParameters(new ReflectionMethod($controller, $action)));                                            
                                        } else {
                                            error($CONFIG["basic"]["APP_ERROR_CODE_OF_MISSING_CONTROLLER_OR_ACTION"]);
                                        }
                                    }
                                }
                                $ext::handle($template_files[$i]);
                            }
                        }
                    }

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

function errorHandler($errno, $errstr, $errfile, $errline) {
    global $CONFIG;
    if (!(error_reporting() & $errno)) {
        return false;
    }

    if ($CONFIG["debug"]["DEBUG_LOG_ERR"] == "1") {
        Logger::log("[$errno] $errstr in $errfile at line $errline", "errors.log");
    }

    switch ($errno) {
        case E_USER_ERROR:
            if(!empty($CONFIG["database"]["DB_HOST"]) && $CONFIG["debug"]["DEBUG_SAVE"] == "1") 
                db::query("INSERT INTO debug_errors (`ERR_LEVEL`, `ERR_MESSAGE`, `ERR_FILE`, `ERR_LINE`) VALUES (?, ?, ?, ?);", array('ERROR', $errstr, $errfile, $errline));
            if($CONFIG["debug"]["DEBUG_PRINT_ERR"] == "1") {
                echo "<b>ERROR</b> [$errno] $errstr<br />\n";
                echo "  Fatal error on line $errline in file <a href='file:///$errfile:$errline'>$errfile</a>";
                echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
                echo "Aborting...<br />\n";
            }
        
            exit(1);
            break;

        case E_USER_WARNING:
            if(!empty($CONFIG["database"]["DB_HOST"]) && $CONFIG["debug"]["DEBUG_SAVE"] == "1")
                db::query("INSERT INTO debug_errors (`ERR_LEVEL`, `ERR_MESSAGE`, `ERR_FILE`, `ERR_LINE`) VALUES (?, ?, ?, ?);", array('WARNING', $errstr, $errfile, $errline));
            if($CONFIG["debug"]["DEBUG_PRINT_ERR"] == "1") {
                echo "<b>WARNING</b> [$errno] $errstr on line $errline in file <a href='file:///$errfile:$errline'>$errfile</a><br />\n";
            }
           
            break;

        case E_USER_NOTICE:
            if(!empty($CONFIG["database"]["DB_HOST"]) && $CONFIG["debug"]["DEBUG_SAVE"] == "1")
                db::query("INSERT INTO debug_errors (`ERR_LEVEL`, `ERR_MESSAGE`, `ERR_FILE`, `ERR_LINE`) VALUES (?, ?, ?, ?);", array('NOTICE', $errstr, $errfile, $errline));
            if($CONFIG["debug"]["DEBUG_PRINT_ERR"] == "1") {
                echo "<b>NOTICE</b> [$errno] $errstr on line $errline in file <a href='file:///$errfile:$errline'>$errfile</a><br />\n";
            }
            

            break;

        default:
            if(!empty($CONFIG["database"]["DB_HOST"]) && $CONFIG["debug"]["DEBUG_SAVE"] == "1")
                db::query("INSERT INTO debug_errors (`ERR_LEVEL`, `ERR_MESSAGE`, `ERR_FILE`, `ERR_LINE`) VALUES (?, ?, ?, ?);", array('OTHER', $errstr, $errfile, $errline));
            if($CONFIG["debug"]["DEBUG_PRINT_ERR"] == "1") {
                echo "Unknown error type: [$errno] $errstr on line $errline in file <a href='file:///$errfile:$errline'>$errfile</a><br />\n";
            }
            

            break;
    }

    /* Don't execute PHP internal error handler */
    return true;
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