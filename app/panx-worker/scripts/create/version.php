<?php
$version = read("Enter version (e.g.: 0.1)");
if ($version == "") {
    error("You need to enter version name.");
}

info();

$rootPath = realpath($PATH);
$zipFileName = "public/download/$version.zip";
$zip = new ZipArchive();
$zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE);

addFolderToZip($rootPath . "/", $zip, $zipdir = '');
$zip->close();
