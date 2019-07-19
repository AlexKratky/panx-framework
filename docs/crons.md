# Crons

Crons are scripts that are called periodically. They are located in `/crons/*` in daily, weekly or monthly folder, depends on the period. Each cron should include `mini-loader.php` so the classes will be automatically included, the $CONFIG variable will be available etc.

Example cron:

```php
<?php
require_once __DIR__ . "/../mini-loader.php";
$x = new APIModel();
$x->resetRateDaily();
echo "Daily rate limits reset to 0.\n";
```



