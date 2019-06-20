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
        file_put_contents($_SERVER['DOCUMENT_ROOT']. "/../cache/" . $name, json_encode($data));
    }

    /**
     * Obtain the data of specified variable.
     * @param string $name The name of variable.
     * @param int $cacheTime The cache live in seconds, if you pass null, then the default time will be used.
     * @return mixed|false Returns false if there is no cache with that variable name or when the cache is expired, else returns decoded data using json_decode()
    */
    public static function get(string $name, ?int $cacheTime = null) {
        if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/../cache/" . $name) && filectime($_SERVER['DOCUMENT_ROOT'] . "/../cache/" . $name) + ($cacheTime == null ? self::CACHE_TIME : $cacheTime) > time()) {
            return json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/../cache/" . $name), true);
        } else {
            return false;
        }
    }

    /**
     * Destroy specified cache.
     * @param string $name The name of variable.
     */
    public static function destroy(string $name): boolean {
        return unlink($_SERVER['DOCUMENT_ROOT']. "/../cache/" . $name);
    }
}