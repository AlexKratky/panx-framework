<?php
/*
 * @version     v0.1 (27-07-2018) [dd-mm-yyyy]
 * @link        https://alexkratky.cz            Author website
 * @link        https://tssoft.cz/CMD            Documentation
 * @author      Alex Kratky <info@alexkratky.cz>
 * @copyright   Copyright (c) 2018 Alex Kratky
 * @license     http://opensource.org/licenses/mit-license.php  MIT License
 */

/*

Basic setup

*/
$FILE_NAME = $argv[0];
$ARGS_COUNT = count($argv) - 1;
$ARGS = $argv;
$LOG = false;
$LOG_PROJECT = "";
$LOG_NAME = date("Y-m-d H:i:s") . ".log";

$PROGRAM_INFO = array(
                        "name" => "",
                        "version" => "v0.1 (27-07-2018) [dd-mm-yyyy]", 
                        "links" => array(
                            "https://alexkratky.cz            Author website",
                            "https://tssoft.cz/CMD            Documentation"
                        ), 
                        "author" => "Alex Kratky <info@alexkratky.cz>", 
                        "copyright" => "Copyright (c) ".date("Y", time())." Alex Kratky", 
                        "license" => "http://opensource.org/licenses/mit-license.php  MIT License"
                    );

array_shift($ARGS);
require(__DIR__.'/TS_COLOR.php');

/*
if( !function_exists ( "colorize" ) ) {
    require_once(__DIR__ . "/TS_COLOR.php");
}
*/
/*

Writing & Reading

*/

//LOG_X mean that if its called from e.g. info_msg it wont write it twice
function write($TEXT, $newLine = true, $LOG_X = true) {
    global $LOG;
    if($newLine) {
        echo $TEXT . "\n";
    } else {
        echo $TEXT;
    }
    if($LOG && $LOG_X) {
        writeLog($TEXT);
    }
}

//not be in log
function emptyLine() {
    echo "\n";
}

function read($TEXT, $ADD_COLON = true, $LOG_X = true) {
    global $LOG;
    if($ADD_COLON) {
        $x = readline($TEXT . ": ");
        if ($LOG) {
            writeLog("[READ]" . $TEXT . ": " . $x);
        }
        return $x;
    } else {
        $x = readline($TEXT);
        if ($LOG  && $LOG_X) {
            writeLog("[READ]" . $TEXT . " " . $x);
        }
        return $x;

    }
}

function error($TEXT) {
    global $LOG;
    //fatal error, need exit app
    emptyLine();
    write(colorize($TEXT, "red", "black"), true, false);
    emptyLine();
    write(colorize("Executing will be stoped.", "cyan", "black"), true, false);
    if ($LOG) {
        writeLog("[FATAL_ERROR]" . $TEXT);
    }
    exit();
}


function info_msg($TEXT) {
    global $LOG;
    write(colorize($TEXT, "cyan", "black"), true, false);
    if ($LOG) {
        writeLog("[INFO]" . $TEXT);
    }
}

function error_msg($TEXT) {
    global $LOG;
    write(colorize($TEXT, "red", "black"), true, false);
    if ($LOG) {
        writeLog("[ERROR]" . $TEXT);
    }
}

function displayInfo() {
    global $PROGRAM_INFO;
    write("Name: " . colorize($PROGRAM_INFO["name"], "cyan", "black"));
    write("Version: " . colorize($PROGRAM_INFO["version"], "cyan", "black"));
    for($i = 0; $i < count($PROGRAM_INFO["links"]); $i++) {
        write("Link: " . colorize($PROGRAM_INFO["links"][$i], "cyan", "black"));
    }
    write("Author: " . colorize($PROGRAM_INFO["author"], "cyan", "black"));
    write("Copyright: " . colorize($PROGRAM_INFO["copyright"], "cyan", "black"));
    write("License: " . colorize($PROGRAM_INFO["license"], "cyan", "black"));
}

/*

Settings things

*/

//check if dir exists before real path, if not then create it
dir_exists(__DIR__ . "/../settings/");

$SETTINGS_DIR = realpath(__DIR__ . "/../settings/");

$SETTING = readSetting("TS_CMD");
if($SETTING === false) {
    //no settings set, create new one
    initSetting("TS_CMD");
}

