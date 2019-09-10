<?php
function isAssoc(array $arr): bool
{
    if (array() === $arr) {
        return false;
    }

    return array_keys($arr) !== range(0, count($arr) - 1);
}

require $PATH . "/app/classes/RouteErrors.php";
require $PATH . "/app/classes/RouteAction.php";
require $PATH . "/app/classes/Route.php";
require $PATH . "/app/classes/API.php";

$route_files = scandir($PATH . "/routes/");
foreach ($route_files as $route_file) {
    if ($route_file == "." || $route_file == "..") {
        continue;
    }

    if (is_dir($PATH . "/routes/" . $route_file)) {
        continue;
    }
    require $PATH . "/routes/" . $route_file;
}


$renderer = new TextTable(Route::getDataTable());
$renderer->showHeaders(true);
$renderer->render();
