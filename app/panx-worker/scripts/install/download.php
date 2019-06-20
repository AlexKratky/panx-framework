<?php
$version;
if (!class_exists('ZipArchive')) {
    echo ("ZipArchive is not installed. \n");
    exit();
}

if (!isset($ARGS[1])) {
    $version = file_get_contents("https://panx.eu/api/v1/getlatestversion");
    echo ("No version passed, using the latest one: $version \n");
} else {
    $version = $ARGS[1];
}

if (!file_exists($PATH . "/temp/")) {
    mkdir($PATH . "/temp");
}
try {
    $z = fopen("https://panx.eu/download/$version.zip", 'r');
    if (!$z) {
        echo ("Failed to download $version.zip\n");
        exit();
    }
    file_put_contents($PATH . "/temp/$version.zip", $z);
    fclose($z);
} catch (Exception $e) {
    error($e);
}
