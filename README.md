# Installation

Update your `composer.json` file to include this package as a dependency
```json
"braunson/laravel-rackspace-opencloud": "dev-master"
```

Register the OpenCloud service provider by adding it to the providers array in your `app/config/app.php` file.
```php
'providers' => array(
	Braunson\LaravelRackspaceOpencloud\LaravelRackspaceOpencloudServiceProvider
)
```

Alias the OpenCloud facade by adding it to the aliases array in the `app/config/app.php` file.
```php
'aliases' => array(
	'OpenCloud' => 'Braunson\LaravelRackspaceOpencloud\Facades\OpenCloud'
)
```

## Configuration

Copy the config file into your project by running
```
php artisan config:publish braunson/laravel-rackspace-opencloud
```

Edit the config file to include your username, api key, region and url (internal or public).

# Usage

## Artisan Commands

Upload files via the command line.

Synchronize a whole directory. Copies all files to /public/assets
```
php artisan cdn:sync public/assets
```

Copies all files to /assets trimming 'public' from the path
```
php artisan cdn:sync public/assets --trim=public
```

The sync command will save a file adjacent to the synchronized directory. It contains the http and https urls for your container. Along with a md5 hash of the directory.
In this way when a file changes inside a directory and is reuploaded you get a new cache busted URL.

If you are using the URL helper then it will return a CDN url for a file, if it finds a *.cdn.json file adjacent to one of it's parent directories.

```
URL::asset('assets/image.jpg');
```

You should be able to run `php artisan cdn:sync public/assets --trim=public` before or during a deployment and once complete all files being called by `URL::asset()` will return a CDN resource


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
	// Do something with $cdnUrlth

	return Redirect::to('/upload');
});
```
## Delete from CDN

```php
OpenCloud::delete($container, $file)
```
