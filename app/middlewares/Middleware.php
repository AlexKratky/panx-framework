<?php
interface Middleware {
    /**
     * This function is called everytime (If the requested URI uses this middleware) and decides if the request is valid or not.
     * @return bool Returns true, if the request is valid, returns false otherwise.
     */
    public static function handle(): bool;

    /**
     * This method handle errors, if you do not set any error() function, it will display Error - Request declined by middleware.
     * @return int|string The error code. If return value is '-1', it won't include any other files.
     */
    public static function error();
}