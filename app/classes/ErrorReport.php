<?php
class ErrorReport {
    protected static $errors = array();

    public static function add($topic, $error_msg) {
        if(!isset(self::$errors[$topic])) {
            self::$errors[$topic] = array();
        }
        array_push(self::$errors[$topic], $error_msg);
    }

    // return & delete
    public static function get($topic) {

    }

    // return & delete all
    public static function getAll($topic) {

    }

    // delete
    public static function delete($topic) {

    }

    // delete all
    public static function deleteAll($topic) {

    }

    // return
    public static function show($topic) {

    }

    // return all
    public static function showAll($topic) {

    }
}