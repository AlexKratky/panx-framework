<?php
Route::setError(Route::ERROR_MIDDLEWARE, "default/errors/1.php");
Route::setError(Route::ERROR_BAD_REQUEST, "default/errors/400.php");
Route::setError(Route::ERROR_FORBIDDEN, "default/errors/403.php");
Route::setError(Route::ERROR_NOT_FOUND, "default/errors/404.php");
Route::setError(Route::ERROR_INTERNAL_SERVER, "default/errors/500.php");
Route::setError(Route::ERROR_NOT_LOGGED_IN, "default/errors/not_logged_in.php");
Route::set('/git-deploy', function() {
    //coming in future versions...
    error(403);
});
Route::set('/cron-execute/{SECRET}', function() {
    if(!empty($GLOBALS["CONFIG"]["cron"]["SECRET"]) && Route::getValue("SECRET") == $GLOBALS["CONFIG"]["cron"]["SECRET"]) {
        $IS_REQUIRED_FROM_WEB = true;
        require $_SERVER["DOCUMENT_ROOT"] . "/../crons/execute.php";
    } else {
        error(400);
    }
});
Route::set('/error/{CODE}', function() {
    error(Route::getValue("CODE"));
})->setAlias("error");