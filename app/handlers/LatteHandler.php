<?php
class LatteHandler extends Handler {
    public static function handle($file) {
        require_once $_SERVER['DOCUMENT_ROOT']."/../vendor/autoload.php";
        $latte = new Latte\Engine;

        $latte->setTempDirectory($_SERVER['DOCUMENT_ROOT']."/../temp/");
        $latte->render($_SERVER['DOCUMENT_ROOT']."/../template/$file", self::$parameters);

    }
}