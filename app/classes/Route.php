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

declare(strict_types = 1);

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
     * @var array The array of API controllers. [0] => api group, e.g. v1; [1] => Contoller name
     */
    protected static $API_CONTROLLERS = array();
    /**
     * @var array The array of locks (which will limit routes to certain methods).
     */
    protected static $LOCK = array();
    /**
     * @var array The array of API routes. [GROUP] => [
     *  [ 
     *      [0] => ROUTE
     *      [1] => VALUE
     *      [2] => LOCK
     *      [3] => REQUIRED_PARAMETERS
     *      [4] => ACTION
     *  ]
     * ]
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
    public function __construct(string $ROUTE) {
        $this->ROUTE = $ROUTE;
    }

    /**
     * Saves route to $ROUTES.
     * @param string $ROUTE The URI to handle.
     * @param function|string|array $TEMPLATE_FILE Handler of URI, it can be single file, multiple files using array or function.
     * @param array|null $LOCK The array of supported methods for this route. If is set to null, the route will not be locked, so it can be accessed from any http method.
     * @return Route The object representing the route.
     */
    public static function set(string $ROUTE, $TEMPLATE_FILE, ?array $LOCK = null): Route {
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
    public static function apiGroup(string $VERSION, array $ROUTES) {
        if(!isset(self::$API_ROUTES[$VERSION])) {
            self::$API_ROUTES[$VERSION] = [];
        }
        foreach ($ROUTES as $ROUTE) {
            if(!isAssoc($ROUTE)) {
                array_push(self::$API_ROUTES[$VERSION], $ROUTE);
            } else {
                $r = array(
                    $ROUTE["route"],
                    $ROUTE["files"] ?? null,
                    $ROUTE["lock"] ?? null,
                    $ROUTE["required_params"] ?? null,
                    $ROUTE["action"] ?? null
                );
                array_push(self::$API_ROUTES[$VERSION], $r);
            }
            $alias = $ROUTE[5] ?? ($ROUTE["alias"] ?? null);
            if($alias)
                self::setRouteAlias("/api/$VERSION/" . ($ROUTE[0] ?? $ROUTE["route"]), $alias);
        }
    }

    /**
     * Sets controller for single route.
     * @param string $controller The controller name.
     * @return Route Reference to this object.
     */
    public function setController(string $controller): Route {
        self::$CONTROLLERS[$this->ROUTE] = $controller;
        return $this;
    }

    /**
     * Sets controller for API group.
     * @param string $apiGroup The name of API group, e.g. v1
     * @param string $controller The controller name.
     */
    public static function setApiController(string $apiGroup, string $controller) {
        self::$API_CONTROLLERS[$apiGroup] = $controller;
    }

    /**
     * Returns the contoller name for specified route
     * @param string|null $ROUTE
     * @return string|null Returns the controller. If there is no controller sets, try to lookup for default, e.g. requesting /action/edit will include ActionController. If no default controller found, returns null.
     */
    public static function getController(?string $ROUTE = null): ?string {
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
            if($url->getLink()[1] != "api") {
                $default = ucfirst(strtolower($url->getLink()[1])) . "Controller";
                if(file_exists($_SERVER['DOCUMENT_ROOT']."/../app/controllers/$default.php")) {
                    return $default;
                }
                return null;
            } else {
                if(isset($url->getLink()[2])) {
                    if(isset(self::$API_CONTROLLERS[$url->getLink()[2]])) {
                        return self::$API_CONTROLLERS[$url->getLink()[2]]; 
                    }
                }
                return null;
            }
        }
    }

    /**
     * Sets controller for multiple routes.
     * @param array $ROUTES For each route it will sets controllers.
     * @param string $controller The controller name.
     */
    public static function setControllers(array $ROUTES, string $controller) {
        foreach ($ROUTES as $ROUTE) {
            self::$CONTROLLERS[$ROUTE->ROUTE] = $controller;
        }
    }

    /**
     * Sets the alias for Route.
     * @param string $alias
     */
    public function setAlias(string $alias) {
        self::$ALIASES[$alias] = $this->ROUTE;
        return $this;
    }

    /**
     * Gets the name of alias for current route.
     * @return string Alias name.
     */
    public static function getAlias(): string {
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
    public static function setRouteAlias(string $ROUTE, string $alias) {
        self::$ALIASES[$alias] = $ROUTE;
    }

    /**
     * Sets required parameters ($_GET & $_POST) for single route.
     * @param array $get The required $_GET parameters names.
     * @param array $post The required $_POST parameters names. 
     * @param int|string $error The error code when the route does not contain all parameters. By default: 400.
     * @return Route Reference to this object.
     */
    public function setRequiredParameters(array $get = array(), array $post = array(), $error = 400): Route {
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
    public function setMiddleware(array $MIDDLEWARES): Route {
        self::$MIDDLEWARES[$this->ROUTE] = $MIDDLEWARES;
        return $this;
    }

    /**
     * Sets middlewares for routes.
     * @param array $ROUTES For each route it will sets middlewares.
     * @param array $MIDDLEWARES The array of middlewares.
     */
    public static function setMiddlewares(array $ROUTES, array $MIDDLEWARES) {
        foreach ($ROUTES as $ROUTE) {
            self::$MIDDLEWARES[$ROUTE->ROUTE] = $MIDDLEWARES;
        }
    }

    /**
     * Sets middlewares for API group.
     * @param string $API_GROUP The name of API group.
     * @param array $MIDDLEWARES The array containing all middlewares for API group.
     */
    public static function setApiMiddleware(string $API_GROUP, array $MIDDLEWARES) {
        self::$API_MIDDLEWARES[$API_GROUP] = $MIDDLEWARES;
    }

    /**
     * Sets the API endpoint and its handler.
     * @param string $endpoint The API endpoint, e.g. 'v1' or 'v2'.
     * @param object $handler The API handler, new API("v3")
     */
    public static function setApiEndpoint(string $endpoint, $handler) {
        self::$API_ENDPOINTS[$endpoint] = $handler;
    }

    /**
     * Obtain value from URL (Using {paramater}).
     * @param string $VALUE_NAME The parameter's name used in routes.
     * @return string|false The parameter's value or false if the key doesnt exist.
     */
    public static function getValue(string $VALUE_NAME) {
        return isset(self::$VALUES[$VALUE_NAME]) ? self::$VALUES[$VALUE_NAME] : false;

    }

    /**
     * Obtain controller from route (Using <controller>).
     * @return string|null The controller or null if the the Route does not contain one.
     */
    public static function getRouteController(): ?string {
        return self::$ROUTE_CONTROLLER;
    }

    /**
     * Obtain action from route (Using <action>).
     * @return string|null The action or null if the the Route does not contain one.
     */
    public static function getRouteAction(): ?string {
        $url = new URL();
        if(isset(self::$ROUTE_ACTION)) {
            return self::$ROUTE_ACTION;
        }
        if(isset($url->getLink()[1]) && isset($url->getLink()[2]) && $url->getLink()[1] == "api") {
            $g = $url->getLink()[2];
            foreach (self::$API_ROUTES[$g] as $API_ROUTE) {
                $x = new URL("/api/".$g."/".trim($API_ROUTE[0], "/"), false);
                if(count($x->getLink()) > count($url->getLink())) {
                    continue;
                }
                $t = RouteAction::test(array("/api/".$g."/".trim($API_ROUTE[0], "/"), $API_ROUTE[1] ?? null), RouteAction::TYPE_API_ROUTE);
                if($t !== false) {
                    if (!empty($API_ROUTE[4])) {
                        return $API_ROUTE[4];
                    } else {
                        break;
                    }
                }
            }
        }
        return null;
    }

    /**
     * Returns array containing all routes (TYPE, URI/CODE, ACTION, LOCK, MIDDLEWARES).
     * @return array The array of all loaded routes.
     */
    public static function getDataTable(): array {
        $data = array();
        foreach (self::$ROUTES as $ROUTE => $FILE) {
            $alias = "";
            foreach(self::$ALIASES as $ALIAS => $R) {
                if($ROUTE == $R) {
                    $alias = $ALIAS;
                    break;
                }
            }
            array_push($data, 
                array(
                    'TYPE' => 'ROUTE', 
                    'URI/CODE' => $ROUTE, 
                    'ACTION' => !isset($FILE) ? "/" : (is_object($FILE) && ($FILE instanceof Closure) ? "function" : (is_array($FILE) ? "[".implode(", ", $FILE) . "]" :  $FILE)), 
                    'LOCK' => (isset(self::$LOCK[$ROUTE]) ? "[".implode(", ", self::$LOCK[$ROUTE])."]" : "[]"), 
                    'MIDDLEWARES' => (isset(self::$MIDDLEWARES[$ROUTE]) ? "[".implode(", ", self::$MIDDLEWARES[$ROUTE])."]" : "[]"), 
                    'CONTROLLER' => (isset(self::$CONTROLLERS[$ROUTE]) ? self::$CONTROLLERS[$ROUTE] : ''),
                    'API_EP' => "",
                    'R_P_G' => (isset(self::$REQUIRED_PARAMETERS[$ROUTE][0]) ? "[".implode(", ", self::$REQUIRED_PARAMETERS[$ROUTE][0])."]" : "[]"),
                    'R_P_P' => (isset(self::$REQUIRED_PARAMETERS[$ROUTE][1]) ? "[".implode(", ", self::$REQUIRED_PARAMETERS[$ROUTE][1])."]" : "[]"),
                    'R_P_E' => (isset(self::$REQUIRED_PARAMETERS[$ROUTE][2]) ? self::$REQUIRED_PARAMETERS[$ROUTE][2] : ""),
                    'ALIAS' => $alias
                )
            );
        }

        //var_dump(self::$API_ROUTES);
        foreach (self::$API_ROUTES as $API_ROUTE => $API) {
   
            foreach ($API as $route => $value) {
                array_push($data, 
                    array(
                        'TYPE' => 'API', 
                        'URI/CODE' => "/api/" . $API_ROUTE . "/" . $value[0], 
                        'ACTION' => !isset($value[1]) ? "/" : (is_object($value[1]) && ($value[1] instanceof Closure) ? "function" : (is_array($value[1]) ? "[" . implode(", ", $value[1]) . "]" : $value[1])), 
                        'LOCK' => (isset($value[2]) ? "[" . implode(", ", $value[2]) . "]" : "[]"), 
                        'MIDDLEWARES' => (isset(self::$API_MIDDLEWARES[$API_ROUTE]) ? "[" . implode(", ", self::$API_MIDDLEWARES[$API_ROUTE]) . "]" : "[]"), 
                        'CONTROLLER' => (isset(self::$API_CONTROLLERS[$API_ROUTE]) ? self::$API_CONTROLLERS[$API_ROUTE] : ''),
                        'API_EP' => (isset(self::$API_ENDPOINTS[$API_ROUTE]) ? get_class(self::$API_ENDPOINTS[$API_ROUTE]) : ""),
                        'R_P_G' => (isset($value[3][0]) ? "[" . implode(", ", $value[3][0]) . "]" : "[]"),
                        'R_P_P' => (isset($value[3][1]) ? "[" . implode(", ", $value[3][1]) . "]" : "[]"),
                        'R_P_E' => (isset($value[3][2]) ? $value[3][2] : ""),
                        'ALIAS' => ""
                    )
                );

            }
            

        }

        foreach (self::$ERRORS as $ERROR => $ACTION) {
            //'LOCK' => (isset(self::$LOCK[$ROUTE]) ? "[" . implode(", ", self::$LOCK[$ROUTE]) . "]" : "[]"),
            $alias = "";
            foreach(self::$ALIASES as $ALIAS => $R) {
                if($ROUTE == $R) {
                    $alias = $ALIAS;
                    break;
                }
            }
            array_push($data, 
                array(
                    'TYPE' => 'ERROR', 
                    'URI/CODE' => $ERROR, 
                    'ACTION' => (is_object($ACTION) && ($ACTION instanceof Closure) ? "function" : (is_array($ACTION) ? "[" . implode(", ", $ACTION) . "]" : $ACTION)), 
                    'LOCK' => "[]", 
                    'MIDDLEWARES' => "[]", 
                    'CONTROLLER' => "",
                    'API_EP' => "",
                    'R_P_G' => "[]",
                    'R_P_P' => "[]",
                    'R_P_E' => "",
                    'ALIAS' => $alias
                ));
        }


        return $data;
    }
}
