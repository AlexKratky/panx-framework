<?php
Route::set("/", "home.php");
Route::set("/post/", ["post-list.php"]);
Route::set("/login", "login.php");
Route::set("/signin", function() {
    redirect("/login");
});
Route::set("/test/*", "test.php");