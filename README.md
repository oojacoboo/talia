Talia
=====

Talia is a PHP5.4+ Micro-Framework

Example Usage:
------
```php

require 'vendor/autoload.php';

$app = new Killswitch\Talia\Application;

$app->get('/', function() {
    return 'Hello World.';
});

$app->run();

/* END OF FILE */
```
