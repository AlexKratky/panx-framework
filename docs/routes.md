# Routes

All routes are located in `/routes/` folder, by default in `route.php` file, but it could be in any other file, just by creating custom PHP file in `/routes/`. 

Syntax of routes:

```php
Route::set("/URI/TO/HANDLE", "File_to_require.php");
```

In URI, you can use wildcards (+, *).

The `+` meaning is one element, for example: `/post/+/edit` will handle `/post/1/edit`, `/post/2/edit`, ...

The ` *` meaning is one or more elements, for example: `/post/*` will handle `/post/1`, but also `/post/1/edit`, `/post/2/view`, ...

But in URI you can use parameters too, for example `/post/{ID}/edit` have same meaning as `/post/+/edit`, but you can access to `{ID}`  using `Route::getValue("ID")`.

### Default routes

In `/routes/` directory is also file called `default.php` containing default routes. In default routes are routes to error pages. For example Error 404. When you need to set route for error code (403, 404), you need to call function `Route::setError` instead of `Route::set`. Example syntax: 

 ```php
Route::setError(404, "default/errors/404.php");
 ```

### Using wildcards

Keep in mind that routes with wildcard should be last, for example:

```php
Route::set("/", "home.php");
Route::set("*", "test.php");
```

This will display `home.php` file to all users requesting to `/`, and `test.php` to all users requesting something difference. 

But if you write something like this:

```php
Route::set("*", "test.php");
Route::set("/", "home.php");
```

The result will be different. It will display `test.php` to all users, whatever user request. So if user tries to request `/`, framework will display `test.php` instead of `home.php`. So in this case, the second route is useless, and should be deleted to keep route file clear.

### Including multiple files

If you want to include more template files, then you need to pass array, for example:

```php
Route::set("/docs/intro", ["header.php", "intro.php", "footer.php"]);
```

### Routes with redirect

If you want to redirect from one route to another, you can do it by this:

```php
Route::set("/docs", function() {
    redirect("/docs/intro");
});
Route::set("/docs/intro", ["header.php", "intro.php", "footer.php"]);
```

