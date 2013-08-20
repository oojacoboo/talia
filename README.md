Talia
=====

Talia is a PHP5.4+ Micro-Framework written by [Three Leaf Creative](http://www.threeleafcreative.com)

Example Usage:
------
```php

require 'vendor/autoload.php';

use Talia\Talia AS App;

$app = new App();

$app->get('/', function() {
	echo 'Hello World.';
});

$app->run();

/* END OF FILE */
```
