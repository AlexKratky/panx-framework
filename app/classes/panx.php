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
    if($template_files === null) {
        error(500);

    }
    if (!is_array($template_files)) {
        if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/../template/" . $template_files)) {
            require $_SERVER['DOCUMENT_ROOT'] . "/../template/" . $template_files;
        } else {
            error(500);
        }
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
 * Redirects to Route alias.
 * @param string $alias The Route alias where will be the user redirected.
 * @param string|null $params The string of Route params.
 * @param string|null $get The string of GET params. 
 * @param boolean|string $goto If is equal to TRUE, saves to session the current URL. If is equal to FALSE, it will not saves anything to session. Otherwise, it will save string passed to session.
 */
function aliasredirect($alias, $params = null, $get = null, $goto = false) {
    redirect(Route::alias($alias, $params, $get), $goto);
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

$_DUMP_CSS_ALREADY_INCLUDED = false;

/**
 * Dumps the variable.
 * @param mixed $var The variable to be dumped. If the $var is = 23000, dump all defined vars
 * @param boolean $should_exit If it sets to TRUE, the function will stop executing, otherwise it will not. Default is TRUE.
 */
function d($var = 21300, $should_exit = true, $var_name_manual = null) {
    global $_DUMP_CSS_ALREADY_INCLUDED;
    $varName = $var;
    foreach ($GLOBALS as $var_name => $value) {
        if ($value === $var) {
            $varName = $var_name;
        }
    }
    if($var === 21300) $varName = "dump";
    if(!is_string($varName)) {
        $varName = "dump";
    } else {
        $varName = "\$". $varName;
    }
    $varName = $var_name_manual ?? $varName;

    if(!isset($CONFIG))
        $CONFIG = parse_ini_file($_SERVER['DOCUMENT_ROOT']."/../.config", true);
    $id = generateRandomString();
    
    if($CONFIG["basic"]["APP_DEBUG"] == "1") {
        if(!$_DUMP_CSS_ALREADY_INCLUDED) {
            echo '<link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">';
            $_DUMP_CSS_ALREADY_INCLUDED = true;
        }
        echo "<div style='border: 1px solid #1a1a1d; padding: 5px; border-radius:5px;font-family: \"Poppins\", sans-serif; background: white; position: relative; z-index: 9999; margin-top:5px;' id='{$id}_main'>";
        echo "<div onclick='document.getElementById(\"$id\").style.display = (document.getElementById(\"$id\").style.display == \"block\" ? \"none\" : \"block\")' style='width: 100%; color:red; cursor: pointer;'>$varName > <span onclick='document.getElementById(\"{$id}_main\").style.display = \"none\"' style='float: right; margin-right: 10px; font-size: 18px;'>&times;</span></div>";
        echo "<div id='$id' style='display:none'>";

        if($var === 21300) {
            foreach (get_defined_vars() as $k => $v) {
            echo "<pre>";
            //var_dump($var);
            echo $k . ": ";
            highlight_string("<?php\n" . var_export($v, true) . "\n");

            echo "</pre>";
            //echo '<script>d

            }
        } else {
            echo "<pre>";
            //var_dump($var);
            highlight_string("<?php\n" . var_export($var, true) . "\n");
            
            echo "</pre>";
            //echo '<script>document.getElementsByTagName("code")[0].getElementsByTagName("span")[1].remove() ;document.getElementsByTagName("code")[0].getElementsByTagName("span")[document.getElementsByTagName("code")[0].getElementsByTagName("span").length - 1].remove() ; </script>';
        }
        $args = array();
        for($i = 0; $i < count(debug_backtrace()[1]['args']); $i++) {
            array_push($args, "\"". debug_backtrace()[1]['args'][$i] . "\"");
        }
        echo "<div style='color:black';>";
        echo "<br><br><b>Source</b>: ".  debug_backtrace()[0]['file']."@" . debug_backtrace()[1]['function'] ."(".implode(", ", $args).")";
        echo "<hr>";
        echo "<pre>";
        print_r(debug_backtrace());
        echo "</pre>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
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
 * @param bool $default Determine, if the translation is located in default language files.
 * @param array $replacement Replace %s with the value from replacement.
 * @param string $returnKeyOnFailure Returns the key instead of false, if no data found.
 * @param string $keyFormat The format in which will be key returned if no data found. Use %s for $key. 
 * @return string|false The translation of key or false if the translation does not exists.
 */
function __($key, $default = false, $replacement = array(), $returnKeyOnFailure = true, $keyFormat = "__%s") {
    if(!isset($CONFIG))
        $CONFIG = parse_ini_file($_SERVER['DOCUMENT_ROOT']."/../.config", true);

    $lang = strtolower($CONFIG["basic"]["APP_LANGUAGE"]);

    if($lang == "auto") {
        $lang = $GLOBALS["request"]->getMostPreferredLanguage();
        if($lang === null) {
            $lang = "en";
        }
        if($lang == "cs") {
            $lang = "cz";
        }
    }
    if(isset($_COOKIE["language"])) {
        $lang = $_COOKIE["language"];
    }
    if($default) {
        $lang = "default_$lang";
    }
    $c = Cache::get("lang_$lang.json", ($default ? 86400 : $CONFIG["basic"]["APP_LANG_CACHE_TIME"]));
    if($c !== false) {
        if(empty($c[$key])) {
            if(!$returnKeyOnFailure)
                return false;
            return str_replace("%s", $key, $keyFormat);
        } else {
            $x = $c[$key];
            for ($i = 0; $i < count($replacement); $i++) {
                $x = preg_replace("/%s/", $replacement[$i], $x, 1);
            }
            return $x;
        }
    } else {
        $translation = array();
        if(!file_exists($_SERVER['DOCUMENT_ROOT']."/../app/resources/lang/$lang.lang")) {
            if(!file_exists($_SERVER['DOCUMENT_ROOT']."/../app/resources/lang/en.lang")) {
                if(!$returnKeyOnFailure)
                    return false;
                return str_replace("%s", $key, $keyFormat);
            } else {
                $lang = "en";
            }
        }
        $lang_f = $_SERVER['DOCUMENT_ROOT']."/../app/resources/lang/$lang.lang";
        $lang_f = file_get_contents($lang_f);
        $lang_f = explode(PHP_EOL, $lang_f);
        foreach ($lang_f as $line) {
            $line = explode(": ", $line, 2);
            $translation[$line[0]] = $line[1];
        }
        Cache::save("lang_$lang.json", $translation);
        if(empty($translation[$key])) {
            if(!$returnKeyOnFailure)
                return false;
            return str_replace("%s", $key, $keyFormat);
        } else {
            $x = $translation[$key];
            for ($i = 0; $i < count($replacement); $i++) {
                $x = preg_replace("/%s/", $replacement[$i], $x, 1);
            }
            return $x;
        }
    }
}

/**
 * Prints the JS code with GoogleAnalytics. You need specife the UA code in config.
 */
function _ga() {
    //dump();
        $CONFIG = parse_ini_file($_SERVER['DOCUMENT_ROOT']."/../.config", true);
        if(!empty($CONFIG["google-analytics"]["UA_CODE"])) {
        $UA = $CONFIG["google-analytics"]["UA_CODE"];
        echo "<!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src=\"https://www.googletagmanager.com/gtag/js?id=$UA\"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', '$UA');
    </script>
    ";
        }
}

/**
 * Shortcut for Route::alias. Prints: href="{ROUTE}".
 * @param string $alias The alias of the route.
 * @param string $parmas The Route parameters. Write like this param1:param2:[1,2,3]:comment=true
 * @param string $get The GET parameters (eg. ?x=x). Write like this x=true:y=false
 */
function href($alias, $params = null, $get = null) {
    echo 'href="'.Route::alias($alias,$params,$get).'"';
}

/**
 * Shortcut for Route::alias. Prints: {ROUTE}
 * @param string $alias The alias of the route.
 * @param string $parmas The Route parameters. Write like this param1:param2:[1,2,3]:comment=true
 * @param string $get The GET parameters (eg. ?x=x). Write like this x=true:y=false
 */
function l($alias, $params = null, $get = null) {
    echo Route::alias($alias,$params,$get);
}

/**
 * Returns random string with specified $length.
 */
function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
}

/**
 * Sets the CORS headers by config. E.g. accept any origin on /api/xx/xxxxx
 */
function cors()
{
    $CONFIG = parse_ini_file($_SERVER['DOCUMENT_ROOT']."/../.config", true);
    if($CONFIG["basic"]["APP_CORS"] != "1") return;
    if($CONFIG["basic"]["APP_CORS_ONLY_API"] == "1") {
        $u = new URL();
        if(!isset($u->getLink()[1]) || $u->getLink()[1] != "api") {
            return;
        }
    }
    // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400'); // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        // may also be using PUT, PATCH, HEAD etc
        {
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        }

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        }

        exit(0);
    }
}
