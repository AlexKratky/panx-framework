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


### < action > and < controller > wildcards
These wildcards will automatically include controller and call the contoller method specified by action. For example, you will have following route
```php
 Route::set("/admin/<controller>/<action>", "form.php");
```
and the user will visit with this URI: `/admin/auth/login/`. This will include AuthController and call the contoller's method specified by action - login() (AuthContoller::login()).

### Parameters with regex rule
You can limit the route parameter by regular expression. For example, you want to use parameter {ID}, which should be just number, so you can do it by this:

```php
Route::set("example/{ID[^[0-9]*$]}", "id.php");
```

If you enter something different then just ID, it will try to find other route, otherwise display 404.


### Parameters with Validator rule

You can limit the route parameter by Validator rule. For example, you want to use parameter {NAME}, which should be valid name, so you can do it by this:

```php
Route::set("/{NAME#Validator::validateUsername}");
```
### Including multiple files

If you want to include more template files, then you need to pass array, for example:

```php
Route::set("/docs/intro", ["header.php", "intro.php", "footer.php"]);
```

### No files

You can leave second parameter (files or function) empty, so no files will be required, only if you set up the Controller, then will be called.

### Routes with redirect

If you want to redirect from one route to another, you can do it by this:

```php
Route::set("/docs", function() {
    redirect("/docs/intro");
});
Route::set("/docs/intro", ["header.php", "intro.php", "footer.php"]);
```

### Routes with function

The second parameter of route can be anonymous function (Like in redirect).

``````php
Route::set("/dumpServerVariable", function() {
    echo 'This route will dump() $_SERVER variable';
    dump($_SERVER); // Function from panx
});
``````

### Locking route to method

If you need route accessible only from certain http methods, you can do it by passing third argument.

```php
Route::set("/", "home.php", ["GET"]);
```

Route above will be accessible only using GET method.

```php
Route::set("/postGet", "example.php", ["POST", "GET"]);
```

Route above will be accessible using methods POST and GET.

The third argument is always an array. If you visit route, that does not support that method you are requesting, you will get Error 400 - Bad request.



### API routes

API routes should be in `/routes/api.php` file. You do not sets the route using function `Route::set()`, but using `Route::apiGroup()`. For example:

```php
Route::apiGroup("v1", array(
    // /api/v1/list
    array("list", function(){
        echo "list";
    }),
    // /api/v1/getlatestversion/stable
    array("getlatestversion/stable", function() {
        echo "0.1";
    }, ["GET"]),
));
```

As the first parameter, you specify the version of API, for example `v1`, second parameter is array, containing all routes. Each route is single array. In route array, the first parameter is URI, the second parameter is function , file or array of files. The third parameter is optional. If you enter third parameter, it will lock route to certain http methods (See `Locking route to method` for more details).

Why you should use API routes instead of classic routes? If the first element in URI is `/api/`, it will check all API routes first, after it will check all classic routes. So it will increase the speed of response.

`Route::apiGroup()` function will generate route by following patern: `/api/{VERSION}/{URI}`, where `{VERSION}` is for example `v1`, and `{URI}` is for example `list` or `getlatestversion/stable` as in example above.

### Controllers

You can setup controller using setController() function:

 ```php
Route::set('/login', 'auth/login.latte')->setController("AuthController");
 ```

To get more info about controller, see [Controllers](https://panx.eu/docs/controllers)

### Middlewares

You can setup controller using setMiddleware() function:

```php
Route::set('/verifymail/{TOKEN}', function() {
    
})->setMiddleware(['AuthMiddleware']);
```

The parameter must be always array, even if you set only one middleware.

To get more info about middlewares, see [Middlewares](https://panx.eu/docs/middlewares)

### API Endpoints

You can set API endpoint using function Route::setApiEndpoint():

```php
Route::setApiEndpoint("v3", new API("v3"));
```

To get more info about API endpoints, see [API Endpoints](https://panx.eu/docs/api-endpoints)

### API Extended syntax

Because API route have many parameters, it is recommended to use following syntax:

```php
Route::apiGroup("v5", array(
    array(
        "route" => "test/{name}/{action}",
        "files" => null,
        "lock" => null,
        "required_params" => null,
        "action" => "login",
        "alias" => "test"
    ),
));
```



### Aliases

You can set up alias to each route. This is useful for your template files, because you will not write the absolute routes, e.g. '/login', but you will use the alias. If you using Latte, then you can do it by n:link, e.g. n:link="login" and when you change the route in route file, you do not need to edit the template file. In n:link, you can also include parameters for route and GET method. So if your route have wildcard, you can do it like this:

```php+HTML
<?php
Route::set("/test/of/route/<action>/{NAME}/{ID}/*", function() {echo "Hello";});
?>

<a n:link="test, 'action=akce:jmeno:identifikator:hvezdicka=[1,10,20,30]', 'id=10:remember=true:test'">macro test</a>

<!-- which will create -->
<a href="/test/of/route/akce/jmeno/identifikator/1/10/20/30/?id=10&remember=true&test">macro test</a>
```

To set alias for API route, you need as 6th parameter ([5]) or by extended syntax.

### Required parameters

You can set required parameters using ->setRequiredParameters(array $get, array $post);

Where the $get and $post  are arrays containing the names of the inputs.



## Other functions of Route class

### Route::searchWithNoLimits()

This function will return template file(s)/function without limitation of middlewares etc.

### Route::convertRoute($route = null)

Convert the URI to route.

- For example, if you set route '/example/+/edit' in route.php and you pass the URI to this function (e.g., /example/13/edit), it will returns the route with wildcard -> '/example/+/edit'
