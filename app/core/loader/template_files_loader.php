<?php
if (is_callable($template_files)) {
    $template_files();
} else {
    $include = true;
    switch ($template_files) {
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
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/../app/core/handlers.php")) {
        require $_SERVER['DOCUMENT_ROOT'] . "/../app/core/handlers.php";
    }
    if (!is_array($template_files)) {
        $template_files = array($template_files);
    }
    if ($include) {
        if ($template_files !== null) {
            for ($i = 0; $i < count($template_files); $i++) {
                $ext = pathinfo($_SERVER['DOCUMENT_ROOT'] . "/../template/" . $template_files[$i])["extension"];
                if ($ext == "php") {
                    require $_SERVER['DOCUMENT_ROOT'] . "/../template/" . $template_files[$i];
                } else {
                    //Need to custom handler
                    $h;
                    if (!empty($handlers[$ext])) {
                        $h = $handlers[$ext];
                    } else {
                        $ext = ucfirst(strtolower($ext));
                        $h = $ext . "Handler";
                    }

                    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/../app/handlers/" . $h . ".php")) {
                        require_once $_SERVER['DOCUMENT_ROOT'] . "/../app/handlers/" . $h . ".php";
                        $controller = Route::getController();
                        if ($controller === null) {
                            $controller = Route::getRouteController();
                        }
                        if ($controller !== null) {
                            if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/../app/controllers/$controller.php")) {
                                require_once $_SERVER['DOCUMENT_ROOT'] . "/../app/controllers/$controller.php";
                            } else {
                                $controller = ucfirst(strtolower($controller)) . "Controller";
                                if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/../app/controllers/$controller.php")) {
                                    require_once $_SERVER['DOCUMENT_ROOT'] . "/../app/controllers/$controller.php";
                                } else {
                                    error($CONFIG["basic"]["APP_ERROR_CODE_OF_MISSING_CONTROLLER_OR_ACTION"]);
                                }
                            }
                            $controller::main($h);
                            $action = Route::getRouteAction();
                            if ($action !== null) {
                                if (method_exists($controller, $action)) {
                                    call_user_func_array(array($controller, $action), getArrayOfParameters(new ReflectionMethod($controller, $action)));
                                } else {
                                    error($CONFIG["basic"]["APP_ERROR_CODE_OF_MISSING_CONTROLLER_OR_ACTION"]);
                                }
                            }
                        }
                        $h::handle($template_files[$i]);
                    }
                }
            }
        }
    }
}
