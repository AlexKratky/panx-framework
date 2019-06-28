<?php
$changelog = file_get_contents($path);
$changelog = explode(PHP_EOL, $changelog);
echo "\n";
foreach ($changelog as $line) {
    if (isset($line[0])) {
        if ($line[0] == "?") {
            echo "\e[0;36;40m" . substr($line, 1) . "\e[0m\n";
        } else if ($line[0] == "!") {
            echo "\e[0;31;40m" . substr($line, 1) . "\e[0m\n";
        } else if ($line[0] == "#")  {
            continue;
        } else {
            echo $line . "\n";
        }
    } else {
        echo $line . "\n";
    }

}
echo "\n";
