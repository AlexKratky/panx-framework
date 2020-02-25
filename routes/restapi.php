<?php
Route::set("/rest/v1/login", null)->setController("RestController#login");


Route::set("/rest/v1/get/u/{table}", null)->setController("RestController#getUser"); //all of user
Route::set("/rest/v1/get/u/{table}/{limit_or_column[^[0-9]*$]}", null)->setController("RestController#getUser"); //all of user w/ limit
Route::set("/rest/v1/get/u/{table}/{limit_or_column}/{id}", null)->setController("RestController#getUser"); // get single user row
Route::set("/rest/v1/get/{table}", null)->setController("RestController#get"); //all
Route::set("/rest/v1/get/{table}/{limit_or_column[^[0-9]*$]}", null)->setController("RestController#get"); //all w/ limit
Route::set("/rest/v1/get/{table}/{limit_or_column}/{id}", null)->setController("RestController#get"); // get single row

// /u/ is used when we have permissions to select all but we want only the data from the user. If the user don't have permission to select all, then will be selected data only from user.


Route::set("/rest/v1/create/{table}", null)->setController("RestController#create"); // create row in table


Route::set("/rest/v1/update/{table}/{id}", null)->setController("RestController#update"); // update row in table
// Route::set("/rest/v1/update/u/{table}/{id}", null)->setController("RestController#update"); // update user's row in table


Route::set("/rest/v1/delete/{table}/{id}", null)->setController("RestController#update"); // delete row in table, use columns[0]
Route::set("/rest/v1/delete/{table}/{column}/{id}", null)->setController("RestController#update"); // delete row in table
// Route::set("/rest/v1/delete/u/{table}/{id}", null)->setController("RestController#update"); // delete row in table, use columns[0]
// Route::set("/rest/v1/delete/u/{table}/{column}/{id}", null)->setController("RestController#update"); // delete row in table


// Route::set("/rest/v1/<action>/{table}", null)->setController("RestController");
// Route::set("/rest/v1/<action>/{table}/{column}/{id}", null)->setController("RestController");
// Route::set("/rest/v1/<action>/{table}/{column}/{id}/all", null)->setController("RestController");