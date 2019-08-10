<?php
class ExampleController {
    private static $handler;

    public static function main($handler)
    {
        self::$handler = $handler;
    }

    public static function login() {
        self::$handler::setParameters([
            'items' => ['test', 'function'],
        ]);
    }


    public static function parameter($ID,$NAME) {
        self::$handler::setParameters([
            'items' => [$NAME, $ID],
        ]);
    }
}