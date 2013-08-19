<?php namespace Thomaswelton\LaravelRackspaceOpencloud\Facades;

use Illuminate\Support\Facades\Facade;

class OpenCloud extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'open-cloud'; }

}
