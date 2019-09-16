# Auth

The panx-framework support authentification by default using Auth class. All routes are located in `auth.php`, all template files in `/template/auth/`, all CSS files in `/res/css/auth/`. Auth class is included automatically. Auth class will auto login user, if there are valid cookies. You can check if user is logined using AuthMiddleware or by function `isLogined(): bool`. You can access all user data (except password), using `user($data)` function, for example:

```php
$name = $auth->user('name');
$id = $auth->user('id');
$mail = $auth->user('mail');
```

The argument in user() is case insensitive and have multiple aliases, e.g. you can use `user('email')` or `user('mail')`.





To work with Auth class, you need to setup DB connection and run command `php panx-worker create auth`

Also, you need to setup following things in .config:

```ini
[auth]
LANDING_PAGE = /
LOGOUT_PAGE = /login
; The google recaptcha is needed to prevent from brutal force attacks
GOOGLE_RECAPTCHA = 
GOOGLE_RECAPTCHA_SECRET = 
```



To install routes, css & templates, you need to install Auth extension

`php panx-worker extension install https://panx.eu/download/extensions/Auth.zip`

