<?php
class LatteHandler extends Handler {
    public static function handle($file) {
        require_once $_SERVER['DOCUMENT_ROOT']."/../vendor/autoload.php";
        $latte = new Latte\Engine;
        $set = new Latte\Macros\MacroSet($latte->getCompiler());

        $set->addMacro('link', null, null,
            'if ($_l->tmp = array_filter(%node.array))
            echo \' href="\' . LatteHandler::convert($_l->tmp) . \'"\'
        ');
        //$_l->tmp
        $latte->setTempDirectory($_SERVER['DOCUMENT_ROOT']."/../temp/");
        $latte->render($_SERVER['DOCUMENT_ROOT']."/../template/$file", self::$parameters);

    }

    public static function convert($x) {
        $alias = $x[0];
        $params = $x[1] ?? null;
        $get = $x[2] ?? null;
        return Route::alias($alias, $params, $get);
    }
}