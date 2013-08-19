[![Build Status](https://travis-ci.org/thomaswelton/laravel-rackspace-opencloud.png?branch=master)](https://travis-ci.org/thomaswelton/laravel-rackspace-opencloud)
[![Latest Stable Version](https://poser.pugx.org/thomaswelton/laravel-rackspace-opencloud/v/stable.png)](https://packagist.org/packages/thomaswelton/laravel-rackspace-opencloud)
[![Total Downloads](https://poser.pugx.org/thomaswelton/laravel-rackspace-opencloud/downloads.png)](https://packagist.org/packages/thomaswelton/laravel-rackspace-opencloud)


# Installation

Update your `composer.json` file to include this package as a dependency
```json
"thomaswelton/laravel-rackspace-opencloud": "dev-master"
```

Register the OpenCloud service provider by adding it to the providers array in your `app/config/app.php` file.
```php
'providers' => array(
	Thomaswelton\LaravelRackspaceOpencloud\LaravelRackspaceOpencloudServiceProvider
)
```

Alias the OpenCloud facade by adding it to the aliases array in the `app/config/app.php` file.
```php
'aliases' => array(
	'OpenCloud' => 'Thomaswelton\LaravelRackspaceOpencloud\Facades\OpenCloud'
)
```

# Configuration

Copy the config file into your project by running
```
php artisan config:publish thomaswelton/laravel-rackspace-opencloud
```

Edit the config file to include your username, api key and auth url. The auth URL should be one of
- https://identity.api.rackspacecloud.com/v2.0/
- https://lon.identity.api.rackspacecloud.com/v2.0/
