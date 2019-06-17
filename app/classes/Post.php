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
        if(!file_exists($_SERVER['DOCUMENT_ROOT']."/../template/posts/".Route::getValue("ID").".php")) {
            require $_SERVER['DOCUMENT_ROOT']."/../template/" . Route::searchError(Route::ERROR_NOT_FOUND);
            return;
        }
        foreach (self::$HEADERS as $header) {
            require $_SERVER['DOCUMENT_ROOT']. "/../template/" . $header;
        }

        require $_SERVER['DOCUMENT_ROOT']."/../template/posts/".Route::getValue("ID").".php";

        foreach (self::$FOOTERS as $footer) {
            require $_SERVER['DOCUMENT_ROOT']. "/../template/" . $footer;
        }
    }

    public static function getTitle($post) {
        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/../template/posts/" . $post . ".php")) {
            return Route::ERROR_NOT_FOUND;
        }
        $info = self::loadInfo($post);
        if($info !== false) {
            if(isset($info["title"]))
                return $info["title"];
        }
        preg_match('/<h[1-6]>(.+)<\/h[1-6]>/', file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/../template/posts/" . $post . ".php"), $matches);
        return $matches[1];
    }

    public static function listPosts() {
        $p = Cache::get("posts", 60);
        if($p !== false) {
            Logger::log("Using cached posts");
            return $p;
        }
        $f = scandir($_SERVER['DOCUMENT_ROOT']."/../template/posts/");
        $f_arr = array();

        foreach($f as $file) {
            if($file == "." || $file == "..") continue;
            if(pathinfo($file, PATHINFO_EXTENSION) != "php") continue;
            array_push($f_arr, array("name" => basename($file, ".php"), "created_at" => filectime($_SERVER['DOCUMENT_ROOT']."/../template/posts/$file")));

        }
        //sort array by date
        

        usort($f_arr, 'self::compareTime');
        Cache::save("posts", $f_arr);
        Logger::log("Cache saved");
        return $f_arr;

    }

    public static function deletePost() {
        if(!file_exists($_SERVER['DOCUMENT_ROOT']."/../template/posts/".Route::getValue("ID").".php")) {
            require $_SERVER['DOCUMENT_ROOT']."/../template/" . Route::searchError(Route::ERROR_NOT_FOUND);
            return;
        }

        unlink($_SERVER['DOCUMENT_ROOT']."/../template/posts/".Route::getValue("ID").".php");
        if(file_exists(unlink($_SERVER['DOCUMENT_ROOT'] . "/../template/posts/" . Route::getValue("ID") . ".info")))
            unlink($_SERVER['DOCUMENT_ROOT'] . "/../template/posts/" . Route::getValue("ID") . ".info");

        Cache::destroy("posts");
    }

    public static function loadInfo($post) {
        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/../template/posts/" . $post . ".php")) {
            return Route::ERROR_NOT_FOUND;
        }
        if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/../template/posts/" . $post . ".info")) {
            $f = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/../template/posts/" . $post . ".info");
            $f = explode(PHP_EOL, $f);
            $info = [];
            foreach($f as $line) {
                if(isset($line[0]) && $line[0] == "#") continue; //skip comments
                $line = explode(': ', $line, 2);
                $info[strtolower($line[0])] = $line[1];
            }
            return $info;
        } else {
            return false;
        }
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