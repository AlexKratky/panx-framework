<?php
require_once $_SERVER['DOCUMENT_ROOT']."/../vendor/autoload.php";

use Tracy\Debugger;

Debugger::enable(!($GLOBALS["CONFIG"]["basic"]["APP_DEBUG"] == "1"));
