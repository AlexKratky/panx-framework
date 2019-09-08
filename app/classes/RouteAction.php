<?php
/**
 * @name RouteAction.php
 * @link https://alexkratky.cz                          Author website
 * @link https://panx.eu/docs/                          Documentation
 * @link https://github.com/AlexKratky/panx-framework/  Github Repository
 * @author Alex Kratky <info@alexkratky.cz>
 * @copyright Copyright (c) 2019 Alex Kratky
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @description Contains function to work with routes. Part of panx-framework.
 */

abstract class RouteAction {

    /**
     * @var string
     */
    public const TYPE_API_ROUTE = "API_ROUTE";
    /**
     * @var string
     */
    public const TYPE_STANDARD_ROUTE = "STANDARD_ROUTE";
    /**
     * @var string Used in test(); do not save any values etc.
     */
    public const TYPE_NO_LIMIT = "NO_LIMIT";
    /**
     * The regex for testing paramaters, e.g. '{NAME#Validator::validateUsername}
     */
    private const URL_REGEX = "/{(.+?)\s?((\[(.+?)\])|(\#(.+?)))?}/";


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
            if(isset(Route::$API_ROUTES[$L[2]])) {
                if (!empty(Route::$API_MIDDLEWARES[$L[2]])) {
                    foreach (Route::$API_MIDDLEWARES[$L[2]] as $MIDDLEWARE) {
                        require $_SERVER['DOCUMENT_ROOT'] . "/../app/middlewares/" . $MIDDLEWARE . ".php";
                        if (!$MIDDLEWARE::handle()) {
                            if(method_exists($MIDDLEWARE, "error"))
                                return $MIDDLEWARE::error();
                            return Route::ERROR_MIDDLEWARE;
                        }
                    }
                }

                if(!empty(Route::$API_ENDPOINTS[$L[2]])) {
                    if(!Route::$API_ENDPOINTS[$L[2]]->request($C)) {
                        echo json(Route::$API_ENDPOINTS[$L[2]]->error());
                        exit();
                    }
                }

                foreach(Route::$API_ROUTES[$L[2]] as $API_ROUTE) {
                    $x = new URL("/api/".$L[2]."/".trim($API_ROUTE[0], "/"), false);
                    if(count($x->getLink()) > count($GLOBALS["request"]->getUrl()->getLink())) {
                        continue;
                    }

                    $t = self::test(array("/api/".$L[2]."/".trim($API_ROUTE[0], "/"), $API_ROUTE[1]), self::TYPE_API_ROUTE);
                    if($t !== false) {
                        if (!empty($API_ROUTE[2])) {
                            if (!in_array($_SERVER["REQUEST_METHOD"], $API_ROUTE[2])) {
                                return Route::ERROR_BAD_REQUEST;
                            }
                        }
                        return $API_ROUTE[1];
                    }
                }
            }
        }

        foreach (Route::$ROUTES as $ROUTE => $VALUE) {
            $CURRENT = null;
            if (isset($GLOBALS["CONFIG"]["basic"]["APP_ROUTES_CASE_SENSITIVE"]) && $GLOBALS["CONFIG"]["basic"]["APP_ROUTES_CASE_SENSITIVE"] !== "1") {
                $CURRENT = strtolower($_SERVER["REQUEST_URI"]);
            }

            $x = new URL($ROUTE . "", false);

            if(count($x->getLink()) > count($GLOBALS["request"]->getUrl()->getLink())) {
                continue;
            }

            $t = self::test(array($ROUTE."", $VALUE), self::TYPE_STANDARD_ROUTE, $CURRENT);
            if($t !== false) {
                if (!empty(Route::$LOCK[$ROUTE])) {
                    if (!in_array($_SERVER["REQUEST_METHOD"], Route::$LOCK[$ROUTE])) {
                        return Route::ERROR_BAD_REQUEST;
                    }
                }
                if (!empty(Route::$MIDDLEWARES[$ROUTE])) {
                    foreach (Route::$MIDDLEWARES[$ROUTE] as $MIDDLEWARE) {
                        require $_SERVER['DOCUMENT_ROOT'] . "/../app/middlewares/" . $MIDDLEWARE . ".php";
                        if (!$MIDDLEWARE::handle()) {
                            if (method_exists($MIDDLEWARE, "error")) {
                                return $MIDDLEWARE::error();
                            }

                            return Route::ERROR_MIDDLEWARE;
                        }
                    }
                }
                if (!empty(Route::$REQUIRED_PARAMETERS[$ROUTE])) {
                    foreach (Route::$REQUIRED_PARAMETERS[$ROUTE][0] as $get) {
                        if (empty($_GET[$get])) {
                            return Route::$REQUIRED_PARAMETERS[$ROUTE][2];
                        }
                    }
                    foreach (Route::$REQUIRED_PARAMETERS[$ROUTE][1] as $post) {
                        if (empty($_POST[$post])) {
                            return Route::$REQUIRED_PARAMETERS[$ROUTE][2];
                        }
                    }
                }

                return $VALUE;
            }
            

        }
        return (isset(Route::$ROUTES[$SEARCH_ROUTE]) ? Route::$ROUTES[$SEARCH_ROUTE] : Route::ERROR_NOT_FOUND);
    }

    /**
     * The function will return template file(s)/function without limitation of middlewares etc.
     */
    public static function searchWithNoLimits() {
        $C = new URL();
        $L = $C->getLink();
        if (count($L) > 2 && $L[1] == "api") {
            //e.g. $API_ROUTES["v2"]
            if(isset(Route::$API_ROUTES[$L[2]])) {
                foreach(Route::$API_ROUTES[$L[2]] as $API_ROUTE) {
                    $x = new URL("/api/".$L[2]."/".trim($API_ROUTE[0], "/"), false);

                    if(count($x->getLink()) > count($C->getLink())) {
                        continue;
                    }

                    $t = self::test(array("/api/".$L[2]."/".trim($API_ROUTE[0], "/"), $API_ROUTE[1]), self::TYPE_NO_LIMIT);
                    if($t !== false) {
                        /*if (!empty($API_ROUTE[2])) {
                            if (!in_array($_SERVER["REQUEST_METHOD"], $API_ROUTE[2])) {
                                return Route::ERROR_BAD_REQUEST;
                            }
                        }*/
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
        foreach (Route::$ROUTES as $ROUTE => $VALUE) {
            $CURRENT = new URL($route);
            if (isset($GLOBALS["CONFIG"]["basic"]["APP_ROUTES_CASE_SENSITIVE"]) && $GLOBALS["CONFIG"]["basic"]["APP_ROUTES_CASE_SENSITIVE"] !== "1") {
                $ROUTE = strtolower($ROUTE);
            } else {
                
            }

            $x = new URL($ROUTE."", false);

            if(count($x->getLink()) > count($CURRENT->getLink())) {
                continue;
            }

            $t = self::test(array($ROUTE."", $VALUE), self::TYPE_NO_LIMIT, $route);
            if($t !== false) {
                return $ROUTE;
            }
            

        }
        return $CURRENT->getString();
    }

    /**
     * Returns the URL from Route alias.
     * @param string $alias The alias of the route.
     * @param string $parmas The Route parameters. Write like this param1:param2:[1,2,3]:comment=true. The 'name=value' syntax is just for you, in route will be used just value.
     * @param string $get The GET parameters (eg. ?x=x). Write like this x=true:y=false:debug => ?x=true&y=false&debug
     * @return string url.
     */
    public static function alias($alias, $params = null, $get = null) {
        if(!isset(Route::$ALIASES[$alias])) return null;
        $r = new URL(Route::$ALIASES[$alias], false);
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
            preg_match("/{(.+?)\s?((\[(.+?)\])|(\#(.+?)))?}/", $l[$i], $matches);

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
     * Check if Route matches current URI (or specified URI).
     * @param string $ROUTE The tested Route, e.g. '/edit/{ID}'.
     * @param string $TYPE The type of Route - use RouteAction::TYPE_API_ROUTE, RouteAction::TYPE_STANDARD_ROUTE or RouteAction::TYPE_NO_LIMIT.
     * @param string $CURRENT The URI, if sets to null, then it will use current URI.
     * @return array|false Returns false if the Route do not match or array with route info: (bool) 'api'; (string) 'route'; (string|array|function) 'action'; 
     */
    private static function test($ROUTE, $TYPE, $CURRENT = null) {
        $CURRENT = ($CURRENT === null ? new URL() : new URL($CURRENT, false));
        $CURRENT = $CURRENT->getLink();
        $x = new URL($ROUTE[0], false);
        $x = $x->getLink();
        $match = true;
        for ($i = 0; $i < count($x); $i++) {
            if ($x[$i] == "*") {
                if($TYPE !== self::TYPE_NO_LIMIT)
                    Route::$CURRENT_ROUTE_INFO = array(
                        "api" => ($TYPE === self::TYPE_API_ROUTE),
                        "route" => $ROUTE[0],
                        "action" => $ROUTE[1],
                    );
                return array(
                    "api" => ($TYPE === self::TYPE_API_ROUTE),
                    "route" => $ROUTE[0],
                    "action" => $ROUTE[1],
                );
            }
            if ($x[$i] == "+") {
                continue;
            }
            preg_match(self::URL_REGEX, $x[$i], $matches);
            if (count($matches) > 0) {
                if (!empty($matches[4])) {
                    preg_match("/" . $matches[4] . "/", $CURRENT[$i], $m);
                    if (count($m) > 0) {
                        if($TYPE !== self::TYPE_NO_LIMIT)
                            Route::$VALUES[$matches[1]] = $CURRENT[$i];
                        continue;
                    }
                } elseif (isset($matches[6])) {
                    $v = $matches[6];
                    $r = $v($CURRENT[$i]);

                    if ($r == true) {
                        if($TYPE !== self::TYPE_NO_LIMIT)                
                            Route::$VALUES[$matches[1]] = $CURRENT[$i];
                        continue;
                    }
                } else {
                    if($TYPE !== self::TYPE_NO_LIMIT)                    
                        Route::$VALUES[$matches[1]] = $CURRENT[$i];
                    continue;
                }
            }
            if (strtolower($x[$i]) == "<controller>") {
                if (ctype_alnum($CURRENT[$i])) {
                    if($TYPE !== self::TYPE_NO_LIMIT)                    
                        Route::$ROUTE_CONTROLLER = $CURRENT[$i];
                    continue;
                }
            }
            if (strtolower($x[$i]) == "<action>") {
                if (ctype_alnum($CURRENT[$i])) {
                    if($TYPE !== self::TYPE_NO_LIMIT)                    
                        Route::$ROUTE_ACTION = $CURRENT[$i];
                    continue;
                }
            }
            if (!isset($CURRENT[$i]) || $x[$i] != $CURRENT[$i]) {
                $match = false;
                break;
            }
        }
        if ($match && count($x) == count($CURRENT)) {
            if($TYPE !== self::TYPE_NO_LIMIT)
                Route::$CURRENT_ROUTE_INFO = array(
                    "api" => true,
                    "route" => $ROUTE[0],
                    "action" => $ROUTE[1],
                );
            return array(
                "api" => ($TYPE === self::TYPE_API_ROUTE),
                "route" => $ROUTE[0],
                "action" => $ROUTE[1],
            );
        }
        // Route do not match
        return false;
    }
}