Talia
=====

Talia is a PHP5.4+ Micro-Framework

Example Usage:
------
```php

require 'vendor/autoload.php';

use Killswitch\Talia\Application;

$app = new Application();

$app->get('/', function() {
    echo 'Hello World.';
});

$app->run();

/* END OF FILE */
```
