<?php
$APP_NAME = read("Name of project");
$APP_URL = read("URL of project with http:// or https:// and ending with / or enter auto");
$APP_DEBUG = read("Debug mode [true/FALSE]");
if ($APP_NAME == "") {
    $APP_NAME = "panx project";
}
if ($APP_URL != "auto") {
    if (!filter_var($APP_URL, FILTER_VALIDATE_URL)) {
        error("You need to enter a valid URL");
    }
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
APP_INFO_ONLY_HOME = true
APP_LANG_CACHE_TIME = 60
APP_ROUTES_CASE_SENSITIVE = true
APP_CACHE_API_RESULTS = true
APP_MULTI_LANGUAGE_POSTS = true
APP_PRIMARY_POST_LANGUAGE = en
; If you use <controller> or <action> in routes and that controller or action does not exists, display this error code
APP_ERROR_CODE_OF_MISSING_CONTROLLER_OR_ACTION = 400
; Saves access to access.log
APP_LOG_ACCESS = true
APP_CORS = true
APP_CORS_ONLY_API = true
; Allows you to visit localhost routes (e.g. /_setup/) from this IP. If not IP set, then the routes will be accessible only from localhost.
APP_ACCESS_LOCALHOST_ROUTES_FROM_IP = 

[database]
; If DB_HOST is empty, no connection will be created
DB_HOST = 
DB_PORT = 3306
DB_DATABASE = db
DB_USERNAME = root
DB_PASSWORD = 

[debug]
; saves to db & log
DEBUG_VISITS_WITHOUT_DEBUG = true
DEBUG_VISITS = true
; SAVE ERRORS TO DB
DEBUG_SAVE = true
; SAVE ERRORS TO FILE
DEBUG_LOG_ERR = true
DEBUG_PRINT_ERR = true


[auth]
LANDING_PAGE = /
LOGOUT_PAGE = /login
GOOGLE_RECAPTCHA = 
GOOGLE_RECAPTCHA_SECRET = 
; currently not supported
; TWO_FACTOR_AUTH = true

[cron]
SECRET = 

[google-analytics]
UA_CODE = 

[addintional_loader_files_before]
file[] = tracy.php

[addintional_loader_files_after]

[custom]
CUSTOM_VALUE = xxx";

file_put_contents($PATH."/.config", $new_config);
