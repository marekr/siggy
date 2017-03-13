<?php

namespace Siggy\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Config;

use Assetic\AssetManager;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;
use Assetic\Factory\AssetFactory;
use Assetic\FilterManager;
use Assetic\Filter\GoogleClosure;

use Siggy\AssetHelpers;

define('VERSION', file_get_contents('VERSION'));

class AssetsCompileCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'assets:compile';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Compile assets';

	/**
	 * @var array
	 */
	protected $config;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->config  = config('assets');
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		//clean out the old files...
		$files = rglob(AssetHelpers::joinPaths($this->config['outputPath'], '*'));
		foreach($files as $file){
			if(is_file($file))
			{
				unlink($file);
			}
		}

		foreach($files as $file){
			if(is_dir($file))
			{
				@rmdir($file);
			}
		}
		
		$cache = [];
		foreach($this->config['assets'] as $asset)
		{
			$this->info("Compiling {$asset['virtualName']}");
			$resultingAsset = null;

			$assetName = AssetHelpers::jsAssetFilename($asset['virtualName'],VERSION);
			$filePath = AssetHelpers::joinPaths($this->config['outputPath'], $assetName);

			if($asset['type'] == 'js')
			{
				$files = [];
				foreach($asset['files'] as $file)
				{
					$sourceFile = AssetHelpers::joinPaths($asset['basePath'],$file);
					$files[] = new FileAsset( $sourceFile );

					$destPath = AssetHelpers::joinPaths($this->config['outputPath'], $file);
					@mkdir(dirname($destPath), 0777, true);
					copy($sourceFile, $destPath);
				}
				
				$resultingAsset = new AssetCollection($files, [
					new \Siggy\Assetic\CompilerJarFilter($this->config['closurePath'], $filePath.'.map', 'java'),
				]);
			}
			else{
				throw new \Exception("Unknown asset type");
				$this->error("Unknown asset type {$asset['type']}");
			}

			$result = $resultingAsset->dump();
			$result .= "\n//# sourceMappingURL={$assetName}.map\n";

			if (file_put_contents($filePath, $result) === false) {
				$this->error("Error writing asset file: {$file_path}");
			}
			$this->info("Wrote {$filePath}");

			$cache[ $asset['virtualName'] ] = $filePath;
		}
		
		file_put_contents(AssetHelpers::joinPaths($this->config['outputPath'],'assets.json'), json_encode($cache));
	}
}
