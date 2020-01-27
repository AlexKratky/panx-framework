<?php
Route::set("/rest/v1/<action>/", null)->setController("RestController");
Route::set("/rest/v1/<action>/{table}", null)->setController("RestController");
Route::set("/rest/v1/<action>/{table}/{column}/{id}", null)->setController("RestController");
Route::set("/rest/v1/<action>/{table}/{column}/{id}/all", null)->setController("RestController");