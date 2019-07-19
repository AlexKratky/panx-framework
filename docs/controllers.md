# Controllers

Controller connects the template file with model. That means, the data from model will be passed to view (template file) using controller.

Each controller must have ::main($handler) function, which will be called automatically from loader.php.

Example controller:

```php
class AuthController
{
    private static $handler;

    public static function main($handler) {
        self::$handler = $handler;
       
        if (isset($GLOBALS["request"]->getUrl()->getLink()[1])) {
            switch($GLOBALS["request"]->getUrl()->getLink()[1]) {
                case 'login':
                    self::login();  
                    break;
            }
        }
    }

    public static function login() {
        self::$handler::setParameters([
            'name'=>'xx',
            'mail'=>'yy'
        ]);
    }
}

```

The $handler contain reference to [Handler](https://panx.eu/docs/handlers) of the file (e.g. LatteHandler). So the Handler will obtain parameters that will be available in template file using controller.