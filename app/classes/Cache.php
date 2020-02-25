<?php
/**
 * @name Cache.php
 * @link https://alexkratky.cz                          Author website
 * @link https://panx.eu/docs/                          Documentation
 * @link https://github.com/AlexKratky/panx-framework/  Github Repository
 * @author Alex Kratky <info@alexkratky.cz>
 * @copyright Copyright (c) 2019 Alex Kratky
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @description Class to work with cache. Part of panx-framework.
 */

declare(strict_types=1);

class Cache {

    /**
     * @var int Default cache live in seconds.
     */
    const CACHE_TIME = 10;

    /**
     * Creates /cache/ folder.
     */
    public static function init(): void {
        if(!file_exists($_SERVER['DOCUMENT_ROOT'] . "/../cache/")) {
            if(!mkdir($_SERVER['DOCUMENT_ROOT'] . "/../cache/"))
                Logger::log("Failed to create cache folder");
        }
    }

    /**
     * Saves data to cache file.
     * @param string $name The name of variable.
     * @param mixed $data The data that will be saved. Data will be edited by json_encode()
    */
    public static function save(string $name, $data): void {
        file_put_contents('panx://'.$_SERVER['DOCUMENT_ROOT']. "/../cache/" . $name, json_encode($data));
    }

    /**
     * Obtain the data of specified variable.
     * @param string $name The name of variable.
     * @param int $cacheTime The cache live in seconds, if you pass null, then the default time will be used.
     * @return mixed|false Returns false if there is no cache with that variable name or when the cache is expired, else returns decoded data using json_decode()
    */
    public static function get(string $name, ?int $cacheTime = null) {
        if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/../cache/" . $name) && filectime($_SERVER['DOCUMENT_ROOT'] . "/../cache/" . $name) + ($cacheTime == null ? self::CACHE_TIME : $cacheTime) > time()) {
            return json_decode(file_get_contents('panx://'.$_SERVER['DOCUMENT_ROOT'] . "/../cache/" . $name), true);
        } else {
            return false;
        }
    }

    /**
     * Destroy specified cache.
     * @param string $name The name of variable.
     */
    public static function destroy(string $name): bool {
        if(file_exists($_SERVER['DOCUMENT_ROOT']. "/../cache/" . $name))
            return unlink($_SERVER['DOCUMENT_ROOT']. "/../cache/" . $name);
        else return false;
    }

    /**
     * Clear all cache files older then $time.
     * Can be called using php panx-worker clear cache old.
     * @param string $dir The basedir, used when called from terminal.
     * @param int $time The time in seconds, default value is 86400 (1 day).
     */
    public static function clearUnused($dir = null, $time = 86400) {
        if($dir === null) {
            $dir = $_SERVER['DOCUMENT_ROOT'] . "/..";
        }
        $c = scandir($dir . "/cache/");
        foreach ($c as $f) {
            if($f == "." || $f == "..") continue;
            if(filemtime($dir . "/cache/" . $f) + $time < time()) {
                unlink($dir . "/cache/" . $f);
            }
        }
    }

    /**
     * Clear all cache files. Used in updates.
     * Can be called using php panx-worker clear cache
     * @param string $dir The basedir, used when called from terminal.
     */
    public static function clearAll($dir = null) {
        if($dir === null) {
            $dir = $_SERVER['DOCUMENT_ROOT'] . "/..";
        }
        $c = scandir($dir . "/cache/");
        foreach ($c as $f) {
            if($f == "." || $f == "..") continue;
            unlink($dir . "/cache/" . $f);
        }
        //Logger::log("Cache was cleared.", "main.log", $dir);
    }
}