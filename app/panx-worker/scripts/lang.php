<?php
function isAssoc(array $arr): bool
{
    if (array() === $arr) {
        return false;
    }

    return array_keys($arr) !== range(0, count($arr) - 1);
}

$lang_files = scandir($PATH . "/app/resources/lang/");
$LANG = array();
foreach ($lang_files as $lang_file) {
    if ($lang_file == "." || $lang_file == ".." || $lang_file == "info.txt") {
        continue;
    }

    if (is_dir($PATH . "/app/resources/lang/" . $lang_file)) {
        continue;
    }

    $l = str_replace("default_", "", str_replace(".lang", "", $lang_file));

    $lang_f = file_get_contents($PATH . "/app/resources/lang/" . $lang_file);
    $lang_f = explode(PHP_EOL, $lang_f);
    foreach ($lang_f as $line) {
        if(strpos($line, ":") !== false && strpos($line, "#") !== 0) {
            $line = explode(": ", $line, 2);
            if(strpos($line[0], ".") === false) {
                $LANG[$l][$line[0]] = $line[1];
            } else {
                //need to use namespace
                $line[0] = explode(".", $line[0], 2);
                $LANG[$l][$line[0][0]][$line[0][1]] = $line[1];
            }
        }
    }
}
var_dump($LANG);

/*
$renderer = new TextTable($LANG);
$renderer->showHeaders(true);
$renderer->render();
*/