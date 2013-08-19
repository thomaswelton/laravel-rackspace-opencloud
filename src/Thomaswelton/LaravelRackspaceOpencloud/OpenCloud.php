<?php namespace Thomaswelton\LaravelRackspaceOpencloud;

define('RAXSDK_TIMEOUT', 120);

use \Config;

class OpenCloud extends \OpenCloud\Rackspace{

	function __construct(){
		$config = array(
			'username' => Config::get('laravel-rackspace-opencloud::username'),
			'apiKey' => Config::get('laravel-rackspace-opencloud::apiKey')
		);

		parent::__construct(Config::get('laravel-rackspace-opencloud::authUrl'), $config);
	}
}

