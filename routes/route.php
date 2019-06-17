<?php
Route::set("/", "home.php", ["POST", "GET"]);
Route::setMiddleware(
    Route::set("/logined", function () {
        echo "Yep!";
    }), 
    ["AuthMiddleware"])
;
Route::set("/post/", ["post-list.php"]);
Route::set("/login", "login.php");
Route::set("/signin", function() {
    redirect("/login");
});
Route::set("/test/*", "test.php");