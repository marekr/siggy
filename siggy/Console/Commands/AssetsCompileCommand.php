<?php

namespace Siggy\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Config;

use Siggy\Assets\Helpers;
use Siggy\Assets\ClosureJarCompiler;

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
		$files = rglob(Helpers::joinPaths($this->config['outputPath'], '*'));
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
		$compiledOutput = '';
		foreach($this->config['assets'] as $asset)
		{
			$this->info("Compiling {$asset['virtualName']}");
			$resultingAsset = null;

			$assetName = Helpers::jsAssetFilename($asset['virtualName'],VERSION);
			$filePath = Helpers::joinPaths($this->config['outputPath'], $assetName);

			$remap = $asset['basePath'].'|'.$asset['publicPath'];

			if($asset['type'] == 'js')
			{
				$files = [];
				foreach($asset['files'] as $file)
				{
					$sourceFile = Helpers::joinPaths($asset['basePath'],$file);
					$files[] = $sourceFile;
				}
				
				$compiler = new ClosureJarCompiler($this->config['closurePath'], $filePath.'.map','java');
				$compiledOutput = $compiler->compile($files, ['source_map_location_mapping' => $remap]);
			}
			else{
				throw new \Exception("Unknown asset type");
				$this->error("Unknown asset type {$asset['type']}");
			}

			$result = $compiledOutput;
			$result .= "\n//# sourceMappingURL={$assetName}.map\n";

			if (file_put_contents($filePath, $result) === false) {
				$this->error("Error writing asset file: {$file_path}");
			}
			$this->info("Wrote {$filePath}");

			$cache[ $asset['virtualName'] ] = $filePath;
		}
		
		file_put_contents(Helpers::joinPaths($this->config['outputPath'],'assets.json'), json_encode($cache));
	}
}
