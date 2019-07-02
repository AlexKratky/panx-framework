<?php
class MainController {
    private static $handler;

    public static function main($handler) {
        self::$handler = $handler;

        self::$handler::setParameters($parameters = [
            'items' => ['one', 'two', 'three', 'latxteeeeeeeeeeeee'],
        ]);
        
        if(isset($GLOBALS["request"]->getUrl()->getLink()[2])) {
            $action = $GLOBALS["request"]->getUrl()->getLink()[2];
            
            if(method_exists('MainController', $action)) {
                call_user_func('self::' . $action);
            }
        }
    }

    public static function test() {
        self::$handler::setParameters($parameters = [
            'items' => ['test', 'function'],
        ]);
        echo "called";
    }
}