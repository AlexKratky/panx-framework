<?php
require_once __DIR__ . "/../mini-loader.php";
$x = new APIModel();
$x->resetRateDaily();
echo "Daily rate limits reset to 0.\n";