<?php
$C = [];


foreach ($_POST as $key => $value) {
    if(strpos($key, ":") !== false) {
        // namespace, e.g. basic:APP_NAME
        $key = explode(":", $key, 2);
        if(!isset($C[$key[0]])) {
            $C[$key[0]] = [];
        }
        if(strpos($value, "[") === 0 && strpos($value, "]") === (strlen($value) - 1)) {
            //probably array
            if(isset($GLOBALS["CONFIG"][$key[0]][$key[1]])) {
                if(is_array($GLOBALS["CONFIG"][$key[0]][$key[1]])) {
                    $value = trim($value, "[]");
                    $value = preg_split("/,\s*/", $value);
                }
            }
        }
        $C[$key[0]][$key[1]] = $value;
    } else {
        $C[$key] = $value;
    }
}





copy($_SERVER["DOCUMENT_ROOT"] . "/../.config", $_SERVER["DOCUMENT_ROOT"] . "/../.config.backup");
if(file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/../.config", arr2ini($C)) === false) {
    echo "<h3 style='color: red;'>Failed to save your config!</h3>";
} else {
    redirect("/_setup?success");

}

function arr2ini(array $a, array $parent = array())
{
    $out = '';
    foreach ($a as $k => $v)
    {
        if (is_array($v))
        {
            $sec = array_merge((array) $parent, (array) $k);
            $out .= PHP_EOL . '[' . join('.', $sec) . ']' . PHP_EOL;
            if($k == "addintional_loader_files_before" ||  $k == "addintional_loader_files_after") {
                foreach ($v as $key => $value) {
                    foreach ($value as $index => $real_value) {
                        $out .= $key . "[] = " . $real_value . PHP_EOL;
                    }
                }
                continue;
            }
            if(count($parent) == 0) {
                $out .= arr2ini($v, $sec);
            }
        }
        else
        {
            $out .= "$k = $v" . PHP_EOL;
        }
    }
    return $out;
}