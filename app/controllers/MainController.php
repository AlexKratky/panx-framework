<?php
/**
 * @name AuthMiddleware.php
 * @link https://alexkratky.cz                          Author website
 * @link https://panx.eu/docs/                          Documentation
 * @link https://github.com/AlexKratky/panx-framework/  Github Repository
 * @author Alex Kratky <info@alexkratky.cz>
 * @copyright Copyright (c) 2019 Alex Kratky
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @description Example controller.
 */

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