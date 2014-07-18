# Talia

Talia is a PHP >= 5.4 Micro-framework built just to try new things, and build some stuff quickly via prototypes or even full blown apps.

## Installation

Installation of Talia is pretty easy and straightforward. Create a `composer.json` file and just `require` Talia (`"certifiedwebninja/talia": "~2"`) then run `composer install`. After that it's pretty simple to get started below.

## Example Usage:

```php

require 'vendor/autoload.php';

use CertifiedWebNinja\Talia\Application;

// Initialize application with environment
$app = new Application('development');

// GET / && return "Hell World!" response
$app->get('/', function() use($app) {
    return $app['response']->create('Hell World!');
});

// Run the app
$app->run();

/* END OF FILE */
```
