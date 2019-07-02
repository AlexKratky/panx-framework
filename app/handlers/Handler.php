<?php
class Handler {
    protected static $parameters = array();

    public static function handle($file) {
        
    }

    public static function setParameters($parameters) {
        self::$parameters = $parameters;
    }

    public static function addParameters($parameters) {
        array_push(self::$parameters, $parameters);
    }
}
