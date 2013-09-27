<?php namespace Thomaswelton\LaravelRackspaceOpencloud\Commands;

use \File;
use \Str;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CdnSyncCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'cdn:sync';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Upload a file or directory to a CDN';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
    {
        $opencloud = \App::make('open-cloud');
        $container_name = \Config::get('laravel-rackspace-opencloud::container');
        $container = $opencloud->getContainer($container_name);

        // Get directory or file path
        $path = base_path() . '/' . $this->argument('path');
        $path_trim = base_path() . '/' . $this->option('trim');

        $this->info('Syncing to CDN: ' . $path);

        // Exit if not exists
        if(!File::isDirectory($path)){
            return $this->error('Path is not a directory');
        }

        $files = File::allFiles($path);

        // Get an md5 of a concatenated md5_file hash of all files
        $directoryHash = md5(array_reduce($files, function($hash, $file){
            // Do not include .cdn.json files in the directory hash
            if(substr($file, -9) == '.cdn.json'){
                return $hash;
            }

            $hash .= md5_file($file);
            return $hash;
        }));

        $fileCount = count($files);
        $this->info('Found ' . $fileCount . ' ' . Str::plural('file', $fileCount));

        $cdnFile = $opencloud->uploadDir($container_name, $path, $directoryHash, $path_trim);

        $cdnJsonArray = array(
            'http' => $container->PublicURL(),
            'https' => $container->SSLURI(),
            'prefix' => $directoryHash,
            'created' => time()
        );

        File::put($path . '.cdn.json', json_encode($cdnJsonArray));
    }

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('path', InputArgument::REQUIRED, 'File or directory path'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('trim', '', InputOption::VALUE_OPTIONAL, 'String to trim from directory when uploading', null),
		);
	}

}
