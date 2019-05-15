<?php
class Post {
    private static $HEADERS = array("header.php");
    private static $FOOTERS = array("footer.php");

    public static function setHeaders($headers) {
        self::$HEADERS = $headers;
    }

    public static function setFooters($footers) {
        self::$FOOTERS = $footers;
    }

    public static function loadPost() {
        foreach (self::$HEADERS as $header) {
            require __DIR__. "/template/" . $header;
        }

        require __DIR__."/template/posts/".Route::getValue("ID").".php";

        foreach (self::$FOOTERS as $footer) {
            require __DIR__. "/template/" . $footer;
        }
    }

    public static function listPosts() {
        $f = scandir(__DIR__."/template/posts/");
        $f_arr = array();

        foreach($f as $file) {
            array_push($f_arr, array($file, filectime(__DIR__."/template/posts/$file")));

        }
//sort array by date
        return $f_arr;
    }
}