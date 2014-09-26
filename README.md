apigility-routeaccept
=====================

Routing based on Accept header of request


### Installation
Install composer in your project

    curl -s http://getcomposer.org/installer | php

Define dependencies in your composer.json file

```json
{
    "require": {
        "zpetr/apigility-routeaccept" : "dev-master"
    }
}
```

Finally install dependencies

    php composer.phar install

or update it

    php composer.phar update

### Usage
1. Add *zPetr\\RouteAccept* to application.config.php:
```php
	return array(
    	'modules' => array(
        	...,
            'zPetr\\RouteAccept',
            ....
		)
	)     
```