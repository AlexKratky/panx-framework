# Debbuging

To enable debug mode, change the value in config APP_DEBUG to true. This will enable many options. Also, you can select some of this function in [debug] option in config.



### Saving errors

In debug mode, all errors will be logged into error.log and saved to DB.

### Saving visits

In debug mode, all visits will be logged into access.log and saved to DB.

### Tracy extension

You can enable Tracy extension by creating file with following code:

```php
<?php
require_once $_SERVER['DOCUMENT_ROOT']."/../vendor/autoload.php";
use Tracy\Debugger;

Debugger::enable(!($GLOBALS["CONFIG"]["basic"]["APP_DEBUG"] == "1"));
```



and by adding that file into the config:

```ini
[addintional_loader_files_before]
file[] = tracy.php
```

