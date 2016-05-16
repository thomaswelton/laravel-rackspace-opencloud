<?php namespace Thomaswelton\LaravelRackspaceOpencloud;

use \Config;
use \File;
use Alchemy\Zippy\Zippy;

define('RAXSDK_TIMEOUT', Config::get('laravel-rackspace-opencloud::raxsdk_timeout'));

class OpenCloud extends \OpenCloud\Rackspace{

	public $region = null;

	function __construct(){

		$this->region = Config::get('laravel-rackspace-opencloud::region');
		$authUrl = ($this->region == 'LON') ? 'https://lon.identity.api.rackspacecloud.com/v2.0/' : 'https://identity.api.rackspacecloud.com/v2.0/';

		parent::__construct($authUrl, array(
			'username' => Config::get('laravel-rackspace-opencloud::username'),
			'apiKey' => Config::get('laravel-rackspace-opencloud::apiKey')
		));
	}

	public function getObjectStore(){
		return $this->ObjectStore('cloudFiles', $this->region);
	}

	public function getContainer($name){
		// create a new container
		$container = $this->getObjectStore()->Container();
		$container->Create(array('name' => $name ));

		// publish it to the CDN with 1 year TTL
		$ttl = 60 * 60 * 24 * 365;
		$container->PublishToCDN($ttl);

		return $container;
	}

	public function upload($container, $file, $name = null)
	{
		if(is_object($file) && get_class($file) == 'Symfony\Component\HttpFoundation\File\UploadedFile'){
			// Passed with was a file upload from a form. Used the PHP tmp name
			// and guess an extension
			if(is_null($name)){
				$name = basename($file) . '.' . $file->guessExtension();
			}

			return $this->createDataObject($container, $file->getRealPath(), $name);

		}else if(File::isFile($file)){
			// Passed file was a string to the file path
			return $this->createDataObject($container, $file, $name);

		}else{
			throw new Exception("OpenCloud::upload file not found", 1);
		}
	}

    // Create and archive and upload a whole directory
    // $dir - Directory to upload
    // $cdnDir - Directory on the CDN to upload to
    // $dirTrim - Path segments to trim from the dir path when on the CDN
    public function uploadDir($container, $dir, $cdnDir = '', $dirTrim = ''){
        $temp_file =  storage_path() . '/CDN-' . time() . '.tar.gz';

        $zip_dir_name = (0 === strpos($dir, $dirTrim)) ? substr($dir, strlen($dirTrim) + 1) : $dir;

        $zippy = Zippy::load();
        // creates an archive.zip that contains a directory "folder" that contains
        // files contained in "/path/to/directory" recursively
        $archive = $zippy->create($temp_file, array(
            $cdnDir . '/' . $zip_dir_name => $dir
        ), true);

        $cdnFile = $this->createDataObject($container, $temp_file, '/', 'tar.gz');

        File::delete($temp_file);

        return $cdnFile;
    }

    public function exists($container, $file){
        $container = $this->getContainer($container);
        try{
            return $container->DataObject($file);
        }catch(\OpenCloud\Common\Exceptions\ObjFetchError $e){
            return false;
        }
    }

	public function createDataObject($container, $filePath, $fileName = null, $extract = null)
	{
		if(is_null($fileName)){
			$fileName = basename($filePath);
		}

		$container = $this->getContainer($container);

		$headers = array(
			"Access-Control-Allow-Origin" => "*"
		);

		$object = $container->DataObject();
		$object->Create(array('name'=> $fileName, 'extra_headers' => $headers), $filePath, $extract);

		return $object;
	}
  
  public function delete($container, $file){
      $container = $this->getContainer($container);
      //if file is fed with full url, shorten to last component
        $file = explode('/',$file);
        $file = end($file);
      try{
          return $container->DataObject($file)->delete();
      }catch(\OpenCloud\Common\Exceptions\ObjFetchError $e){
          return $e;
      }
  }
}

