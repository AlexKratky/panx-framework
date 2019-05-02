# Custom functions

panx-framework include `panx.php` file (located `/app/classes/panx`) containing special function.

List of special functions:

* error($code) - Include template file of specified error and exit() executing of script.
* redirect($url) - Redirects to $url

### Usage

You can use all these function from yours templates files, just by calling them without any prefix, for example:

```php
$is_user_logged_in = false;
if($is_user_logged_in)
	error("USER_NOT_LOGGED_IN");
```

> When you are using error() function, don't forget to include route for error file, e.g. Route::setError("USER_NOT_LOGGED_IN", "errors/not_logged_in.php");