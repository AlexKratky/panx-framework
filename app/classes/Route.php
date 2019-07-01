<?php
/**
 * @name Route.php
 * @link https://alexkratky.cz                          Author website
 * @link https://panx.eu/docs/                          Documentation
 * @link https://github.com/AlexKratky/panx-framework/  Github Repository
 * @author Alex Kratky <info@alexkratky.cz>
 * @copyright Copyright (c) 2019 Alex Kratky
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @description Router engine. Part of panx-framework.
 */

class Route {
    /**
     * @var array The array of routes.
     */
    private static $ROUTES = array();
    /**
     * @var array The array of middlewares.
     */
    private static $MIDDLEWARES = array();
    /**
     * @var array The array of controllers.
     */
    private static $CONTROLLERS = array();
    /**
     * @var array The array of locks (which will limit routes to certain methods).
     */
    private static $LOCK = array();
    /**
     * @var array The array of API routes.
     */
    private static $API_ROUTES = array();
    /**
     * @var array The array of API middlewares.
     */
    private static $API_MIDDLEWARES = array();
    /**
     * @var array The array of error routes.
     */
    private static $ERRORS = array();
    /**
     * @var array The array of URL parameters (Accesible using Route::getValue()).
     */
    private static $VALUES = array();
    /**
     * @var string The string containing the Route.
     */
    public $ROUTE;

    /**
     * Error class constants
     */
    /**
     * @var int This error will not include any files.
     */
    const DO_NOT_INCLUDE_ANY_FILE = -1;
    /**
     * @var int Middleware error (When middleware decline request).
     */
    const ERROR_MIDDLEWARE = 1;
    /**
     * @var int Error 400 - Bad Request.
     */
    const ERROR_BAD_REQUEST = 400;
    /**
     * @var int Error 403 - Forbidden.
     */
    const ERROR_FORBIDDEN = 403;
    /**
     * @var int Error 404 - Not found.
     */
    const ERROR_NOT_FOUND = 404;

    /**
     * Custom errors
     */
    /**
     * @var string Example custom error.
     */
    const ERROR_NOT_LOGGED_IN = "NOT_LOGGED_IN";

    /**
     * Creates a new instance of Route object containing the route.
     * @param string $ROUTE
     */
    public function __construct($ROUTE) {
        $this->ROUTE = $ROUTE;
    }

    /**
     * Saves route to $ROUTES.
     * @param string $ROUTE The URI to handle.
     * @param function|string|array $TEMPLATE_FILE Handler of URI, it can be single file, multiple files using array or function.
     * @param array|null $LOCK The array of supported methods for this route. If is set to null, the route will not be locked, so it can be accessed from any http method.
     * @return Route The object representing the route.
     */
    public static function set($ROUTE, $TEMPLATE_FILE, $LOCK = null) {
        /**
         * $matches[0][0] : {id}
         * $matches[1][0] : id
         */
        //preg_match_all("/{(.+?)}/", $ROUTE, $matches);
        
        self::$ROUTES[$ROUTE] = $TEMPLATE_FILE;
        if($LOCK !== null) self::$LOCK[$ROUTE] = $LOCK;
        return new Route($ROUTE);
    }

    /**
     * Saves group of api routes to $API_ROUTES.
     * @param string $VERSION The version of API, e.g. 'v1' (/api/v1/).
     * @param array $ROUTES The multi dimensional array of routes.
     */
    public static function apiGroup($VERSION, $ROUTES) {
        self::$API_ROUTES[$VERSION] = $ROUTES;
    }

