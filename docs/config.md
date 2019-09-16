# Config file

Example config file

```ini
[basic]
APP_NAME = panx framework ; The project name
APP_URL = https://panx.eu/ ; Project url with http:// prefix and ending /
APP_DEBUG = true ; Determines, if the debug mode is on
APP_HTML_BEAUTIFY = false
APP_LANGUAGE = auto ; The default app language
APP_INFO = true ; Add html comment that the site is powered by panx framework
APP_INFO_ONLY_HOME = true ; The info message will appear only on /
APP_LANG_CACHE_TIME = 60 ; Default language cache time
APP_ROUTES_CASE_SENSITIVE = true ; Determines, if the routes is case sensitive
APP_CACHE_API_RESULTS = true ; Will cache api responses, by default 5 ~ 10s
APP_MULTI_LANGUAGE_POSTS = true
APP_PRIMARY_POST_LANGUAGE = en
; If you use <controller> or <action> in routes and that controller or action does not exists, display this error code
APP_ERROR_CODE_OF_MISSING_CONTROLLER_OR_ACTION = 400
; Saves access to access.log
APP_LOG_ACCESS = true
APP_CORS = true ; Sets CORS to any (*)
APP_CORS_ONLY_API = true ; Sets CORS to any (*), but only on /api/*

[database]
; If DB_HOST is empty, no connection will be created
DB_HOST = 
DB_PORT = 3306
DB_DATABASE =
DB_USERNAME =
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
; TWO_FACTOR_ATUH = true

[cron]
SECRET = 

; UA_CODE for GA
[google-analytics]
UA_CODE =

; Additional files that will be included on top of loader.php
[addintional_loader_files_before]
file[] = tracy.php

; Additional files that will be included on bottom of loader.php
[addintional_loader_files_after]

; Your custom config values
[custom]
CUSTOM_VALUE = xxx
```

