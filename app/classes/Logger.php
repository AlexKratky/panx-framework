<?php
class Logger {
    public static function log($text, $file = "main.log") {
        file_put_contents ( $_SERVER['DOCUMENT_ROOT'] . "/../logs/" . $file , "[".date("d/m/Y H:i:s")."] ".$text . " -  ".debug_backtrace()[0]['file']."@" . debug_backtrace()[1]['function'] ."() \r\n", FILE_APPEND | LOCK_EX);
        
    }
}
