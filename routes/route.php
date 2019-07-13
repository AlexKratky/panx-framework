<?php
Route::set("/", "home.php", ["POST", "GET"]);

Route::set("/logined", function () {
    echo "Yep!";
})->setMiddleware(["AuthMiddleware"])->setController(["xd", "xd2"]);

Route::set("/post/", ["post-list.php"]);
Route::set("/test/*", "test.php");
Route::set("/lang", function() {
    echo __("welcome");
});
Route::set("/Handler", ["handler.latte", "test.latte"])->setController("MainController");
Route::set("/Handler2/*", ["handler.latte"])->setController("MainController");
Route::set("/MAIN/*", function() {
    var_dump(Route::getController());
});
