[![Build Status](https://travis-ci.org/thomaswelton/laravel-rackspace-opencloud.png?branch=master)](https://travis-ci.org/thomaswelton/laravel-rackspace-opencloud)
[![Latest Stable Version](https://poser.pugx.org/thomaswelton/laravel-rackspace-opencloud/v/stable.png)](https://packagist.org/packages/thomaswelton/laravel-rackspace-opencloud)
[![Total Downloads](https://poser.pugx.org/thomaswelton/laravel-rackspace-opencloud/downloads.png)](https://packagist.org/packages/thomaswelton/laravel-rackspace-opencloud)
[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/thomaswelton/laravel-rackspace-opencloud/trend.png)](https://bitdeli.com/free "Bitdeli Badge")


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

## Configuration

Copy the config file into your project by running
```
php artisan config:publish thomaswelton/laravel-rackspace-opencloud
```

Edit the config file to include your username, api key and region.

# Usage

## Upload to CDN

```php
OpenCloud::upload($container, $file, $name = null)
```

- $container - (string) Name of the container to upload into
- $file - (string / UploadedFile) Path to file, or instance of 'Symfony\Component\HttpFoundation\File\UploadedFile' as returned by Input::file()
- $name - (string) Optional file name to be used when saving the file to the CDN.

Example:
```php
Route::post('/upload', function()
{
	if(Input::hasFile('image')){
		$file = OpenCloud::upload('my-container', Input::file('image'));
	}

	$cdnUrl = $file->PublicURL();
	// Do something with $cdnUrl

	return Redirect::to('/upload');
});
```
