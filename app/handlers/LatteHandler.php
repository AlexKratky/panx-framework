<?php

class LatteHandler extends Handler {
    public static $themeX;

    public static function handle($file) {
        $latte = new Latte\Engine;
        $set = new Latte\Macros\MacroSet($latte->getCompiler());

        $set->addMacro('link', null, null,
            'if ($_l->tmp = array_filter(%node.array))
            echo \' href="\' . LatteHandler::convert($_l->tmp) . \'"\'
        ');
        $set->addMacro(
            "component",
            function($node, $writer) {
                $param = $node->args;
                return $writer->write('echo LatteHandler::component("'.$param.'")'); 
            },
            function($node, $writer) {
                $param = $node->args;
                return $writer->write('echo LatteHandler::componentEnd("'.$param.'")'); 
            }, 
            null
        );
        $set->addMacro(
            "singleComponent",
            function($node, $writer) {
                $param = $node->args;
                return $writer->write('echo LatteHandler::singleComponent("'.$param.'")'); 
            },
            null, 
            null
        );
        $set->addMacro(
            "form",
            function($node, $writer) {
                $param = $node->args;
                return $writer->write('LatteHandler::form("'.$param.'")'); 
            },
            null, 
            null
        );
        //$_l->tmp
        self::addParameter("APP_NAME", $GLOBALS["CONFIG"]["basic"]["APP_NAME"]);
        self::addParameter("APP_URL", $GLOBALS["CONFIG"]["basic"]["APP_URL"]);
        self::addParameter("URL_STRING", $GLOBALS["request"]->getUrl()->getString());
        $latte->setTempDirectory($_SERVER['DOCUMENT_ROOT']."/../temp/");
        $latte->render($_SERVER['DOCUMENT_ROOT']."/../template/$file", self::$parameters);

    }

    public static function convert($x) {
        $alias = $x[0];
        $params = $x[1] ?? null;
        $get = $x[2] ?? null;
        return Route::alias($alias, $params, $get);
    }

    public static function component($node = null) {
        $component = explode(",", $node, 2)[0];
        $args = self::convertNodeToArray($node);
        self::$themeX = new ThemeX($component, $args);
        return self::$themeX->componentStart();
    }

    public static function componentEnd($node = null) {
        return self::$themeX->componentEnd();
    }

    public static function singleComponent($node = null) {
        $component = explode(",", $node, 2)[0];
        $args = self::convertNodeToArray($node);
        self::$themeX = new ThemeX($component, $args);
        return self::$themeX->component();
    }

    public static function form($node = null) {
        $component = explode(",", $node, 2)[0];
        $f = new $component();
        $f->render();
    }



    private static function convertNodeToArray($node) {
        $m = preg_split("/'[^']*'(*SKIP)(*F)|(,\s)+/", $node);
        $args = array();
        foreach ($m as $v) {
            if(strpos($v, " => ")) {
                $v = explode(" => ", $v, 2);
                $t = array();
                $t = explode(", ", $v[1]);
                $t[0] = ltrim($t[0], "'");
                $t[count($t) - 1] = rtrim($t[count($t) - 1], "'");
                if(count($t) == 1) {
                    $t = $t[0];
                }
                //dump($t);
                $args[$v[0]] = $t;

            }
        }
        return $args;
    }
}