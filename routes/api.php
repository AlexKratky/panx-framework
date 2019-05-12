<?php
// apiGroup create route /api/version/route
Route::apiGroup("v1", array(
    // /api/v1/list
    array("list", function(){
        echo "list";
    }),
    
    array("getlatestversion", function() {
        echo "0.1";
    }),
));

Route::apiGroup("v2", array(
    // /api/v1/list
    array("edit/post", function(){
        echo "edit post";
    }),
    
));

Route::apiGroup("v3", array(
    // /api/v1/list
    array("edit/post/{ID}", function () {
        echo "edit post " . Route::getValue("ID");
    }),

    array("view/post/+", function () {
        echo "view post";
    }),

    array("delete/post/*", function () {
        echo "delete post";
    }),

));