    /**
     * Returns file(s) or function which responds to $SEARCH_ROUTE.
     * Supports wildcards:
     *  + : Mean one elements, eg: /post/+/edit -> match with /post/1/edit, /post/2/edit ...
     *  * : Mean one or more element, eg: /post/* -> match with /post/1, /post/1/edit ... 
     *  {VARIABLE} : Mean one element (Same as +), but the value will be saved and can be accessed by getValue()
     * @param string $SEARCH_ROUTE The searched route.
     * @return function|array|string Returns file(s) or function which responds to $SEARCH_ROUTE.
     */
    public static function search($SEARCH_ROUTE) {
        $C = new URL();
        $L = $C->getLink();
        if (count($L) > 2 && $L[1] == "api") {
            //e.g. $API_ROUTES["v2"]
            if(isset(self::$API_ROUTES[$L[2]])) {
                if (!empty(self::$API_MIDDLEWARES[$L[2]])) {
                    foreach (self::$API_MIDDLEWARES[$L[2]] as $MIDDLEWARE) {
                        require $_SERVER['DOCUMENT_ROOT'] . "/../app/middlewares/" . $MIDDLEWARE . ".php";
                        if (!$MIDDLEWARE::handle()) {
                            if(method_exists($MIDDLEWARE, "error"))
                                return $MIDDLEWARE::error();
                            return self::ERROR_MIDDLEWARE;
                        }
                    }
                }

                foreach(self::$API_ROUTES[$L[2]] as $API_ROUTE) {
                    //var_dump($API_ROUTE);
                    /*if($C->getString() == "/api/".$L[2]."/".trim($API_ROUTE[0], "/")){
                        return $API_ROUTE[1];
                    }*/
                    $CURRENT = new URL();
                    $x = new URL("/api/".$L[2]."/".trim($API_ROUTE[0], "/"), false);

                    if(count($x->getLink()) > count($CURRENT->getLink())) {
                        continue;
                    }

                    $match = true;
                    $x = $x->getLink();
                    $CURRENT = $CURRENT->getLink();
                    for($i = 0; $i < count($x); $i++) {
                        if($x[$i] == "*") {
                            if (!empty($API_ROUTE[2])) {
                                if (!in_array($_SERVER["REQUEST_METHOD"], $API_ROUTE[2])) {
                                    return self::ERROR_BAD_REQUEST;
                                }
                            }

                            return $API_ROUTE[1];
                        }
                        if($x[$i] == "+") {
                            continue;
                        }
                        preg_match("/{(.+?)}/", $x[$i], $matches);
                        if(count($matches) > 0) {
                            self::$VALUES[$matches[1]] = $CURRENT[$i];
                            continue;
                        }
                        if(!isset($CURRENT[$i]) || $x[$i] != $CURRENT[$i]) {
                            $match = false;

                            break;
                        }
                    }
                    if($match && count($x) == count($CURRENT)) {
                        if (!empty($API_ROUTE[2])) {
                            if (!in_array($_SERVER["REQUEST_METHOD"], $API_ROUTE[2])) {
                                return self::ERROR_BAD_REQUEST;
                            }
                        }
                        return $API_ROUTE[1];
                    }
                }
            }
        }

        foreach (self::$ROUTES as $ROUTE => $VALUE) {
            $CURRENT = new URL();
            $x = new URL($ROUTE."", false);

            if(count($x->getLink()) > count($CURRENT->getLink())) {
                continue;
            }

            $match = true;
            $x = $x->getLink();
            $CURRENT = $CURRENT->getLink();
            for($i = 0; $i < count($x); $i++) {
                if($x[$i] == "*") {
                    if(!empty(self::$LOCK[$ROUTE])) {
                        if(!in_array($_SERVER["REQUEST_METHOD"], self::$LOCK[$ROUTE])) {
                            return self::ERROR_BAD_REQUEST;
                        }
                    }
                    if (!empty(self::$MIDDLEWARES[$ROUTE])) {
                        foreach (self::$MIDDLEWARES[$ROUTE] as $MIDDLEWARE) {
                            require $_SERVER['DOCUMENT_ROOT']."/../app/middlewares/".$MIDDLEWARE.".php";
                            if(!$MIDDLEWARE::handle()) {
                                 if(method_exists($MIDDLEWARE, "error"))
                                    return $MIDDLEWARE::error();
                                return self::ERROR_MIDDLEWARE;
                            }
                        }
                    }
                    return $VALUE;
                }
                if($x[$i] == "+") {
                    continue;
                }
                preg_match("/{(.+?)}/", $x[$i], $matches);
                if(count($matches) > 0) {
                    self::$VALUES[$matches[1]] = $CURRENT[$i];
                    continue;
                }
                if(!isset($CURRENT[$i]) || $x[$i] != $CURRENT[$i]) {
                    $match = false;

                    break;
                }
            }
            if($match && count($x) == count($CURRENT)) {
                if (!empty(self::$LOCK[$ROUTE])) {
                    if (!in_array($_SERVER["REQUEST_METHOD"], self::$LOCK[$ROUTE])) {
                        return self::ERROR_BAD_REQUEST;
                    }
                }
                if (!empty(self::$MIDDLEWARES[$ROUTE])) {
                    foreach (self::$MIDDLEWARES[$ROUTE] as $MIDDLEWARE) {
                        require $_SERVER['DOCUMENT_ROOT']."/../app/middlewares/".$MIDDLEWARE.".php";
                        if(!$MIDDLEWARE::handle()) {
                             if(method_exists($MIDDLEWARE, "error"))
                                return $MIDDLEWARE::error();
                            return self::ERROR_MIDDLEWARE;
                        }
                    }
                }
                return $VALUE;
            }

        }
        return (isset(self::$ROUTES[$SEARCH_ROUTE]) ? self::$ROUTES[$SEARCH_ROUTE] : self::ERROR_NOT_FOUND);
    }

    /**
     * Sets controllers for single route.
     * @param array|string $controller The array of controllers or single string.
     * @return Route Reference to this object.
     */
    public function setController($controller) {
        self::$CONTROLLERS[$this->ROUTE] = $controller;
        return $this;
    }

    /**
     * Sets controllers for multiple routes.
     * @param array $ROUTES For each route it will sets controllers.
     * @param array|string $controller The array of controllers or single string.
     */
    public static function setControllers($ROUTES, $controller) {
        foreach ($ROUTES as $ROUTE) {
            self::$CONTROLLERS[$ROUTE->ROUTE] = $controller;
        }
    }

