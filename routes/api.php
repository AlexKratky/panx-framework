<?php
// apiGroup create route /api/version/route
Route::apiGroup("v1", array(
    // /api/v1/list
    array("list", function(){
        echo "list";
    }),
    
    array("getlatestversion", function() {
        echo "0.1.2";
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

    array('getheaders', function () {
        foreach (getallheaders() as $name => $value) {
            echo "$name: $value<br>";
        }
    }),

    array('request', function () {
        echo $GLOBALS["request"]->getUrl()->getString();
        echo "<br>";
        echo $GLOBALS["request"]->getQuery();
        echo "<br>";
        echo $GLOBALS["request"]->getQuery('xd');
        echo "<br>";
        echo var_dump($GLOBALS["request"]->getPost());
        echo "<br>";
        echo $GLOBALS["request"]->getPost('xd');
        echo "<br>";
        echo $GLOBALS["request"]->getMethod();
        echo "<br>";
        echo var_dump($GLOBALS["request"]->isMethod('post'));
        echo "<br>";
        echo $GLOBALS["request"]->getHeader('user-agent');
        echo "<br>";
        echo var_dump($GLOBALS["request"]->getHeaders());
        echo "<br>";
        echo var_dump($GLOBALS["request"]->getReferer());
        echo "<br>";
        echo var_dump($GLOBALS["request"]->isSecured());
        echo "<br>";
        echo var_dump($GLOBALS["request"]->isAjax());
        echo "<br>";
        echo $GLOBALS["request"]->getRemoteAddress();
        echo "<br>";
        echo var_dump($GLOBALS["request"]->detectLanguage(array("en", "sk", "cz")));
        echo "<br>";
        echo var_dump($GLOBALS["request"]->workWith('GET', ['xd', 'lel']));
        echo "<br>";
        echo var_dump($GLOBALS["request"]->getMostPreferredLanguage());
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
        //echo "edit post " . Route::getValue("ID");
        error(403);
    }),

    array("view/post/+", function () {
        echo "view post";
    }),

    array("delete/post/*", function () {
        echo "delete post";
    }),

));
