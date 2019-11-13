<?php
Route::set("/_setup", "default/setup/setup.php")->setLocalOnly();
Route::set("/_setup/save", "default/setup/setup_save.php", ["POST"])->setLocalOnly();
Route::set("/_setup/test", "default/setup/test.php")->setLocalOnly();