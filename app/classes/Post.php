<?php
class Post {
    private static $HEADERS = array();
    private static $FOOTERS = array();
    private static $SORTING = SORT_DESC;

    public static function setHeaders($headers) {
        self::$HEADERS = $headers;
    }

    public static function setFooters($footers) {
        self::$FOOTERS = $footers;
    }

    public static function setSorting($sorting) {
        self::$SORTING = $sorting;
    }

    public static function loadPost() {
        if(!file_exists(__DIR__."/../../template/posts/".Route::getValue("ID").".php")) {
            require __DIR__."/../../template/" . Route::searchError(Route::ERROR_NOT_FOUND);
        }
        foreach (self::$HEADERS as $header) {
            require __DIR__. "/../../template/" . $header;
        }

        require __DIR__."/../../template/posts/".Route::getValue("ID").".php";

        foreach (self::$FOOTERS as $footer) {
            require __DIR__. "/../../template/" . $footer;
        }
    }

    public static function listPosts() {
        $f = scandir(__DIR__."/../../template/posts/");
        $f_arr = array();

        foreach($f as $file) {
            if($file == "." || $file == "..") continue;
            array_push($f_arr, array("name" => basename($file, ".php"), "created_at" => filectime(__DIR__."/../../template/posts/$file")));

        }
        //sort array by date
        

        usort($f_arr, 'self::compareTime');
        return $f_arr;

    }

    private static function compareTime($a, $b)
    {
        //From newest to oldest
        if(self::$SORTING == SORT_DESC) {
            return $b["created_at"] - $a["created_at"];
        } elseif(self::$SORTING == SORT_ASC) {
            return $a["created_at"] - $b["created_at"];
        }
    }

}