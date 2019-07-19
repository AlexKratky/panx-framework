<?php
if (!isset($ARGS[2])) {
    $name = read("Enter name of model");
    if ($name == "") {
        error("You need to enter model name.");
    }
} else {
    $name = $ARGS[2];
}

$dir = $PATH."/app/models/";
$code = "<?php
class $name {
    //write here your own function (non-static)
}";

file_put_contents($dir . $name . ".php", $code);
