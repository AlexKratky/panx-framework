<?php
Route::set("/", "home.php", ["POST", "GET"])->setAlias("home");

Route::set("/logined", function () {
    echo "Yep!";
})->setMiddleware(["AuthMiddleware"])->setController(["xd", "xd2"]);

Route::set("/post/", ["post-list.php"]);
Route::set("/test/*", "test.php");
Route::set("/macro/*", "test.latte");

Route::set("/lang", function() {
    echo __("welcome");
});
Route::set("/Handler", ["handler.latte", "test.latte"])->setController("MainController");
Route::set("/route/<controller>/<action>", ["handler.latte", "test.latte"]);
Route::set("/route-test/<action>/{TEST}", ["handler.latte", "test.latte"])->setController("ExampleController")->setRequiredParameters(array("xd"));
Route::set("/route-test/<action>/{NAME}/{ID[^[0-9]*$]}/*", ["handler.latte", "test.latte"])->setController("ExampleController")->setAlias("test");
Route::set("/Handler2/*", ["handler.latte"])->setController("MainController");
Route::set("/MAIN/*", function() {
    var_dump(Route::getController());
});
Route::set("/required-params-get", function() {echo "ok";})->setRequiredParameters(array("param1", "param2", "param3"));
Route::set("/required-params-post", function() {echo "ok";})->setRequiredParameters(array(), array("param1", "param2"));


Route::set("/images", "img.php");

//example loader of Pagination
Route::set("/images/load/{PAGE}", function() {
    $data = [84, 424, 507, 633, 938, 666, 111, 656, 283, 447, 619, 617, 720, 251, 299, 484, 631, 839, 772, 327, 275, 577, 155, 1, 128, 420, 132, 344, 781, 222, 760, 153, 934, 64, 134, 953, 186, 690, 322, 703, 755, 27, 169, 756, 319, 933, 102, 176, 509];
    $getUrl = function($id) {
        return "https://picsum.photos/id/$id/1080/720"; 
    };
    $p = new Pagination($data, 5);
    $x = $p->getData();
    $output = array();
    for($i = 0; $i < count($x); $i++) {
        array_push($output, "<img src='".$getUrl($x[$i])."'><br>");
    }
    $output = join("<br>", $output);
    $result = array(
        "data" => $output,
        "current_page" => $p->currentPage(),
        "total_pages" => $p->totalPages()
    );

    echo json(json_encode($result, JSON_UNESCAPED_SLASHES));
});