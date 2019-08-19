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
     * @var array The API endpoints class
     */
    private static $API_ENDPOINTS = array();
    /**
     * @var array The array of error routes.
     */
    private static $ERRORS = array();
    /**
     * @var array The array of URL parameters (Accesible using Route::getValue()).
     */
    private static $VALUES = array();
    /**
     * @var string <controller>.
     */
    private static $ROUTE_CONTROLLER = null;
    /**
     * @var string <action>.
     */
    private static $ROUTE_ACTION = null;
    /**
     * @var array The array of required parameters. [0] => $_GET, [1] => $_POST, [2] => ERROR_CODE.
     */
    private static $REQUIRED_PARAMETERS = array();
    /**
     * @var array The array of aliases. ALIAS => ROUTE.
     */
    private static $ALIASES = array();
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
        if(isset($GLOBALS["CONFIG"]["basic"]["APP_ROUTES_CASE_SENSITIVE"]) && $GLOBALS["CONFIG"]["basic"]["APP_ROUTES_CASE_SENSITIVE"] !== "1") {
            $ROUTE = strtolower($ROUTE);
        }
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

                if(!empty(self::$API_ENDPOINTS[$L[2]])) {
                    if(!self::$API_ENDPOINTS[$L[2]]->request($C)) {
                        echo json(self::$API_ENDPOINTS[$L[2]]->error());
                        exit();
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
                        preg_match("/{(.+?)\s?(\[(.+?)\])?}/", $x[$i], $matches);

                        if(count($matches) > 0) {
                            if(isset($matches[3])) {
                                preg_match("/".$matches[3]."/", $CURRENT[$i], $m);
                                if(count($m) > 0) {
                                    self::$VALUES[$matches[1]] = $CURRENT[$i];
                                    continue;
                                }
                            } else {
                                self::$VALUES[$matches[1]] = $CURRENT[$i];
                                continue;
                            }
                        }
                        if(strtolower($x[$i]) == "<controller>") {
                            if(ctype_alnum($CURRENT[$i])) {      
                                self::$ROUTE_CONTROLLER = $CURRENT[$i];
                                continue;
                            }
                        }
                        if(strtolower($x[$i]) == "<action>") {
                            if(ctype_alnum($CURRENT[$i])) {                            
                                self::$ROUTE_ACTION = $CURRENT[$i];
                                continue;
                            }
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
            if (isset($GLOBALS["CONFIG"]["basic"]["APP_ROUTES_CASE_SENSITIVE"]) && $GLOBALS["CONFIG"]["basic"]["APP_ROUTES_CASE_SENSITIVE"] !== "1") {
                $CURRENT = new URL(strtolower($_SERVER["REQUEST_URI"]));
            }

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
                preg_match("/{(.+?)\s?(\[(.+?)\])?}/", $x[$i], $matches);

                if(count($matches) > 0) {
                    if(isset($matches[3])) {
                        preg_match("/".$matches[3]."/", $CURRENT[$i], $m);
                        if(count($m) > 0) {
                            self::$VALUES[$matches[1]] = $CURRENT[$i];
                            continue;
                        }
                    } else {
                        self::$VALUES[$matches[1]] = $CURRENT[$i];
                        continue;
                    }
                }
                if(strtolower($x[$i]) == "<controller>") {
                    if(ctype_alnum($CURRENT[$i])) {
                        self::$ROUTE_CONTROLLER = $CURRENT[$i];
                        continue;
                    }
                }
                if(strtolower($x[$i]) == "<action>") {
                    if(ctype_alnum($CURRENT[$i])) {
                        self::$ROUTE_ACTION = $CURRENT[$i];
                        continue;
                    }
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
                if (!empty(self::$REQUIRED_PARAMETERS[$ROUTE])) {
                    foreach (self::$REQUIRED_PARAMETERS[$ROUTE][0] as $get) {
                        if(empty($_GET[$get])) {
                            return self::$REQUIRED_PARAMETERS[$ROUTE][2];
                        }
                    }
                    foreach (self::$REQUIRED_PARAMETERS[$ROUTE][1] as $post) {
                        if(empty($_POST[$post])) {
                            return self::$REQUIRED_PARAMETERS[$ROUTE][2];
                        }
                    }
                }

                return $VALUE;
            }

        }
        return (isset(self::$ROUTES[$SEARCH_ROUTE]) ? self::$ROUTES[$SEARCH_ROUTE] : self::ERROR_NOT_FOUND);
    }

    /**
     * The function will return template file(s)/function without limitation of middlewares etc.
     */
    public static function searchWithNoLimits() {
        $C = new URL();
        $L = $C->getLink();
        if (count($L) > 2 && $L[1] == "api") {
            //e.g. $API_ROUTES["v2"]
            if(isset(self::$API_ROUTES[$L[2]])) {
                foreach(self::$API_ROUTES[$L[2]] as $API_ROUTE) {
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
                        preg_match("/{(.+?)\s?(\[(.+?)\])?}/", $x[$i], $matches);

                        if(count($matches) > 0) {
                            if(isset($matches[3])) {
                                preg_match("/".$matches[3]."/", $CURRENT[$i], $m);
                                if(count($m) > 0) {
                                    //self::$VALUES[$matches[1]] = $CURRENT[$i];
                                    continue;
                                }
                            } else {
                                //self::$VALUES[$matches[1]] = $CURRENT[$i];
                                continue;
                            }
                        }
                        if(strtolower($x[$i]) == "<controller>") {
                            if(ctype_alnum($CURRENT[$i])) {
                                continue;
                            }
                        }
                        if(strtolower($x[$i]) == "<action>") {
                            if(ctype_alnum($CURRENT[$i])) {                            
                                continue;
                            }
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
    }

    /**
     * Convert the URI to route
     * For example, if you set route '/example/+/edit' in route.php and you pass the URI to this function (e.g., /example/13/edit), it will returns the route with wildcard -> '/example/+/edit'
     * @param string|null $route The URI, if sets to null, then will use the current URI.
     * @return string The route coresponding to URI.
     */
    public static function convertRoute($route = null) {
        foreach (self::$ROUTES as $ROUTE => $VALUE) {
            $CURRENT = new URL($route);
            if (isset($GLOBALS["CONFIG"]["basic"]["APP_ROUTES_CASE_SENSITIVE"]) && $GLOBALS["CONFIG"]["basic"]["APP_ROUTES_CASE_SENSITIVE"] !== "1") {
                $ROUTE = strtolower($ROUTE);
            } else {
                
            }

            $x = new URL($ROUTE."", false);

            if(count($x->getLink()) > count($CURRENT->getLink())) {
                continue;
            }

            $match = true;
            $x = $x->getLink();
            $CURRENT_LINK = $CURRENT->getLink();
            for($i = 0; $i < count($x); $i++) {
                if($x[$i] == "*") {
                    return $ROUTE;
                }
                if($x[$i] == "+") {
                    continue;
                }
                preg_match("/{(.+?)\s?(\[(.+?)\])?}/", $x[$i], $matches);

                if(count($matches) > 0) {
                    if(isset($matches[3])) {
                        preg_match("/".$matches[3]."/", $CURRENT_LINK[$i], $m);
                        if(count($m) > 0) {
                            //self::$VALUES[$matches[1]] = $CURRENT[$i];
                            continue;
                        }
                    } else {
                        //self::$VALUES[$matches[1]] = $CURRENT[$i];
                        continue;
                    }
                }
                if(strtolower($x[$i]) == "<controller>") {
                    if(ctype_alnum($CURRENT_LINK[$i])) {                    
                        continue;
                    }
                }
                if(strtolower($x[$i]) == "<action>") {
                    if(ctype_alnum($CURRENT_LINK[$i])) {                    
                        continue;
                    }
                }
                if(!isset($CURRENT_LINK[$i]) || $x[$i] != $CURRENT_LINK[$i]) {
                    $match = false;

                    break;
                }
            }
            if($match && count($x) == count($CURRENT_LINK)) {
                return $ROUTE;
            }

        }
        return $CURRENT->getString();
    }

    /**
     * Sets controller for single route.
     * @param string $controller The controller name.
     * @return Route Reference to this object.
     */
    public function setController($controller) {
        self::$CONTROLLERS[$this->ROUTE] = $controller;
        return $this;
    }

    /**
     * Returns the contoller name for specified route
     * @param string|null $ROUTE
     * @return string|null Returns the controller. If there is no controller sets, try to lookup for default, e.g. requesting /action/edit will include ActionController. If no default controller found, returns null.
     */
    public static function getController($ROUTE = null) {
        if($ROUTE === null) {
            $ROUTE = $GLOBALS["request"]->getUrl()->getString();
            $ROUTE = self::convertRoute($ROUTE);
        }

        if(isset(self::$CONTROLLERS[$ROUTE])) {
            return self::$CONTROLLERS[$ROUTE]; 
        } else {
            //try to lookup for default controller or return empty array
            $url = new URL($ROUTE);
            if(!isset($url->getLink()[1])) {
                return null;
            }
            $default = ucfirst(strtolower($url->getLink()[1])) . "Controller";
            if(file_exists($_SERVER['DOCUMENT_ROOT']."/../app/controllers/$default.php")) {
                return $default;
            }
            return null;
        }
    }

    /**
     * Sets controller for multiple routes.
     * @param array $ROUTES For each route it will sets controllers.
     * @param string $controller The controller name.
     */
    public static function setControllers($ROUTES, $controller) {
        foreach ($ROUTES as $ROUTE) {
            self::$CONTROLLERS[$ROUTE->ROUTE] = $controller;
        }
    }

    /**
     * Returns the URL from Route alias.
     * @param string $alias The alias of the route.
     * @param string $parmas The Route parameters. Write like this param1:param2:[1,2,3]:comment=true. The 'name=value' syntax is just for you, in route will be used just value.
     * @param string $get The GET parameters (eg. ?x=x). Write like this x=true:y=false:debug => ?x=true&y=false&debug
     * @return string url.
     */
    public static function alias($alias, $params = null, $get = null) {
        if(!isset(self::$ALIASES[$alias])) return null;
        $r = new URL(self::$ALIASES[$alias], false);
        $l = $r->getLink();
        $params = explode(":", $params);
        $params_index = 0;
        $link = "/";
        //params
        for($i = 1; $i < count($l); $i++) {
            if($l[$i] == "<controller>" || $l[$i] == "<action>" || $l[$i] == "+") {

                (isset($params[$params_index]) ? : error(400));
                $link .= (strpos($params[$params_index], "=") !== false ? explode("=", $params[$params_index], 2)[1] : $params[$params_index]) . "/";
                $params_index++;
                continue;
            }
            //parse array []
            if($l[$i] == "*") {
                (isset($params[$params_index]) ?: error(400));
                $x = (strpos($params[$params_index], "=") !== false ? explode("=", $params[$params_index], 2)[1] : $params[$params_index]);
                $x = trim($x, "[]");
                $x = preg_replace('/\s+/', '', $x);
                $x = explode(",",$x);
                foreach ($x as $v) {
                    $link .= $v."/";
                }
                $params_index++;
                continue;
            }
            //regex
            preg_match("/{(.+?)\s?(\[(.+?)\])?}/", $l[$i], $matches);

            if(count($matches) > 0) {
                (isset($params[$params_index]) ? : error(400));
                $link .= (strpos($params[$params_index], "=") !== false ? explode("=", $params[$params_index], 2)[1] : $params[$params_index]) . "/";
                $params_index++;
                continue;
            } else {
                $link .= $l[$i] . "/";
                continue;
            }
        }
        //get params
        if($get !== null) {
            $link .= "?";
            $add_ampersand = false;
            $get = explode(":", $get);
            foreach($get as $get_param) {
                $link .= ($add_ampersand ? "&" : "") . $get_param;
                $add_ampersand = true;
            }
        }
        return $link;
    }

    /**
     * Sets the alias for Route.
     * @param string $alias
     */
    public function setAlias($alias) {
        self::$ALIASES[$alias] = $this->ROUTE;
        return $this;
    }

    /**
     * Sets the alias for Route.
     * @param string $ROUTE The route.
     * @param string $alias
     */
    public static function setRouteAlias($ROUTE, $alias) {
        self::$ALIASES[$alias] = $ROUTE;
    }

    /**
     * Sets required parameters ($_GET & $_POST) for single route.
     * @param array $get The required $_GET parameters names.
     * @param array $post The required $_POST parameters names. 
     * @param int|string $error The error code when the route does not contain all parameters. By default: 400.
     * @return Route Reference to this object.
     */
    public function setRequiredParameters($get = array(), $post = array(), $error = 400) {
        self::$REQUIRED_PARAMETERS[$this->ROUTE] = array($get, $post, $error);
        return $this;
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

    public static function setApiEndpoint($endpoint, $handler) {
        self::$API_ENDPOINTS[$endpoint] = $handler;
    }

    /**
     * Obtain value from URL (Using {paramater}).
     * @param string $VALUE_NAME The parameter's name used in routes.
     * @return string|false The parameter's value or false if the key doesnt exist.
     */
    public static function getValue($VALUE_NAME) {
        return isset(self::$VALUES[$VALUE_NAME]) ? self::$VALUES[$VALUE_NAME] : false;

    }

    /**
     * Obtain controller from route (Using <controller>).
     * @return string|null The controller or null if the the Route does not contain one.
     */
    public static function getRouteController() {
        return self::$ROUTE_CONTROLLER;
    }

    /**
     * Obtain action from route (Using <action>).
     * @return string|null The action or null if the the Route does not contain one.
     */
    public static function getRouteAction() {
        return self::$ROUTE_ACTION;
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
                array_push($data, array('TYPE' => 'API', 'URI/CODE' => "/api/" . $API_ROUTE . "/" . $value[0], 'ACTION' => (is_object($value[1]) && ($value[1] instanceof Closure) ? "function" : (is_array($value[1]) ? "[" . implode(", ", $value[1]) . "]" : $value[1])), 'LOCK' => (isset($value[2]) ? "[" . implode(", ", $value[2]) . "]" : "[]"), 'MIDDLEWARES' => (isset(self::$API_MIDDLEWARES[$API_ROUTE]) ? "[" . implode(", ", self::$API_MIDDLEWARES[$API_ROUTE]) . "]" : "[]"), 'CONTROLLERS' => "[]"));

            }
            

        }

        foreach (self::$ERRORS as $ERROR => $ACTION) {
            //'LOCK' => (isset(self::$LOCK[$ROUTE]) ? "[" . implode(", ", self::$LOCK[$ROUTE]) . "]" : "[]"),
            array_push($data, array('TYPE' => 'ERROR', 'URI/CODE' => $ERROR, 'ACTION' => (is_object($ACTION) && ($ACTION instanceof Closure) ? "function" : (is_array($ACTION) ? "[" . implode(", ", $ACTION) . "]" : $ACTION)), 'LOCK' => "[]", 'MIDDLEWARES' => "[]", 'CONTROLLERS' => "[]"));
        }


        return $data;
    }
}