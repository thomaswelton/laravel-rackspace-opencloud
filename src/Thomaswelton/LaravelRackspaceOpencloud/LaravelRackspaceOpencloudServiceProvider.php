<?php namespace Thomaswelton\LaravelRackspaceOpencloud;

use Illuminate\Support\ServiceProvider;

class LaravelRackspaceOpencloudServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('thomaswelton/laravel-rackspace-opencloud');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['open-cloud'] = $this->app->share(function($app)
        {
            return new OpenCloud;
        });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
