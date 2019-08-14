<?php
Route::setError(Route::ERROR_MIDDLEWARE, "default/errors/1.php");
Route::setError(Route::ERROR_BAD_REQUEST, "default/errors/400.php");
Route::setError(Route::ERROR_FORBIDDEN, "default/errors/403.php");
Route::setError(Route::ERROR_NOT_FOUND, "default/errors/404.php");
Route::setError(Route::ERROR_NOT_LOGGED_IN, "default/errors/not_logged_in.php");
Route::set('/git-deploy', function() {
    //if($GLOBALS["request"]->getHeader("x-hub-signature") == "sha1=4bed6d143679f8a71db502ad22585a1d1530a597") {
Logger::log(shell_exec("ssh-agent bash -c 'ssh-add /var/www/panx-framework; git pull git develop'"), "git.log");
Logger::log("Pulled.", "git.log");
//} else {
Logger::log($GLOBALS["request"]->getHeader("x-hub-signature"), "git.log");
//}
Logger::log(json_encode(file_get_contents('php://input')), "git.log");
Logger::log(json_encode($GLOBALS["request"]->getHeaders()), "git.log");
dump(file_get_contents('php://input'));
//xd?

});