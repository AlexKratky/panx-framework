<?php
Route::setError(Route::ERROR_MIDDLEWARE, "default/errors/1.php");
Route::setError(Route::ERROR_BAD_REQUEST, "default/errors/400.php");
Route::setError(Route::ERROR_FORBIDDEN, "default/errors/403.php");
Route::setError(Route::ERROR_NOT_FOUND, "default/errors/404.php");
Route::setError(Route::ERROR_NOT_LOGGED_IN, "default/errors/not_logged_in.php");
