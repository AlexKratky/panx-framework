<?php
Route::set("/", "home.php");
Route::set("/post/{AUTHOR_ID}/{ID}", ["header.php", "post.php"]);
Route::set("/login", "login.php");
Route::set("/signin", function() {
    redirect("/login");
});
Route::set("/test/*", "test.php");