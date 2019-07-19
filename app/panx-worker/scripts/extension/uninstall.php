<?php
if (empty($ARGS[2])) {
    error("You need to the extension name.");
}

$extension = $ARGS[2];
if(file_exists($PATH . "/app/panx-worker/scripts/extension/" . strtolower($extension) . ".json")) {
    $f = json_decode(file_get_contents($PATH . "/app/panx-worker/scripts/extension/" . strtolower($extension) . ".json"), true);
    //var_dump($f);
    foreach ($f as $key => $value) {
        if(file_exists($PATH . $key)) {
            unlink($PATH . $key);
            info_msg("Deleted: " . $PATH . $key);
        } else {
            error_msg("File not found (already deleted): " . $PATH . $key);
        }
    }
} else {
    error("No json file found, the extension can not be uninstalled.");
}