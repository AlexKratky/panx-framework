<?php

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

function redirect($url) {
    //var_dump(debug_backtrace());

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

function dump($var, $should_exit = true) {
    if(!isset($CONFIG))
        $CONFIG = parse_ini_file($_SERVER['DOCUMENT_ROOT']."/../.config", true);

    if($CONFIG["basic"]["APP_DEBUG"] == "1") {
        echo "<pre>";
        //var_dump($var);
        highlight_string("<?php\n" . var_export($var, true) . ";\n?>");
        
        echo "</pre>";
        echo '<script>document.getElementsByTagName("code")[0].getElementsByTagName("span")[1].remove() ;document.getElementsByTagName("code")[0].getElementsByTagName("span")[document.getElementsByTagName("code")[0].getElementsByTagName("span").length - 1].remove() ; </script>';

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


/*
*
* Indents a flat JSON string to make it more human-readable.
*
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
 * https://github.com/spyrosoft/php-format-html-output
 * https://www.php.net/manual/en/tidy.examples.basic.php
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