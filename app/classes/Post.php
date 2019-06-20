<?php
/**
 * @name Post.php
 * @link https://alexkratky.cz                          Author website
 * @link https://panx.eu/docs/                          Documentation
 * @link https://github.com/AlexKratky/panx-framework/  Github Repository
 * @author Alex Kratky <info@alexkratky.cz>
 * @copyright Copyright (c) 2019 Alex Kratky
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @description Class to work with post. Part of panx-framework.
 */

class Post {
    /**
     * @var array The array of all headers files.
     */
    private static $HEADERS = array();
    /**
     * @var array The array of all footers files.
     */
    private static $FOOTERS = array();
    /**
     * @var int The sorting of posts. Use SORT_DESC or SORT_ASC constants.
     */
    private static $SORTING = SORT_DESC;

    /**
     * Sets the headers files.
     * @param array $headers Headers.
     */
    public static function setHeaders($headers) {
        self::$HEADERS = $headers;
    }

    /**
     * Sets the footers files.
     * @param array $footers Footers.
     */
    public static function setFooters($footers) {
        self::$FOOTERS = $footers;
    }

    /**
     * Sets the sorting of posts.
     * @param array $sorting The sorting of posts. Use SORT_DESC or SORT_ASC constants.
     */
    public static function setSorting($sorting) {
        self::$SORTING = $sorting;
    }

    /**
     * Loads (requires) the post specified by URL (Route::getValue("ID")).
     */
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

    /**
     * Returns the title of post (From .info file or from first headline).
     * @return string The title of post.
     */
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

    /**
     * Lists all posts and saves them to cache for 60s.
     * @return array Array of all posts. All posts are sorted using your choosen sorting method. Each element have attribute 'name' and 'created_at'
     */
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

    /**
     * Deletes the post specified by URL (Route::getValue("ID")).
     */
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

    /**
     * Load info about post from .info file.
     * @param string $post The name of post.
     * @return false|array Returns FALSE if the post does not have .info file, otherwise returns the array containing the post's info (element => key).
     */
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

    /**
     * Function used for sorting posts. 
     */
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