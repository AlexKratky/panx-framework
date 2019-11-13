<?php
/**
 * @name AuthMiddleware.php
 * @link https://alexkratky.cz                          Author website
 * @link https://panx.eu/docs/                          Documentation
 * @link https://github.com/AlexKratky/panx-framework/  Github Repository
 * @author Alex Kratky <info@alexkratky.cz>
 * @copyright Copyright (c) 2019 Alex Kratky
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @description Example middleware.
 */


class AuthMiddleware {
    /**
     * This function is called everytime (If the requested URI uses this middleware) and decides if the request is valid or not.
     * @return bool Returns true, if the request is valid, returns false otherwise.
     */
    public static function handle() {
        /*if(isset($_SESSION["username"]) && isset($_SESSION["password"])) {
            return true;
        } else {
            return false;
        }*/
        $a = new Auth();
        return $a->isLogined();
    }

    /**
     * This method handle errors, if you do not set any error() function, it will display Error - Request declined by middleware.
     * @return int|string The error code. If return value is '-1', it won't include any other files.
     */
    public static function error() {
        /*echo "NOT AUTHENTICATED";
        return -1;*/
        redirect('/login');
    }
}