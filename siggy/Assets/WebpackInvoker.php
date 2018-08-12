<?php

/*
 *
 * Stolen from assetic.
 *
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Siggy\Assets;

use Symfony\Component\Process\Process;
use Siggy\Assets\CompilerException;

/**
 * Filter for the Google Closure Compiler JAR.
 *
 * @link https://developers.google.com/closure/compiler/
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class WebpackInvoker
{
    protected $outputFileName;
    protected $outputPath;
    private $configPath;
    private $webPackPath;
    private $flagFile;

	private $sourceMap = '';

    public function __construct($configPath, $sourceMap = '', $webPackPath = '/usr/bin/webpack')
    {
        $this->configPath = $configPath;
        $this->webPackPath = $webPackPath;
		$this->sourceMap = $sourceMap;
	}

    public function compile(array $assets = [], array $extra = [])
    {
		$args = [
			$this->webPackPath,
			'--config',
			$this->configPath,
		];

        $pb = new Process(array_merge(
            array($this->webPackPath),
            array('--config', $this->configPath)
        ));

        if (null !== $this->outputFileName) {
			$args = array_merge($args, [
				'--output-filename',
				$this->outputFileName
			]);
		}
		
        if (null !== $this->outputPath) {
			$args = array_merge($args, [
				'--output-path',
				$this->outputPath
			]);
		}

		$env = ['NODE_ENV' => 'production'];
		
		$timeout = null; //to disable
		$proc = new Process($args, null, $env, null, $timeout);
		$proc->inheritEnvironmentVariables(true);
		$code = $proc->run();

		if (0 !== $code) {
			throw CompilerException::fromProcess($proc)->setInput(implode(",",$assets));
		}

		return $proc->getOutput();
	}
	
	public function setOutputFileName($outputFileName)
    {
        $this->outputFileName = $outputFileName;
	}
	
	public function setOutputPath($outputPath)
    {
        $this->outputPath = $outputPath;
    }
}