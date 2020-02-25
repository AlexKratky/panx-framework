<?php
Route::set('/', "home.php")->setTitle("php micro framework")->setAlias('home');
Route::set('/panx/download', "download.latte")->setTitle("Download files");
Route::set('/panx/download/extensions', "download_extensions.latte")->setTitle("Download extensions");