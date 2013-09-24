<?php namespace Thomaswelton\LaravelRackspaceOpencloud;

use \File;
use \Str;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CdnUploadCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'cdn:upload';

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
        $startTime = microtime(true);
        $this->info('Start Time: ' . $startTime);

        $container_name = 'artisan_container';

        // Get directory or file path
		$path = $this->argument('path');

        $this->info('Uploading: ' . $path);

        // Exit if not exists
        if(!File::exists($path)){
            return $this->error('Path does not exist');
        }

        // Get array of SplFileInfo objects
        if(File::isDirectory($path)){
            $files = File::allFiles($path);
        }else{
            $files = array( new \SplFileInfo($path) );
        }

        // Display file count
        $fileCount = count($files);
        $this->info('Found ' . $fileCount . ' ' . Str::plural('file', $fileCount));

        $hash = '';
        foreach ($files as $file) {
            $filePath = $file->getPathName();
            $fileHash = md5_file($file);

            $this->info('File hash: ' . $fileHash);
            $hash .= $fileHash;
        }

        $directoryHash = md5($hash);
        $this->info('Directory hash: '. $directoryHash);

        $opencloud = \App::make('open-cloud');
        $container = $opencloud->getContainer($container_name);

        foreach ($files as $file) {
            $filePath = $file->getPathName();

            $this->info('Uploading ' . $filePath);
            $cdnFile = $opencloud->upload($container_name, $filePath, $directoryHash . '/' . $filePath);
        }

        $endTime = microtime(true);
        $this->info('End Time: ' . $endTime);

        $this->info('Time taken ' . ($endTime - $startTime));
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
			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}