function readSetting($PROJECT, $FILE = "settings.json", $JSON = true) {
    global $SETTINGS_DIR;
    dir_exists($SETTINGS_DIR . "/" . $PROJECT);
    if(file_exists($SETTINGS_DIR."/".$PROJECT."/".$FILE)) {
        $S = file_get_contents($SETTINGS_DIR."/".$PROJECT."/".$FILE);
        if($S !== false) {
            if(!$JSON) {
                return $S;
            } else {
                $S = json_decode($S, true);
                if($S === null) {
                    error_msg("Settings (".$PROJECT."/".$FILE."): null");
                }
                return $S;
            }
        } else {
            return false;
        }
    } else {
        //no settings for project
        return false;
    }
}

function writeSetting($PROJECT, $SETTING, $FILE = "settings.json") {
    global $SETTINGS_DIR;
    dir_exists($SETTINGS_DIR . "/" . $PROJECT);
    if(file_put_contents($SETTINGS_DIR."/".$PROJECT."/".$FILE, json_encode($SETTING), LOCK_EX) === false) {
        error_msg("Failed to save settings(".$SETTINGS_DIR."/".$PROJECT."/".$FILE."): ");
        var_dump($SETTING);
    } else {
        info_msg("Settings saved to " . $PROJECT . "/" . $FILE . ".");
    }
}

function initSetting($PROJECT, $FILE = "settings.json") {
    writeSetting($PROJECT, array(), $FILE);
}

/*

Files & directories

*/
function dir_exists($dir) {
    //info_msg($dir);
    if(!file_exists($dir)) {
        info_msg("Folder '" . $dir . "' doesn't exists. Trying to create it.");   
        if(mkdir($dir)) {
            info_msg("Folder created.");
        } else {
            error("Can't create folder");
        }
    } else {
        return true;
    }
}


/*

Arrays

*/
function removeFromArray($array, $index) {
    $new = array();

    for($i=0; $i < count($array); $i++) {
        if($i == $index) 
            continue;
        array_push($new, $array[$i]);
    }

    return $new;
}

/*

Logs
* Log bude zapisovat vše, i read(), write(), ale funkce writeLog pouze zapíše do logu aniž by to uživatel viděl.

*/
function writeLog($TEXT) {
    global $LOG;
    global $LOG_PROJECT;
    global $LOG_NAME;
    if(file_put_contents(__DIR__ . "/../logs/".$LOG_PROJECT."/".$LOG_NAME, $TEXT . "\n", LOCK_EX | FILE_APPEND) === false) {
        $LOG = false;
        error_msg("Failed to write into log, logging turn off.");
    }
}

function enableLogging($PROJECT) {
    global $LOG;
    global $LOG_PROJECT;
    $LOG = true;
    $LOG_PROJECT = $PROJECT;
    if(!file_exists(__DIR__ . "/../logs/".$PROJECT) || !is_dir(__DIR__ . "/../logs/".$PROJECT)) {
        if(!mkdir(__DIR__ . "/../logs/".$PROJECT)) {
            error("Failed to create directory: " . __DIR__ . "/../logs/".$PROJECT);
        }
    }
    writeLog("[LOG]Log started. " . date("d.m.Y H:i:s"));
}

function disableLogging() {
    global $LOG;
    $LOG = false;
}

/*

Temp files

*/
function tempFileExist($NAME) {
    return file_exists(__DIR__."/../temp/".$NAME);
}

function tempFileWrite($NAME, $TEXT) {
    if(file_put_contents(__DIR__."/../temp/".$NAME, $TEXT, LOCK_EX) === false) {
        return false;
    } else {
        return true;
    }
}

function tempFileRead($NAME) {
    //check if === false
    return file_get_contents(__DIR__."/../temp/".$NAME);
}

/*

Cache

*/
//Always call this command on start of TS_CMD program, you can also provide this function in every work with cache, but user shouldn't do anything with cache files, so its not necessary
function cacheDirectoryInit($PROJECT) {
    if(!file_exists(__DIR__ . "/../cache/" . $PROJECT)) {
        if(mkdir(__DIR__ . "/../cache/" . $PROJECT )) {
            info_msg("Cache folder for $PROJECT created");
        } else {
            error("Failed to create cache folder for " . $PROJECT);
        }
    }
}

function cacheFileExist($PROJECT, $NAME) {
    return file_exists(__DIR__ . "/../cache/" . $PROJECT . "/" . $NAME);
}

function cacheFileWrite($PROJECT, $NAME, $TEXT) {
    if (file_put_contents(__DIR__ . "/../cache/" . $PROJECT . "/" . $NAME, $TEXT, LOCK_EX) === false) {
        return false;
    } else {
        return true;
    }
}

function cacheFileRead($PROJECT, $NAME) {
    //check if === false
    return file_get_contents(__DIR__ . "/../cache/" . $PROJECT . "/" . $NAME);
}


