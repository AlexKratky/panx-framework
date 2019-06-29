<?php
class LatteHandler {
    public static function handle($file) {
        require_once $_SERVER['DOCUMENT_ROOT']."/../vendor/autoload.php";
        $latte = new Latte\Engine;

        $latte->setTempDirectory($_SERVER['DOCUMENT_ROOT']."/../temp/");

        $parameters = [
            'items' => ['one', 'two', 'three', 'lateeeeeeeeeeeee'],
        ];

        // kresli na výstup
        $latte->render($_SERVER['DOCUMENT_ROOT']."/../template/$file", $parameters);

    }
}