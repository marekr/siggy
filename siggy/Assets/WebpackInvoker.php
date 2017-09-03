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

use Symfony\Component\Process\ProcessBuilder;
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
        $pb = new ProcessBuilder(array_merge(
            array($this->webPackPath),
            array('--config', $this->configPath)
        ));

        if (null !== $this->outputFileName) {
            $pb->add('--output-filename')->add($this->outputFileName);
		}
		
        if (null !== $this->outputPath) {
            $pb->add('--output-path')->add($this->outputPath);
		}

		$pb->setEnv('NODE_ENV','production');

		$proc = $pb->getProcess();
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