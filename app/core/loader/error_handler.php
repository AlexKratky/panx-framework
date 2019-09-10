<?php
/**
 * Error handler used in loader.php
 */

function errorHandler($errno, $errstr, $errfile, $errline)
{
    global $CONFIG;
    if (!(error_reporting() & $errno)) {
        return false;
    }

    if ($CONFIG["debug"]["DEBUG_LOG_ERR"] == "1") {
        Logger::log("[$errno] $errstr in $errfile at line $errline", "errors.log");
    }

    switch ($errno) {
        case E_USER_ERROR:
            if (!empty($CONFIG["database"]["DB_HOST"]) && $CONFIG["debug"]["DEBUG_SAVE"] == "1") {
                db::query("INSERT INTO debug_errors (`ERR_LEVEL`, `ERR_MESSAGE`, `ERR_FILE`, `ERR_LINE`) VALUES (?, ?, ?, ?);", array('ERROR', $errstr, $errfile, $errline));
            }

            if ($CONFIG["debug"]["DEBUG_PRINT_ERR"] == "1") {
                echo "<b>ERROR</b> [$errno] $errstr<br />\n";
                echo "  Fatal error on line $errline in file <a href='file:///$errfile:$errline'>$errfile</a>";
                echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
                echo "Aborting...<br />\n";
            }

            exit(1);
            break;

        case E_USER_WARNING:
            if (!empty($CONFIG["database"]["DB_HOST"]) && $CONFIG["debug"]["DEBUG_SAVE"] == "1") {
                db::query("INSERT INTO debug_errors (`ERR_LEVEL`, `ERR_MESSAGE`, `ERR_FILE`, `ERR_LINE`) VALUES (?, ?, ?, ?);", array('WARNING', $errstr, $errfile, $errline));
            }

            if ($CONFIG["debug"]["DEBUG_PRINT_ERR"] == "1") {
                echo "<b>WARNING</b> [$errno] $errstr on line $errline in file <a href='file:///$errfile:$errline'>$errfile</a><br />\n";
            }

            break;

        case E_USER_NOTICE:
            if (!empty($CONFIG["database"]["DB_HOST"]) && $CONFIG["debug"]["DEBUG_SAVE"] == "1") {
                db::query("INSERT INTO debug_errors (`ERR_LEVEL`, `ERR_MESSAGE`, `ERR_FILE`, `ERR_LINE`) VALUES (?, ?, ?, ?);", array('NOTICE', $errstr, $errfile, $errline));
            }

            if ($CONFIG["debug"]["DEBUG_PRINT_ERR"] == "1") {
                echo "<b>NOTICE</b> [$errno] $errstr on line $errline in file <a href='file:///$errfile:$errline'>$errfile</a><br />\n";
            }

            break;

        default:
            if (!empty($CONFIG["database"]["DB_HOST"]) && $CONFIG["debug"]["DEBUG_SAVE"] == "1") {
                db::query("INSERT INTO debug_errors (`ERR_LEVEL`, `ERR_MESSAGE`, `ERR_FILE`, `ERR_LINE`) VALUES (?, ?, ?, ?);", array('OTHER', $errstr, $errfile, $errline));
            }

            if ($CONFIG["debug"]["DEBUG_PRINT_ERR"] == "1") {
                echo "Unknown error type: [$errno] $errstr on line $errline in file <a href='file:///$errfile:$errline'>$errfile</a><br />\n";
            }

            break;
    }

    /* Don't execute PHP internal error handler */
    return true;
}
