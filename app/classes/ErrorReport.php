<?php
class ErrorReport {
    protected static $errors = array();

    public static function add($topic, $error_msg) {
        if(!isset(self::$errors[$topic])) {
            self::$errors[$topic] = array();
        }
        array_push(self::$errors[$topic], $error_msg);
    }

    // saves errors to the session
    public static function save($topic) {
        if(isset(self::$errors[$topic])) {
            $_SESSION["__error__$topic"] = json_encode(self::$errors[$topic]);
        }
    }

    // return & delete
    public static function get($topic) {
        if(isset($_SESSION["__error__$topic"])) {
            $e = json_decode($_SESSION["__error__$topic"], true);
            self::delete($topic);
            return $e;
        } else {
            return (self::$errors[$topic] ?? null);
        }
    }

    // delete
    public static function delete($topic) {
        if(isset($_SESSION["__error__$topic"])) {
            unset($_SESSION["__error__$topic"]);
        }
    }

    // return
    public static function show($topic) {
        if(isset($_SESSION["__error__$topic"])) {
            return json_decode($_SESSION["__error__$topic"], true);
        } else {
            return (self::$errors[$topic] ?? null);
        }
    }

    // check if errors of topic exists
    public static function hasErrorsExists($topic) {
        if(isset($_SESSION["__error__$topic"]) || isset(self::$errors[$topic])) {
            return true;
        } 
        return false;
    }
}