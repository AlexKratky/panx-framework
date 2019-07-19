<?php
require $PATH . "/vendor/autoload.php";

$docs_folder = "/docs/";
if (isset($ARGS[2])) {
    if($ARGS[2] == "?"){
        info_msg("Parameters: ");
        info_msg(" • [SOURCE] # default: /docs/");
        info_msg(" • [VERSION] # default: /, if you enter something, write '/' on start and end of string.");
        exit();
    }
    $docs_folder = $ARGS[2];

}
$version = "/";
if (isset($ARGS[3])) {
    $version = $ARGS[3];
}


info_msg("Documentation will be created from folder: " . $docs_folder);
$Parsedown = new ParsedownExtra();
$r_files = "";
$menu = "";
$menu_arr = array();
$path = $PATH . "/template/docs$version";
$source = $PATH . $docs_folder;
$folders = array($source);
$index = 0;
while (count($folders) > $index) {
    $f = scandir($folders[$index]);
    $rel_path = $path . str_replace($source, "", $folders[$index]);
    if (!file_exists($rel_path)) {
        mkdir($rel_path);
    }

    for ($i = 2; $i < count($f); $i++) {
        // #+?\ .+?\n <- Match title
        if (is_dir($folders[$index] . $f[$i])) {
            array_push($folders, $folders[$index] . $f[$i] . "/");
            continue;
        }
        if ($f[$i] == "sorting") {
            continue;
        }
        preg_match("/#+?\ (.+?)\n/", file_get_contents($folders[$index] . $f[$i]), $matches);
        if (!isset($matches[1])) {
            $matches[1] = basename($f[$i], ".md");
        }
        //$matches[1] is title
        file_put_contents($rel_path . basename($f[$i], ".md") . ".php", "<title>" . $CONFIG["basic"]["APP_NAME"] . " | Documentation | " . $matches[1] . " </title>" . $Parsedown->text(file_get_contents($folders[$index] . $f[$i])));
        //$menu = $menu . "<li class='sidemenu-li'><a href='/docs/".basename($f[$i], ".md")."'>".$matches[1]."</a></li>\r\n";
        array_push($menu_arr, array(str_replace($source, "", $folders[$index]) . basename($f[$i], ".md"), $matches[1]));
        $r_files = $r_files . "Route::set('/docs$version" . str_replace($source, "", $folders[$index]) . basename($f[$i], ".md") . "', ['docs$version"."header.php', 'docs$version" . str_replace($source, "", $folders[$index]) . basename($f[$i], ".md") . ".php', 'docs$version"."footer.php']);\r\n";
    }
    $index++;
}
if (file_exists($source . "sorting")) {
    info_msg("Using sorting");
    $s = file_get_contents($source . "sorting");
    $s = explode(PHP_EOL, $s);
    foreach ($s as $line) {
        foreach ($menu_arr as $menu_arr_el) {
            if ($menu_arr_el[0] == trim($line)) {
                $menu = $menu . "<li class='sidemenu-li'><a href='/docs$version" . $menu_arr_el[0] . "'>" . $menu_arr_el[1] . "</a></li>\r\n";
                break;
            }
        }

    }
} else {
    info_msg("Sort the pages, start with the first:");
    $c = count($menu_arr);
    while (count($menu_arr) > 0) {
        for ($x = 0; $x < $c; $x++) {
            if (isset($menu_arr[$x][0])) {
                write(($x) . ": " . $menu_arr[$x][0] . " - " . $menu_arr[$x][1]);
            }

        }
        $n = read("Select page (or write default to use this sorting)");
        if ($n == "default") {
            foreach ($menu_arr as $menu_arr_el) {
                $menu = $menu . "<li class='sidemenu-li'><a href='/docs$version" . $menu_arr_el[0] . "'>" . $menu_arr_el[1] . "</a></li>\r\n";
                //unset($menu_arr_el);

            }
            break;
        } else {
            if (isset($menu_arr[$n])) {
                $menu = $menu . "<li class='sidemenu-li'><a href='/docs$version" . $menu_arr[$n][0] . "'>" . $menu_arr[$n][1] . "</a></li>\r\n";
                unset($menu_arr[$n]);
                info_msg($n);

            } else {
                info_msg("Invalid page number.");
            }
        }
    }
}
/**
 * Steps that panx-worker must do:
 * * Edit route.php
 * * Create header.php
 * * From generated html create php file and put it to template
 */
write(colorize("Keep current home.php file? [y/N]", "cyan", "black"), false);
$H = read("");
$route;
if ($H != "y" && $H != "Y") {
    file_put_contents($PATH . "/template/home.php", "<h1>Documentation created by panx-worker</h1>");
    $route = "<?php
Route::set('/', ['header.php', 'home.php', 'footer.php']);
\r\n" . $r_files;
//Route::set('/docs', ['header.php', 'footer.php']);

} else {
    $route = "<?php\r\n" . $r_files;
}

if (!file_exists($PATH . "/routes/backup/")) {
    mkdir($PATH . "/routes/backup/");
}

copy($PATH . "/routes/route.php", $PATH . "/routes/backup/route.php");
if($version != "/") {
    $rx = "<?php
Route::set('/docs/', function() {
    redirect('/docs$version');
});";
    file_put_contents($PATH . "/routes/xdocs.php", $rx);
} else {
    if(file_exists($PATH . "/routes/xdocs.php")) {
        unlink($PATH . "/routes/xdocs.php");
    }
}

write(colorize("Redirect /docs$version to /docs$version?: (e.g. enter 'intro' for redirect to /docs$version"."intro)", "cyan", "black"), false);
$R = read("");
if ($R != "") {
    $route = $route . 'Route::set("/docs'.$version.'", function () {
    redirect("/docs'.$version . $R . '");
});' . "\r\n";

}


file_put_contents($PATH . "/routes/docs".str_replace("/", "_", $version).".php", $route);
write(colorize("Use dark theme [Y/n]", "cyan", "black"), false);
$dt = read("");
if ($dt != "n" && $dt != "N") {
    file_put_contents($PATH . "/template/docs$version"."header.php", file_get_contents($PATH . "/app/panx-worker/docs-resource/header.php") . $menu . file_get_contents($PATH . "/app/panx-worker/docs-resource/header_continue.php"));
    info_msg("Using dark theme");
} else {
    file_put_contents($PATH . "/template/docs$version"."header.php", file_get_contents($PATH . "/app/panx-worker/docs-resource/header_light.php") . $menu . file_get_contents($PATH . "/app/panx-worker/docs-resource/header_continue.php"));
}

copy($PATH . "/app/panx-worker/docs-resource/footer.php", $PATH . "/template/docs$version"."footer.php");
