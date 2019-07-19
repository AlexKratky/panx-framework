<?php
require $PATH . "/vendor/autoload.php";

if (!isset($ARGS[2])) {error("You need to specify path to post source.");}
if (!file_exists($PATH . $ARGS[2])) {
    error("File doesnt exists. " . $PATH . $ARGS[2]);
}

$Parsedown = new ParsedownExtra();

preg_match("/#+?\ (.+?)\n/", file_get_contents($PATH . $ARGS[2]), $matches);
$f = $PATH . $ARGS[2];
if (!isset($matches[1])) {
    $matches[1] = basename($f, ".md");
}
info_msg("Current title: " . $matches[1]);
write("Do you want to use custom title? Enter a new one or press enter to keep current.");
$title = read("");
if ($title == "") {
    $title = $matches[1];
}
if (!file_exists($PATH . "/template/posts/")) {
    if (!mkdir($PATH . "/template/posts/")) {
        error("Failed to create directory " . $PATH . "/template/posts/");
    }

}

file_put_contents($PATH . "/template/posts/" . basename($f, ".md") . ".php", "<title>" . $CONFIG["basic"]["APP_NAME"] . " | " . $title . " </title>" . $Parsedown->text(file_get_contents($f)));


if (!file_exists($PATH . "/routes/posts.php")) {
    file_put_contents($PATH . "/routes/posts.php", "<?php
Route::set('/post/{ID}', function () {
    Post::loadPost();
});
Route::set('/post/{LANGUAGE}/{ID}', function () {
    Post::loadPost();
});
");
}

info_msg("Enter route indetifier, or press enter to use 'post' (/post/{ID})");

info_msg("Post created successfully, you can view it on: " . $CONFIG["basic"]["APP_URL"] . "post/" . basename($f, ".md"));