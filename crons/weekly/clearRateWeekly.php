<?php
require_once __DIR__ . "/../mini-loader.php";
$x = new APIModel();
$x->resetRateWeekly();
echo "Weekly rate limits reset to 0.\n";
