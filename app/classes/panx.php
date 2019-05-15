<?php

function error($code) {
    $template_files = Route::searchError($code);
    if (!is_array($template_files)) {
        require __DIR__ . "/../../template/" . $template_files;
    } else {
        for ($i = 0; $i < count($template_files); $i++) {
            require __DIR__ . "/../../template/" . $template_files[$i];
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

function dump($var) {
    if(!isset($CONFIG))
        $CONFIG = parse_ini_file(__DIR__."/../../.config", true);

    if($CONFIG["basic"]["APP_DEBUG"] == "true") {
        var_dump($var);
        exit();
    }
}