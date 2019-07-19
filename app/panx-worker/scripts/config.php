<?php
$APP_NAME = read("Name of project");
$APP_URL = read("URL of project with http:// or https:// and ending with /");
$APP_DEBUG = read("Debug mode [true/FALSE]");
if ($APP_NAME == "") {
    $APP_NAME = "panx project";
}
if (!filter_var($APP_URL, FILTER_VALIDATE_URL)) {
    error("You need to enter a valid URL");
}
if (strtolower($APP_DEBUG) != "true") {
    $APP_DEBUG = "false";
} else {
    $APP_DEBUG = "true";
}
$new_config = "[basic]
APP_NAME = $APP_NAME
APP_URL = $APP_URL
APP_DEBUG = $APP_DEBUG
APP_HTML_BEAUTIFY = false
APP_LANGUAGE = auto
APP_INFO = true
APP_LANG_CACHE_TIME = 60
APP_ROUTES_CASE_SENSITIVE = true

[database]
; If DB_HOST is empty, no connection will be created
DB_HOST =
DB_PORT = 3306
DB_DATABASE = db
DB_USERNAME = root
DB_PASSWORD =

[auth]
LANDING_PAGE = /
LOGOUT_PAGE = /login
GOOGLE_RECAPTCHA = 
GOOGLE_RECAPTCHA_SECRET = 

[custom]
CUSTOM_VALUE = xxx";

file_put_contents($PATH."/.config", $new_config);
