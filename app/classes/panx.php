<?php
/**
 * @name panx.php
 * @link https://alexkratky.cz                          Author website
 * @link https://panx.eu/docs/                          Documentation
 * @link https://github.com/AlexKratky/panx-framework/  Github Repository
 * @author Alex Kratky <info@alexkratky.cz>
 * @copyright Copyright (c) 2019 Alex Kratky
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @description File containing useful functions. Part of panx-framework.
 */

/**
 * Search for error's template files, include it and stop executing.
 * @param mixed $code The error code.
 */
function error($code) {
    $template_files = Route::searchError($code);
    if (!is_array($template_files)) {
        require $_SERVER['DOCUMENT_ROOT'] . "/../template/" . $template_files;
    } else {
        for ($i = 0; $i < count($template_files); $i++) {
            require $_SERVER['DOCUMENT_ROOT'] . "/../template/" . $template_files[$i];
        }
    }
    exit();

}

/**
 * Redirects to specified URL.
 * @param string $url The URL where will be the user redirected.
 * @param boolean|string $goto If is equal to TRUE, saves to session the current URL. If is equal to FALSE, it will not saves anything to session. Otherwise, it will save string passed to session.
 */
function redirect($url, $goto = false) {
    //var_dump(debug_backtrace());
    if($goto != false) {
        if($goto) {
            $_SESSION["REDIRECT_TO"] = $_SERVER['REQUEST_URI'];
        } else {
            $_SESSION["REDIRECT_TO"] = $goto;
        }
    }
    if (headers_sent() === false) {
        header('Location: ' . $url);
    } else {
        echo '  <script type="text/javascript">
                    window.location = "'.$url.'"
                </script>
                <noscript>
                     <meta http-equiv="refresh" content="0;url='.$url.'.html">
                </noscript>';

    }

    exit();
}

/**
 * Go to URL before redirect - Specified by $goto in redirect()
 * @return false|redirect If $_SESSION["REDIRECT_TO"] does not contain any URL, it will return FALSE, otherwise the user will be redirected to that URL.
 */
function goToPrevious() {
    if (!empty($_SESSION["REDIRECT_TO"])) {
        $goto = $_SESSION["REDIRECT_TO"];
        unset($_SESSION["REDIRECT_TO"]);
        redirect($goto);
    } else {
        return false;
    }
}

/**
 * Dumps the variable.
 * @param mixed $var The variable to be dumped.
 * @param boolean $should_exit If it sets to TRUE, the function will stop executing, otherwise it will not. Default is TRUE.
 */
function dump($var, $should_exit = true) {
    if(!isset($CONFIG))
        $CONFIG = parse_ini_file($_SERVER['DOCUMENT_ROOT']."/../.config", true);

    if($CONFIG["basic"]["APP_DEBUG"] == "1") {
        echo "<pre>";
        //var_dump($var);
        highlight_string("<?php\n" . var_export($var, true) . "\n");
        
        echo "</pre>";
        //echo '<script>document.getElementsByTagName("code")[0].getElementsByTagName("span")[1].remove() ;document.getElementsByTagName("code")[0].getElementsByTagName("span")[document.getElementsByTagName("code")[0].getElementsByTagName("span").length - 1].remove() ; </script>';

        $args = array();
        for($i = 0; $i < count(debug_backtrace()[1]['args']); $i++) {
            array_push($args, "\"". debug_backtrace()[1]['args'][$i] . "\"");
        }
        echo "<br><br><b>Source</b>: ".  debug_backtrace()[0]['file']."@" . debug_backtrace()[1]['function'] ."(".implode(", ", $args).")";
        echo "<hr>";
        echo "<pre>";
        print_r(debug_backtrace());
        echo "</pre>";

        if($should_exit) exit();
    }
}


/**
* Indents a flat JSON string to make it more human-readable.
* @param string $json The original JSON string to process.
* @return string Indented version of the original JSON string.
*/
function json($json)
{
    header('Content-Type: application/json');
    $result = '';
    $pos = 0;
    $strLen = strlen($json);
    $indentStr = "\t";
    $newLine = "\n";

    for ($i = 0; $i < $strLen; $i++) {
        // Grab the next character in the string.
        $char = $json[$i];

        // Are we inside a quoted string?
        if ($char == '"') {
            // search for the end of the string (keeping in mind of the escape sequences)
            if (!preg_match('`"(\|"|.)*?"`s', $json, $m, null, $i)) {
                return $json;
            }

            // add extracted string to the result and move ahead
            $result .= $m[0];
            $i += strLen($m[0]) - 1;
            continue;
        } else if ($char == '}' || $char == ']') {
            $result .= $newLine;
            $pos--;
            $result .= str_repeat($indentStr, $pos);
        }

        // Add the character to the result string.
        $result .= $char;

        // If the last character was the beginning of an element,
        // output a new line and indent the next line.
        if ($char == ',' || $char == '{' || $char == '[') {
            $result .= $newLine;
            if ($char == '{' || $char == '[') {
                $pos++;
            }

            $result .= str_repeat($indentStr, $pos);
        }
    }

    return $result;
}


/**
 * The function will beautify the outputed HTML using https://www.php.net/manual/en/tidy.examples.basic.php
 */
function html() {
    $html = ob_get_clean();

    $config = array(
        'indent' => true,
        'output-xhtml' => true,
        'wrap' => 200);

    // Tidy
    $tidy = new tidy;
    $tidy->parseString($html, $config, 'utf8');
    $tidy->cleanRepair();

    // Output
    echo $tidy;

}

/**
 * Function to obtain translation of key. The language is specified in .config
 * @param string $key The name of key.
 * @return string The translation of key.
 */
function __($key) {
    if(!isset($CONFIG))
        $CONFIG = parse_ini_file($_SERVER['DOCUMENT_ROOT']."/../.config", true);

    $lang = $CONFIG["basic"]["APP_LANGUAGE"];
    $c = Cache::get("lang_$lang.json", 60);
    if($c !== false) {
        return (empty($c[$key]) ? false : $c[$key]);
    } else {
        $translation = array();
        $lang_f = $_SERVER['DOCUMENT_ROOT']."/../app/resources/lang/$lang.lang";
        $lang_f = file_get_contents($lang_f);
        $lang_f = explode(PHP_EOL, $lang_f);
        foreach ($lang_f as $line) {
            $line = explode(": ", $line, 2);
            $translation[$line[0]] = $line[1];
        }
        Cache::save("lang_$lang.json", $translation);
        return (empty($translation[$key]) ? false : $translation[$key]);
    }
}