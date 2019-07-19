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
        if($GLOBALS["CONFIG"]["basic"]["APP_MULTI_LANGUAGE_POSTS"] == "1") {
            if(Route::getValue('LANGUAGE') === false) {
                $lang = $GLOBALS["request"]->getMostPreferredLanguage();
                if($lang === null) {
                    $lang = "en";
                }
                if(file_exists($_SERVER['DOCUMENT_ROOT']."/../template/posts/$lang/".Route::getValue("ID").".php")) {
                    redirect('/post/'.$lang.'/'.Route::getValue('ID'));
                } else if (file_exists($_SERVER['DOCUMENT_ROOT']."/../template/posts/".$GLOBALS["CONFIG"]["basic"]["APP_PRIMARY_POST_LANGUAGE"]."/".Route::getValue("ID").".php")) {
                    redirect('/post/'.$GLOBALS["CONFIG"]["basic"]["APP_PRIMARY_POST_LANGUAGE"].'/'.Route::getValue('ID'));
                }
            }
        }
        if(!file_exists($_SERVER['DOCUMENT_ROOT']."/../template/posts/" . (Route::getValue('LANGUAGE') === false ? '' : Route::getValue('LANGUAGE').'/') . Route::getValue("ID").".php")) {
            require $_SERVER['DOCUMENT_ROOT']."/../template/" . Route::searchError(Route::ERROR_NOT_FOUND);
            return;
        }
        foreach (self::$HEADERS as $header) {
            require $_SERVER['DOCUMENT_ROOT']. "/../template/" . $header;
        }

        require $_SERVER['DOCUMENT_ROOT']."/../template/posts/" . (Route::getValue('LANGUAGE') === false ? '' : Route::getValue('LANGUAGE').'/') . Route::getValue("ID").".php";

        foreach (self::$FOOTERS as $footer) {
            require $_SERVER['DOCUMENT_ROOT']. "/../template/" . $footer;
        }
    }

    /**
     * Returns the title of post (From .info file or from first headline).
     * @param string $post The post file name without extension.
     * @param string|null $language The used language of post, can be null, then the language will be passed from URL, if URL do not contains {LANGUAGE}, then it will use the most preferred language or no language.
     * @return string The title of post.
     */
    public static function getTitle($post, $language = null) {
        if($language === null) {
            if(Route::getValue('LANGUAGE') !== false) {
                $language = Route::getValue('LANGUAGE');
            } else {
                $language = $GLOBALS["request"]->getMostPreferredLanguage();
            }
        }
        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/../template/posts/$language/" . $post . ".php") && !file_exists($_SERVER['DOCUMENT_ROOT'] . "/../template/posts/".$GLOBALS["CONFIG"]["basic"]["APP_PRIMARY_POST_LANGUAGE"]."/" . $post . ".php") && !file_exists($_SERVER['DOCUMENT_ROOT'] . "/../template/posts/" . $post . ".php")) {
            return Route::ERROR_NOT_FOUND;
        }
        $info = self::loadInfo($post, $language);
        if($info !== false) {
            if(isset($info["title"]))
                return $info["title"];
        }
        if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/../template/posts/$language/" . $post . ".php")) {
            preg_match('/<h[1-6]>(.+)<\/h[1-6]>/', file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/../template/posts/$language/" . $post . ".php"), $matches);
        } elseif(file_exists($_SERVER['DOCUMENT_ROOT'] . "/../template/posts/".$GLOBALS["CONFIG"]["basic"]["APP_PRIMARY_POST_LANGUAGE"]."/" . $post . ".php")) {
            preg_match('/<h[1-6]>(.+)<\/h[1-6]>/', file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/../template/posts/".$GLOBALS["CONFIG"]["basic"]["APP_PRIMARY_POST_LANGUAGE"]."/" . $post . ".php"), $matches);
        }  else {
            preg_match('/<h[1-6]>(.+)<\/h[1-6]>/', file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/../template/posts/" . $post . ".php"), $matches);
        }
        return $matches[1];
    }

    /**
     * Lists all posts and saves them to cache for 60s.
     * @param string|null $topic The posts topic. Leave empty to global topic. (If is topic sets, then load only posts with specified topic).
     * @param string|null $language The used language of post, can be null, then the language will be passed from URL, if URL do not contains {LANGUAGE}, then it will use the most preferred language or no language.     * 
     * @return array Array of all posts. All posts are sorted using your choosen sorting method. Each element have attribute 'name' and 'created_at'
     */
    public static function listPosts($topic = null, $language = null) {
        if($language === null) {
            if(Route::getValue('LANGUAGE') !== false) {
                $language = Route::getValue('LANGUAGE');
            } else {
                //$language = $GLOBALS["request"]->getMostPreferredLanguage();
                $language = $GLOBALS["CONFIG"]["basic"]["APP_PRIMARY_POST_LANGUAGE"];
            }
        }
        if($topic === null) {
            $p = Cache::get("posts-$language", 60);
            if($p === false) {
                $p = Cache::get("posts-".$GLOBALS["request"]->getMostPreferredLanguage(), 60);
                    if($p === false)
                        $p = Cache::get("posts", 60);
            }
        } else {
            $p = Cache::get("posts-$language-$topic", 60);
            if($p === false) {
                $p = Cache::get("posts".$GLOBALS["request"]->getMostPreferredLanguage()."-$topic", 60);
                if($p === false)
                    $p = Cache::get("posts-$topic", 60);
            }
        }
        if($p !== false) {
            Logger::log("Using cached posts");
            return $p;
        }
        if(!file_exists($_SERVER['DOCUMENT_ROOT']."/../template/posts/"))
            return array();

        if(file_exists($_SERVER['DOCUMENT_ROOT']."/../template/posts/$language/")) {
            $f = scandir($_SERVER['DOCUMENT_ROOT']."/../template/posts/$language/");
        } elseif(file_exists($_SERVER['DOCUMENT_ROOT']."/../template/posts/".$GLOBALS["request"]->getMostPreferredLanguage()."/")) {
            $f = scandir($_SERVER['DOCUMENT_ROOT']."/../template/posts/".$GLOBALS["request"]->getMostPreferredLanguage()."/");
            $language = $GLOBALS["request"]->getMostPreferredLanguage();
        } else {
            $f = scandir($_SERVER['DOCUMENT_ROOT']."/../template/posts/");
            $language = null;
        }
        $f_arr = array();

        foreach($f as $file) {
            if($file == "." || $file == "..") continue;
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            if($ext == "info") {
                continue;
            }
            if($topic !== null) {
                $i = self::loadInfo(basename($file, '.'.$ext));
                
                if($i === false) {
                    continue; //no info file, no topic sets
                }
                if(!isset($i["topic"])) {
                    continue;
                }
                if(trim($i["topic"]) != strtolower($topic)) {
                    continue;
                }
            }
            if(pathinfo($file, PATHINFO_EXTENSION) != "php") continue;
            array_push($f_arr, array("name" => basename($file, ".php"), "created_at" => filectime($_SERVER['DOCUMENT_ROOT']."/../template/posts/$file")));

        }
        //sort array by date
        

        usort($f_arr, 'self::compareTime');
        if($topic === null) {
            if($language !== null) {
                Cache::save("posts-$language", $f_arr);
            } else {
                Cache::save("posts", $f_arr);
            }
        } else {
             if($language !== null) {
                Cache::save("posts-$language-$topic", $f_arr);
            } else {
                Cache::save("posts-$topic", $f_arr);
            }

        }
        Logger::log("Cache saved");
        return $f_arr;

    }

    /**
     * Deletes the post specified by URL (Route::getValue("ID")).
     * @param string|null $language The used language of post, can be null, then the language will be passed from URL, if URL do not contains {LANGUAGE}, then it will use the most preferred language or no language.     * 
     */
    public static function deletePost($language = null) {
        if($language === null) {
            if(Route::getValue('LANGUAGE') !== false) {
                $language = Route::getValue('LANGUAGE');
            } else {
                $language = $GLOBALS["request"]->getMostPreferredLanguage();
            }
        }
        if(!file_exists($_SERVER['DOCUMENT_ROOT']."/../template/posts/$language/".Route::getValue("ID").".php") && !file_exists($_SERVER['DOCUMENT_ROOT']."/../template/posts/".$GLOBALS["CONFIG"]["basic"]["APP_PRIMARY_POST_LANGUAGE"]."/".Route::getValue("ID").".php") && !file_exists($_SERVER['DOCUMENT_ROOT']."/../template/posts/".Route::getValue("ID").".php")) {
            require $_SERVER['DOCUMENT_ROOT']."/../template/" . Route::searchError(Route::ERROR_NOT_FOUND);
            return;
        }
        if(file_exists($_SERVER['DOCUMENT_ROOT']."/../template/posts/$language/".Route::getValue("ID").".php")) {
            $i = self::loadInfo(Route::getValue("ID"), $language);

            unlink($_SERVER['DOCUMENT_ROOT']."/../template/posts/$language/".Route::getValue("ID").".php");
            if(file_exists(unlink($_SERVER['DOCUMENT_ROOT'] . "/../template/posts/$language/" . Route::getValue("ID") . ".info")))
                unlink($_SERVER['DOCUMENT_ROOT'] . "/../template/posts/$language/" . Route::getValue("ID") . ".info");

            Cache::destroy("posts-$language");
            if(isset($i["topic"])) {
                Cache::destroy("posts-$language-" . trim($i["topic"]));
            }
        } elseif(file_exists($_SERVER['DOCUMENT_ROOT']."/../template/posts/".$GLOBALS["CONFIG"]["basic"]["APP_PRIMARY_POST_LANGUAGE"]."/".Route::getValue("ID").".php")) {
            $i = self::loadInfo(Route::getValue("ID"), $GLOBALS["CONFIG"]["basic"]["APP_PRIMARY_POST_LANGUAGE"]);

            unlink($_SERVER['DOCUMENT_ROOT']."/../template/posts/".$GLOBALS["CONFIG"]["basic"]["APP_PRIMARY_POST_LANGUAGE"]."/".Route::getValue("ID").".php");
            if(file_exists(unlink($_SERVER['DOCUMENT_ROOT'] . "/../template/posts/".$GLOBALS["CONFIG"]["basic"]["APP_PRIMARY_POST_LANGUAGE"]."/" . Route::getValue("ID") . ".info")))
                unlink($_SERVER['DOCUMENT_ROOT'] . "/../template/posts/".$GLOBALS["CONFIG"]["basic"]["APP_PRIMARY_POST_LANGUAGE"]."/" . Route::getValue("ID") . ".info");

            Cache::destroy("posts-".$GLOBALS["CONFIG"]["basic"]["APP_PRIMARY_POST_LANGUAGE"]);
            if(isset($i["topic"])) {
                Cache::destroy("posts-".$GLOBALS["CONFIG"]["basic"]["APP_PRIMARY_POST_LANGUAGE"]."-" . trim($i["topic"]));
            }
        } else {
            $i = self::loadInfo(Route::getValue("ID"));
            unlink($_SERVER['DOCUMENT_ROOT']."/../template/posts/".Route::getValue("ID").".php");
            if(file_exists(unlink($_SERVER['DOCUMENT_ROOT'] . "/../template/posts/" . Route::getValue("ID") . ".info")))
                unlink($_SERVER['DOCUMENT_ROOT'] . "/../template/posts/" . Route::getValue("ID") . ".info");

            Cache::destroy("posts");
            if(isset($i["topic"])) {
                Cache::destroy("posts-" . trim($i["topic"]));
            }
        }
    }

    /**
     * Load info about post from .info file.
     * @param string $post The name of post.
     * @param string|null $language The used language of post, can be null, then the language will be passed from URL, if URL do not contains {LANGUAGE}, then it will use the most preferred language or no language.
     * @return false|array Returns FALSE if the post does not have .info file, otherwise returns the array containing the post's info (element => key).
     */
    public static function loadInfo($post, $language = null) {
        if($language === null) {
            if(Route::getValue('LANGUAGE') !== false) {
                $language = Route::getValue('LANGUAGE');
            } else {
                $language = $GLOBALS["request"]->getMostPreferredLanguage();
            }
        }

        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/../template/posts/$language/" . $post . ".php") && !file_exists($_SERVER['DOCUMENT_ROOT'] . "/../template/posts/".$GLOBALS["CONFIG"]["basic"]["APP_PRIMARY_POST_LANGUAGE"]."/" . $post . ".php") &&  !file_exists($_SERVER['DOCUMENT_ROOT'] . "/../template/posts/" . $post . ".php")) {
            return Route::ERROR_NOT_FOUND;
        }
        if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/../template/posts/" . $post . ".info")) {
            $f = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/../template/posts/" . $post . ".info");
            $f = explode(PHP_EOL, $f);
            $info = [];
            foreach($f as $line) {
                if(isset($line[0]) && $line[0] == "#") continue; //skip comments
                $line = explode(': ', $line, 2);
                $info[strtolower($line[0])] = trim($line[1]);
            }
            return $info;
        } else if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/../template/posts/$language/" . $post . ".info")) {
            $f = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/../template/posts/$language/" . $post . ".info");
            $f = explode(PHP_EOL, $f);
            $info = [];
            foreach($f as $line) {
                if(isset($line[0]) && $line[0] == "#") continue; //skip comments
                $line = explode(': ', $line, 2);
                $info[strtolower($line[0])] = trim($line[1]);
            }
            return $info;
        } else if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/../template/posts/".$GLOBALS["CONFIG"]["basic"]["APP_PRIMARY_POST_LANGUAGE"]."/" . $post . ".info")) {
            $f = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/../template/posts/".$GLOBALS["CONFIG"]["basic"]["APP_PRIMARY_POST_LANGUAGE"]."/" . $post . ".info");
            $f = explode(PHP_EOL, $f);
            $info = [];
            foreach($f as $line) {
                if(isset($line[0]) && $line[0] == "#") continue; //skip comments
                $line = explode(': ', $line, 2);
                $info[strtolower($line[0])] = trim($line[1]);
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