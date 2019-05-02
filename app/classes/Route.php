<?php
class Route {
    private static $ROUTES = array();
    private static $ERRORS = array();
    private static $VALUES = array();
    /**
     * Error class constants
     */
    const ERROR_FORBIDDEN = 403;
    const ERROR_NOT_FOUND = 404;

    /**
     * Custom errors
     */
    const ERROR_NOT_LOGGED_IN = "NOT_LOGGED_IN";

    /**
     * Save route to $ROUTES
     */
    public static function set($ROUTE, $TEMPLATE_FILE) {
        /**
         * $matches[0][0] : {id}
         * $matches[1][0] : id
         */
        //preg_match_all("/{(.+?)}/", $ROUTE, $matches);
        
        self::$ROUTES[$ROUTE] = $TEMPLATE_FILE;
    }

    /**
     * Returns file which responds to ROUTE
     * Support wildcards:
     *  + : Mean one elements, eg: /post/+/edit -> match with /post/1/edit, /post/2/edit ...
     *  * : Mean one or more element, eg: /post/* -> match with /post/1, /post/1/edit ... 
     *  {VARIABLE} : Mean one element (Same as +), but the value will be saved and can be accessed by getValue()
     */
    public static function search($SEARCH_ROUTE) {
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

    public static function getValue($VALUE_NAME) {
        return self::$VALUES[$VALUE_NAME];

    }
}