# Creating first project using panx framework

Firstly, download panx framework from Github and extract it to your server. Or you can download just `panx-worker` and run command `php panx-worker install 0.1 clean`, which will download and create clean project (You can check which version is latest [here](https://panx.eu/api/v1/getlatestversion)). You need to specify which version you want to use, or you can run just `php panx-worker install`, which will download latest version, but keep in mind, that without `clean` argument, you will download all files, like templates, documentation, etc. After you ran the command, the last step is run command `php panx-worker config`, which will generate `.config` file. Point your Apache/nginx server to `/public/` folder, so files outside will be hidden for everyone. When you have your web server set up, you need run command to install all dependencies:

`composer install`

This command will install Parsedown (Markdown parser). It is necessary to have markdown parser if you want create documentation. 

Next step is edit `.config` (ini) file, if you have not done it yet. Here you need change `APP_NAME`, `APP_URL`. You should keep `APP_DEBUG` to `false`. Database credentials do not need to change, only if you want to use database. If `DB_HOST` is empty, no connection will be created. Also you can enter other custom values and access to them using `$CONFIG["custom"]["CUSTOM_VALUE"]` (This sometimes will not work, you should use `$GLOBALS["CONFIG"]["custom"]["CUSTOM_VALUE"]`). Or you can run command `php panx-worker config` which will generate config for you.

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

