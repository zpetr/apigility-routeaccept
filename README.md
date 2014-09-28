apigility-routeaccept
=====================

Routing based on Accept header of request

----------
This module give you possibility to have any number of APIs with the same 'route' name.

### Example:
Imagine that you have API ***car*** and API ***moto***. If you create route named ***brand*** for both APIs, apigility will redirect you always to the last one. RouteAccept module offer a possibility to use API which correspond to the Accept Header.

So,

1. request to */brand* with Accept header *application/vnd.**car**.v1+json* will give you something like:
```json
{
    "brand": [
		{
			"id": 1,
			"name": "BMW"
		},
		{
			"id": 2,
			"name": "Ford"
		},
		...
	]
}
```
2. request to */brand* with Accept header *application/vnd.**moto**.v1+json* will give you something like:
```json
{
    "brand": [
		{
			"id": 1,
			"name": "Harley-Davidson"
		},
		{
			"id": 2,
			"name": "Kawasaki"
		},
		...
	]
}
```  

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
2. Create APIs and routes (you can use the same names for different routes now)
3. Use it!