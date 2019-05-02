# Creating first project using panx framework

Firstly, download panx framework from Github and extract it to your server. Point your Apache/nginx server to `/public/` folder, so files outside will be hidden for everyone. When you have your web server set up, you need run command to install all dependencies:

`composer install`

This command will install Parsedown (Markdown parser). It is necessary to have markdown parser if you want create documentation. 

Next step is edit `.config` (ini) file. Here you need change `APP_NAME`, `APP_URL`. You should keep `APP_DEBUG` to `false`. Database credentials do not need to change, only if you want to use database. If `DB_HOST` is empty, no connection will be created. Also you can enter other custom values and access to them using `$CONFIG["custom"]["CUSTOM_VALUE"]`.

Next step is to set routes, go to `/routes/` and open file `route.php`. Add all routes you want, for example:

```php
<?php
// will handle request to / (e.g. http://example.com/) and require home.php from template
Route::set("/", "home.php");
Route::set("/post/{AUTHOR_ID}/{ID}", ["header.php", "post.php"]);
Route::set("/login", "login.php");
Route::set("/test/*", "test.php");
```

To better understand to routes, see [Routes](https://panx.eu/docs/routes).

Next step is uploading files to `/template/` folder, every file need to be PHP file and should correspond with files provided in `route.php`

Final step is uploading public files (CSS, JS, images) to `/public/res/`.

