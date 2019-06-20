<?php
/**
 * @name Logger.php
 * @link https://alexkratky.cz                          Author website
 * @link https://panx.eu/docs/                          Documentation
 * @link https://github.com/AlexKratky/panx-framework/  Github Repository
 * @author Alex Kratky <info@alexkratky.cz>
 * @copyright Copyright (c) 2019 Alex Kratky
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @description Class to work with logs. Part of panx-framework.
 */
class Logger {

    /**
     * Writes data to log file.
     * @param string $text The text to be written.
     * @param string $file The name of log file, default is main.log.
     * @return false|int This function returns the number of bytes that were written to the log file, or FALSE on failure.
     */
    public static function log($text, $file = "main.log") {
        return file_put_contents ( $_SERVER['DOCUMENT_ROOT'] . "/../logs/" . $file , "[".date("d/m/Y H:i:s")."] ".$text . " -  ".debug_backtrace()[0]['file']."@" . debug_backtrace()[1]['function'] ."() \r\n", FILE_APPEND | LOCK_EX);
    }
}
