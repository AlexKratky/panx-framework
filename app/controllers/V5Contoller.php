<?php
class V5Contoller {
    private static $handler;

    public static function main($handler)
    {
        self::$handler = $handler;
    }

    public static function login() {
        echo 'login';
        var_dump(self::$handler);
    }


    public static function parameter($ID,$NAME) {
        echo $ID . " " . $NAME;
    }

    public static function test() {
        echo 'test';
    }

    public static function random() {
        echo 'random';
    }
}