<?php
$info = array();

$path = $PATH . "/";
$index = 0;
$folders = array($path);
while (count($folders) > $index) {
    $f = scandir($folders[$index]);
    for ($i = 2; $i < count($f); $i++) {
        // #+?\ .+?\n <- Match title
        if (is_dir($folders[$index] . $f[$i])) {
            if ($f[$i] == ".git") {
                continue;
            }

            array_push($folders, $folders[$index] . $f[$i] . "/");
            continue;
        }
        $info[str_replace($PATH, "", $folders[$index] . $f[$i])] = array(filemtime($folders[$index] . $f[$i]), filesize($folders[$index] . $f[$i]));
    }
    $index++;
}

file_put_contents($PATH."/info.json", json_encode($info));
