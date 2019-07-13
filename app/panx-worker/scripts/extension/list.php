<?php
$NOT_EXTENSIONS = array("install.php", "list.php", "uninstall.php");
$e = scandir($SCRIPT_PATH."extension");
info_msg("Available extensions:");
foreach ($e as $extension) {
    if($extension == "." || $extension == ".." || in_array($extension, $NOT_EXTENSIONS)) continue;
    if(pathinfo($SCRIPT_PATH."extension/".$extension, PATHINFO_EXTENSION) != "php") continue;
    info_msg(" • " . basename($extension, ".php"));
}