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

declare(strict_types=1);

class Logger {

    /**
     * Writes data to log file.
     * @param string $text The text to be written.
     * @param string $file The name of log file, default is main.log.
     * @param string|null $dir The base path, if sets to null: $_SERVER['DOCUMENT_ROOT'] . "/..".
     * @return false|int This function returns the number of bytes that were written to the log file, or FALSE on failure.
     */
    public static function log(string $text, string $file = "main.log", ?string $dir = null) {
        $sizeCheck = Cache::get("__Logger__sizeChecked__$file.info", 60);
        if($dir === null) {
            $dir = $_SERVER['DOCUMENT_ROOT'] . "/..";
        }

        if($sizeCheck === false && file_exists($dir . "/logs/" . $file)) {
            //Check if the log size if more then 100 MB, if yes, tar.gz it. 102400000
            if(filesize( $dir . "/logs/" . $file) > 102400000) {
                $t = time();
                $a = new PharData(realpath("$dir/logs/") . "/$file.$t.tar");


                $a->addFile(realpath("$dir/logs/$file"));

                $a->compress(Phar::GZ);

                unlink("$dir/logs/$file.$t.tar");
                unlink("$dir/logs/$file");
            }
            Cache::save("__Logger__sizeChecked__$file.info", "t");
        }
        $text .= ( isset($GLOBALS["request"]) ? " | " . ($GLOBALS["request"]->getClientID() ?? null): '');
        return file_put_contents ('panx://'. $dir . "/logs/" . $file , "[".date("d/m/Y H:i:s")."] ".$text . " -  ".debug_backtrace()[0]['file']."@" . debug_backtrace()[1]['function'] ."() \r\n", FILE_APPEND | LOCK_EX);
    }

    public static function write(string $text, string $file = "main.log", ?string $dir = null) {self::log($text, $file, $dir);}
}
