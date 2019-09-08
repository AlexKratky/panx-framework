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

class Route extends RouteAction implements RouteErrors {
    /**
     * @var array The array of routes.
     */
    protected static $ROUTES = array();
    /**
     * @var array The array of middlewares.
     */
    protected static $MIDDLEWARES = array();
    /**
     * @var array The array of controllers.
     */
    protected static $CONTROLLERS = array();
    /**
     * @var array The array of locks (which will limit routes to certain methods).
     */
    protected static $LOCK = array();
    /**
     * @var array The array of API routes.
     */
    protected static $API_ROUTES = array();
    /**
     * @var array The array of API middlewares.
     */
    protected static $API_MIDDLEWARES = array();
    /**
     * @var array The API endpoints class
     */
    protected static $API_ENDPOINTS = array();
    /**
     * @var array The array of error routes.
     */
    protected static $ERRORS = array();
    /**
     * @var array The array of URL parameters (Accesible using Route::getValue()).
     */
    protected static $VALUES = array();
    /**
     * @var string <controller>.
     */
    protected static $ROUTE_CONTROLLER = null;
    /**
     * @var string <action>.
     */
    protected static $ROUTE_ACTION = null;
    /**
     * @var array The array of required parameters. [0] => $_GET, [1] => $_POST, [2] => ERROR_CODE.
     */
    protected static $REQUIRED_PARAMETERS = array();
    /**
     * @var array The array of aliases. ALIAS => ROUTE.
     */
    protected static $ALIASES = array();
    /**
     * @var array The info about current Route.
     */
    protected static $CURRENT_ROUTE_INFO;
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
     * @var int Error 500 - Internal server error.
     */
    const ERROR_INTERNAL_SERVER = 500;

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
     * Sets the alias for Route.
     * @param string $alias
     */
    public function setAlias($alias) {
        self::$ALIASES[$alias] = $this->ROUTE;
        return $this;
    }

    /**
     * Gets the name of alias for current route.
     * @return string Alias name.
     */
    public static function getAlias() {
        foreach (self::$ALIASES as $alias => $route) {
            if($route == self::$CURRENT_ROUTE_INFO["route"]) {
                return $alias;
            }
        }
        return null;
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

    /**
     * Sets the API endpoint and its handler.
     * @param string $endpoint The API endpoint, e.g. 'v1' or 'v2'.
     * @param object $handler The API handler, new API("v3")
     */
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