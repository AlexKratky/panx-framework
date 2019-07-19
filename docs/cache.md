# Working with Cache

Working with Cache in panx framework is quite easy. If you need to save some variable to cache, you can do it by calling `Cache::save($name, $data) `, where `$name` is the name of the variable and `$data` is its value.

After you saved data, you can retrieve them by calling `Cache::get($name, $cacheTime)`, where `$name` is the name of the variable and `$cacheTime` is time in seconds. If the stored data in cache is older then this limit, it will return `false`. Second parameter is optional. If you do not pass any value as second parameter, Cache class will use  the default value (10 seconds).

`Cache::get()` will return the data or `false` if the variable is not stored in cache or it is too old.

Example code used in Post Class (`Post::listPosts()`):

```php
$p = Cache::get("posts", 60);
if($p !== false) {
    Logger::log("Using cached posts");
    return $p;
}
$f = scandir($_SERVER['DOCUMENT_ROOT']."/../template/posts/");
$f_arr = array();

foreach($f as $file) {
    if($file == "." || $file == "..") continue;
    array_push($f_arr, array("name" => basename($file, ".php"), "created_at" => filectime($_SERVER['DOCUMENT_ROOT']."/../template/posts/$file")));

}
//sort array by date
usort($f_arr, 'self::compareTime');
Cache::save("posts", $f_arr);
Logger::log("Cache saved");
return $f_arr;
```



* `Cache::destroy(string $name): bool` - Deletes specified cache file.
* `Cache::clearUnused($dir = null, $time = 86400)` - Deletes unused cache files (Older then $time). The $dir parameter must be specified only from terminal (\__DIR__).
* `Cache::clearAll($dir = null)` - Deletes whole cache directory. The $dir parameter must be specified only from terminal (\__DIR__).