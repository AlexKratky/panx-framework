<?php
if(empty($ARGS[2])) error("You need to specify URL or path");
$file = $ARGS[2];
/**
 * ! Download from GitHub not working - returning Error 404
 */
if (filter_var($ARGS[2], FILTER_VALIDATE_URL)) {
    if(!file_exists($PATH."/temp/")) {
        mkdir($PATH."/temp/");
    }
    $URL = $ARGS[2];
    $filename = "ext-" . time() . ".zip";
    if(!preg_match("/https?:\/\/github\.com\/.*?\/.*\/archive\/master.zip/", $ARGS[2])) {
        info_msg($ARGS[2]);
        if(preg_match("/https?:\/\/github\.com\/.*?\/[^\/]*/", $ARGS[2], $matches)) {
            var_dump($matches);
            $URL = $matches[0] . "/archive/master.zip";
        }
    } 
    info_msg("Starting download from $URL");
    $fp = fopen($PATH . "/temp/" . $filename, 'w+');
    $ch = curl_init();
    $timeout = 50;
    curl_setopt($ch, CURLOPT_URL, $URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_ENCODING, "zip");
    $data = curl_exec($ch);
    curl_close($ch);
    fclose($fp);

    info_msg("Download ended");
    $file = $PATH . "/temp/" . $filename;
}

$zip = new ZipArchive;
if ($zip->open($file) === true) {
    $zip->extractTo($PATH);
    $zip->close();
    info_msg("Extension installed.");
    $D = read("Delete zip file? [Y/n]");
    if(strtolower($D) != "n") {
        unlink($file);
    }
} else {
    error_msg("Failed to install extension.");
}
