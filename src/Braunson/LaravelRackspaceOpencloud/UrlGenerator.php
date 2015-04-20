<?php namespace Thomaswelton\LaravelRackspaceOpencloud;

use \File;
use \Request;
use Illuminate\Routing\UrlGenerator as LaravelGenerator;

class UrlGenerator extends LaravelGenerator{

	/**
     * Generate a URL to an application asset.
     *
     * @param  string  $path
     * @param  bool    $secure
     * @return string
     */
    public function asset($path, $secure = null)
    {
        // Start looking for a CDN json file
        $checkDir = dirname(public_path() . '/' . $path);

        // Look up through the directories looking for a
        // CDN json file
        while($checkDir !== public_path()){
            $cdnJsonPath = $checkDir . '.cdn.json';

            if(File::isFile($cdnJsonPath)){
                $json = File::get($cdnJsonPath);
                $cdnObject = json_decode($json);

                $baseUrl = ($secure || Request::secure()) ? $cdnObject->https : $cdnObject->http;

                return $baseUrl . '/'. $cdnObject->prefix . '/' . $path;
            }

            $checkDir = dirname($checkDir);
        }

        return parent::asset($path, $secure);
    }

}
