<?php
/**
 * @name MainController.php
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
    private static $mainModel;

    public static function main($handler) {
        self::$handler = $handler;
        self::$mainModel = new MainModel();

        self::$handler::setParameters([
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
        self::$handler::setParameters([
            'items' => ['test', 'function'],
        ]);
        echo "called";
    }

    public static function select() {
        self::$handler::setParameters(self::$mainModel->selectFromDb());
    }
}