<?php
if (!isset($ARGS[2])) {
    $name = read("Enter name of middleware");
    if ($name == "") {
        error("You need to enter middleware name.");
    }
} else {
    $name = $ARGS[2];
}

$dir = $PATH . "/app/middlewares/";
$code = '<?php
class '.$name.' {
    /**
     * This function is called everytime (If the requested URI uses this middleware) and decides if the request is valid or not.
     * @return bool Returns true, if the request is valid, returns false otherwise.
     */
    public static function handle() {
        return true;
    }

    /**
     * This method handle errors, if you do not set any error() function, it will display Error - Request declined by middleware.
     * @return int|string The error code. If return value is \'-1\', it won\'t include any other files.
     */
    public static function error() {
        /*echo "NOT AUTHENTICATED";
        return -1;*/
        redirect(\'/login\');
    }
}';

file_put_contents($dir . $name . ".php", $code);
