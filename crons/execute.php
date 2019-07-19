<?php
echo "\n";
$counter = 0;
if(file_exists(__DIR__ . "/counter.info")) {
    $counter = (int)file_get_contents(__DIR__ . "/counter.info");
    echo "Current counter: $counter\n";
    file_put_contents(__DIR__ . "/counter.info", $counter+1);
} else {
    file_put_contents(__DIR__ . "/counter.info", $counter);
}
$daily = scandir(__DIR__ . "/daily/");
foreach ($daily as $file) {
    if($file == "." || $file == "..") {continue;}
    require(__DIR__."/daily/".$file);
}
if($counter % 7 == 0) {
    $weekly = scandir(__DIR__ . "/weekly/");
    foreach ($weekly as $file) {
        if($file == "." || $file == "..") {continue;}
        require(__DIR__."/weekly/".$file);
    }
}
if($counter % 30 == 0) {
    $monthly = scandir(__DIR__ . "/monthly/");
    foreach ($monthly as $file) {
        if($file == "." || $file == "..") {continue;}
        require(__DIR__."/monthly/".$file);
    }
}
echo "\n";