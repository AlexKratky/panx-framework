<?php
require_once __DIR__ . "/../mini-loader.php";
Cache::clearUnused(__DIR__."/../../");
echo "Cache older then 1 day was cleared.\n";