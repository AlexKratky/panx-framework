<?php
require_once __DIR__ . "/../mini-loader.php";
$x = new APIModel();
$x->resetRateMonthly();
echo "Monthly rate limits reset to 0.\n";