    /**
     * Sets route for ERROR CODE
     * @param string|int $ERROR_CODE The code of error. Can be int (e.g. 404) or string (e.g. "NOT_FOUND").
     * @param string|array|function The handler of error, it can be single file, multiple files using array or function. You should use single file only.
     */
    public static function setError($ERROR_CODE, $ERROR_FILE) {
        self::$ERRORS[$ERROR_CODE] = $ERROR_FILE;
    }

    /**
     * Returns file which repsonds to error code
     * @param string|int $ERROR_CODE The code of error. Can be int (e.g. 404) or string (e.g. "NOT_FOUND").
     * @return string|array|function The handler of error, it can be single file, multiple files using array or function. You should use single file only.
     */
    public static function searchError($ERROR_CODE) {
        return self::$ERRORS[$ERROR_CODE];
    }

    /**
     * Sets middlewares for single route.
     * @param array $MIDDLEWARES The array of middlewares.
     * @return Route Reference to this object.
     */
    public function setMiddleware($MIDDLEWARES) {
        self::$MIDDLEWARES[$this->ROUTE] = $MIDDLEWARES;
        return $this;
    }

    /**
     * Sets middlewares for routes.
     * @param array $ROUTES For each route it will sets middlewares.
     * @param array $MIDDLEWARES The array of middlewares.
     */
    public static function setMiddlewares($ROUTES, $MIDDLEWARES) {
        foreach ($ROUTES as $ROUTE) {
            self::$MIDDLEWARES[$ROUTE->ROUTE] = $MIDDLEWARES;
        }
    }

    /**
     * Sets middlewares for API group.
     * @param string $API_GROUP The name of API group.
     * @param array $MIDDLEWARES The array containing all middlewares for API group.
     */
    public static function setApiMiddleware($API_GROUP, $MIDDLEWARES) {
        self::$API_MIDDLEWARES[$API_GROUP] = $MIDDLEWARES;
    }

    /**
     * Obtain value from URL (Using {paramater}).
     * @param string $VALUE_NAME The parameter's name used in routes.
     * @return string The parameter's value.
     */
    public static function getValue($VALUE_NAME) {
        return self::$VALUES[$VALUE_NAME];

    }

    /**
     * Returns array containing all routes (TYPE, URI/CODE, ACTION, LOCK, MIDDLEWARES).
     * @return array The array of all loaded routes.
     */
    public static function getDataTable() {
        $data = array();
        foreach (self::$ROUTES as $ROUTE => $FILE) {
            array_push($data, array('TYPE' => 'ROUTE', 'URI/CODE' => $ROUTE, 'ACTION' => (is_object($FILE) && ($FILE instanceof Closure) ? "function" : (is_array($FILE) ? "[".implode(", ", $FILE) . "]" :  $FILE)), 'LOCK' => (isset(self::$LOCK[$ROUTE]) ? "[".implode(", ", self::$LOCK[$ROUTE])."]" : "[]"), 'MIDDLEWARES' => (isset(self::$MIDDLEWARES[$ROUTE]) ? "[".implode(", ", self::$MIDDLEWARES[$ROUTE])."]" : "[]"), 'CONTROLLERS' => (isset(self::$CONTROLLERS[$ROUTE]) ? (is_array(self::$CONTROLLERS[$ROUTE]) ? "[".implode(", ", self::$CONTROLLERS[$ROUTE])."]" : self::$CONTROLLERS[$ROUTE]) : '[]')));
        }

        //var_dump(self::$API_ROUTES);
        foreach (self::$API_ROUTES as $API_ROUTE => $API) {
   
            foreach ($API as $route => $value) {
                array_push($data, array('TYPE' => 'API', 'URI/CODE' => "/api/" . $API_ROUTE . "/" . $value[0], 'ACTION' => (is_object($value[1]) && ($value[1] instanceof Closure) ? "function" : (is_array($value[1]) ? "[" . implode(", ", $value[1]) . "]" : $value[1])), 'LOCK' => (isset($value[2]) ? "[" . implode(", ", $value[2]) . "]" : "[]"), 'MIDDLEWARES' => "[]", 'CONTROLLERS' => "[]"));

            }
            

        }

        foreach (self::$ERRORS as $ERROR => $ACTION) {
            //'LOCK' => (isset(self::$LOCK[$ROUTE]) ? "[" . implode(", ", self::$LOCK[$ROUTE]) . "]" : "[]"),
            array_push($data, array('TYPE' => 'ERROR', 'URI/CODE' => $ERROR, 'ACTION' => (is_object($ACTION) && ($ACTION instanceof Closure) ? "function" : (is_array($ACTION) ? "[" . implode(", ", $ACTION) . "]" : $ACTION)), 'LOCK' => "[]", 'MIDDLEWARES' => "[]", 'CONTROLLERS' => "[]"));
        }


        return $data;
    }
}