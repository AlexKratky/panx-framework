<?php
$zip = new ZipArchive;
if ($zip->open($PATH . "/temp/$version.zip") === true) {
    $zip->extractTo($PATH);
    $zip->close();
    if (file_exists($PATH . "/temp/$version/changelog")) {
        displayChangelog($PATH . "/temp/$version/changelog");
    }

    echo ("Installation was successful \n");
} else {
    echo ("Failed to install.\n");
    exit();
}
