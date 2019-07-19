<?php
if (!isset($ARGS[2])) {
    $name = read("Enter name of controller");
    if ($name == "") {
        error("You need to enter controller name.");
    }
} else {
    $name = $ARGS[2];
}

$dir = $PATH . "/app/controllers/";
$code = '<?php
class '.$name.' {
    private static $handler;

    public static function main($handler)
    {
        self::$handler = $handler;
        if (isset($GLOBALS["request"]->getUrl()->getLink()[1])) {
            switch ($GLOBALS["request"]->getUrl()->getLink()[1]) {
                case "login":
                    self::login();
                    break;
            }
        }
    }

    public static function login() {
        self::$handler::setParameters([
            "name" => "xx",
            "mail" => "yy",
        ]);
    }
}';

file_put_contents($dir.$name.".php", $code);