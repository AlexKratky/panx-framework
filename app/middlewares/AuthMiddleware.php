<?php
class AuthMiddleware {
    public static function handle() {
        if(isset($_SESSION["username"]) && isset($_SESSION["password"])) {
            return true;
        } else {
            return false;
        }
    }

    public static function error() {
        echo "NOT AUTHENTICATED";
        return -1;
    }
}