<?php
class Cache {

    const CACHE_TIME = 10; //s


    public static function init() {
        if(!file_exists($_SERVER['DOCUMENT_ROOT'] . "/../cache/")) {
            if(!mkdir($_SERVER['DOCUMENT_ROOT'] . "/../cache/"))
                Logger::log("Failed to create cache folder");
        }
    }

    public static function save($name, $data) {
        file_put_contents($_SERVER['DOCUMENT_ROOT']. "/../cache/" . $name, json_encode($data));
    }

    public static function get($name, $cacheTime = null) {
        if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/../cache/" . $name) && filectime($_SERVER['DOCUMENT_ROOT'] . "/../cache/" . $name) + ($cacheTime == null ? self::CACHE_TIME : $cacheTime) > time()) {
            return json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/../cache/" . $name), true);
        } else {
            return false;
        }
    }

    public static function destroy($name) {
        return unlink($_SERVER['DOCUMENT_ROOT']. "/../cache/" . $name);
    }
}