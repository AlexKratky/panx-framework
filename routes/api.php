<?php
// apiGroup create route /api/version/route
Route::apiGroup("v1", array(
    // /api/v1/list
    array("list", function(){
        echo "list";
    }),
    
    array("getlatestversion", function() {
        echo "0.1.1";
    }),

    array("getposts", function () {
        //dump("test");
        echo json(json_encode(Post::listPosts()));
    }),

    array('server', function () {
        dump($_SERVER);
    }),

    array('sleep', function () {
        sleep(1);
    }),

    array('getTitle/{ID}', function() {
        echo Post::getTitle(Route::getValue("ID"));

    }),
));

Route::apiGroup("v2", array(
    // /api/v1/list
    array("edit/post", function(){
        echo "edit post";
    }),
    
));
Route::setApiMiddleware("v2", ["AuthMiddleware"]);

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
