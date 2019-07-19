# Handlers

Handlers are PHP classes that will handle custom template files (meaning other extension then .php - see [Template systems](https://panx.eu/docs/template-systems)). Every handler extends the Handler class and the handler need to have function ::handle($file). The name pattern should follow: {Extension}Handler (e.g. LatteHandler for .latte files). If you will name the handlers like this, you do not need to register them, but if you name other, then you need to register them in `/app/core/handlers.php`, for example:

```php
<?php
$handlers = [
    "latte" => 'LatteHandler',
];
```

As you can see, the handler name follows the pattern, so it is no necessary to include them here, but if you do it, it is not wrong.



Example handler:

```php
<?php
class LatteHandler extends Handler {
    public static function handle($file) {
        require_once $_SERVER['DOCUMENT_ROOT']."/../vendor/autoload.php";
        $latte = new Latte\Engine;

        $latte->setTempDirectory($_SERVER['DOCUMENT_ROOT']."/../temp/");
        $latte->render($_SERVER['DOCUMENT_ROOT']."/../template/$file", self::$parameters);

    }
}
```

The handler above will handle all Latte files and render them.