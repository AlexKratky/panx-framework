<?php
class Route {
    private static $ROUTES = array();
    private static $MIDDLEWARES = array();
    private static $LOCK = array();
    private static $API_ROUTES = array();
    private static $API_MIDDLEWARES = array();
    private static $ERRORS = array();
    private static $VALUES = array();

    /**
     * Error class constants
     */
    const DO_NOT_INCLUDE_ANY_FILE = -1; // wont include any file
    const ERROR_MIDDLEWARE = 1;
    const ERROR_BAD_REQUEST = 400;
    const ERROR_FORBIDDEN = 403;
    const ERROR_NOT_FOUND = 404;

    /**
     * Custom errors
     */
    const ERROR_NOT_LOGGED_IN = "NOT_LOGGED_IN";

    /**
     * Save route to $ROUTES
     */
    public static function set($ROUTE, $TEMPLATE_FILE, $LOCK = null) {
        /**
         * $matches[0][0] : {id}
         * $matches[1][0] : id
         */
        //preg_match_all("/{(.+?)}/", $ROUTE, $matches);
        
        self::$ROUTES[$ROUTE] = $TEMPLATE_FILE;
        if($LOCK !== null) self::$LOCK[$ROUTE] = $LOCK;
        return $ROUTE;
    }

    public static function apiGroup($VERSION, $ROUTES) {
        self::$API_ROUTES[$VERSION] = $ROUTES;
    }

    /**
     * Returns file which responds to ROUTE
     * Support wildcards:
     *  + : Mean one elements, eg: /post/+/edit -> match with /post/1/edit, /post/2/edit ...
     *  * : Mean one or more element, eg: /post/* -> match with /post/1, /post/1/edit ... 
     *  {VARIABLE} : Mean one element (Same as +), but the value will be saved and can be accessed by getValue()
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
     * Set route for ERROR CODE
     */
    public static function setError($ERROR_CODE, $ERROR_FILE) {
        self::$ERRORS[$ERROR_CODE] = $ERROR_FILE;
    }

    /**
     * Returns file which repsonds to error code
     */
    public static function searchError($ERROR_CODE) {
        return self::$ERRORS[$ERROR_CODE];
    }

    public static function setMiddleware($ROUTES, $MIDDLEWARES) {
        if(!is_array($ROUTES)) {
            self::$MIDDLEWARES[$ROUTES] = $MIDDLEWARES;
        } else {
            foreach ($ROUTES as $ROUTE) {
                self::$MIDDLEWARES[$ROUTE] = $MIDDLEWARES;

            }
        }
    }

    public static function setApiMiddleware($API_GROUP, $MIDDLEWARES) {
            self::$API_MIDDLEWARES[$API_GROUP] = $MIDDLEWARES;
    }

    public static function getValue($VALUE_NAME) {
        return self::$VALUES[$VALUE_NAME];

    }

    public static function getDataTable() {
        $data = array();
        foreach (self::$ROUTES as $ROUTE => $FILE) {
            array_push($data, array('TYPE' => 'ROUTE', 'URI/CODE' => $ROUTE, 'ACTION' => (is_object($FILE) && ($FILE instanceof Closure) ? "function" : (is_array($FILE) ? "[".implode(", ", $FILE) . "]" :  $FILE)), 'LOCK' => (isset(self::$LOCK[$ROUTE]) ? "[".implode(", ", self::$LOCK[$ROUTE])."]" : "[]"), 'MIDDLEWARES' => (isset(self::$MIDDLEWARES[$ROUTE]) ? "[".implode(", ", self::$MIDDLEWARES[$ROUTE])."]" : "[]")));
        }

        //var_dump(self::$API_ROUTES);
        foreach (self::$API_ROUTES as $API_ROUTE => $API) {
   
            foreach ($API as $route => $value) {
                array_push($data, array('TYPE' => 'API', 'URI/CODE' => "/api/" . $API_ROUTE . "/" . $value[0], 'ACTION' => (is_object($value[1]) && ($value[1] instanceof Closure) ? "function" : (is_array($value[1]) ? "[" . implode(", ", $value[1]) . "]" : $value[1])), 'LOCK' => (isset($value[2]) ? "[" . implode(", ", $value[2]) . "]" : "[]"), 'MIDDLEWARES' => "[]"));

            }
            

        }

        foreach (self::$ERRORS as $ERROR => $ACTION) {
            array_push($data, array('TYPE' => 'ERROR', 'URI/CODE' => $ERROR, 'ACTION' => (is_object($ACTION) && ($ACTION instanceof Closure) ? "function" : (is_array($ACTION) ? "[" . implode(", ", $ACTION) . "]" : $ACTION)), 'LOCK' => (isset(self::$LOCK[$ROUTE]) ? "[" . implode(", ", self::$LOCK[$ROUTE]) . "]" : "[]"), 'MIDDLEWARES' => "[]"));
        }


        return $data;
    }
}