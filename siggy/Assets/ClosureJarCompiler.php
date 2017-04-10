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
class ClosureJarCompiler
{
    // compilation levels
    const COMPILE_WHITESPACE_ONLY = 'WHITESPACE_ONLY';
    const COMPILE_SIMPLE_OPTIMIZATIONS = 'SIMPLE_OPTIMIZATIONS';
    const COMPILE_ADVANCED_OPTIMIZATIONS = 'ADVANCED_OPTIMIZATIONS';
    // formatting modes
    const FORMAT_PRETTY_PRINT = 'pretty_print';
    const FORMAT_PRINT_INPUT_DELIMITER = 'print_input_delimiter';
    // warning levels
    const LEVEL_QUIET = 'QUIET';
    const LEVEL_DEFAULT = 'DEFAULT';
    const LEVEL_VERBOSE = 'VERBOSE';
    // languages
    const LANGUAGE_ECMASCRIPT3 = 'ECMASCRIPT3';
    const LANGUAGE_ECMASCRIPT5 = 'ECMASCRIPT5';
    const LANGUAGE_ECMASCRIPT5_STRICT = 'ECMASCRIPT5_STRICT';
	
    protected $timeout;
    protected $compilationLevel;
    protected $jsExterns;
    protected $externsUrl;
    protected $excludeDefaultExterns;
    protected $formatting;
    protected $useClosureLibrary;
    protected $warningLevel;
    protected $language;

    private $jarPath;
    private $javaPath;
    private $flagFile;

	private $sourceMap = '';

    public function __construct($jarPath, $sourceMap = '', $javaPath = '/usr/bin/java')
    {
        $this->jarPath = $jarPath;
        $this->javaPath = $javaPath;
		$this->sourceMap = $sourceMap;
    }

    public function setFlagFile($flagFile)
    {
        $this->flagFile = $flagFile;
    }

    public function compile(array $assets, array $extra = [])
    {
        $is64bit = PHP_INT_SIZE === 8;
        $cleanup = array();

        $pb = new ProcessBuilder(array_merge(
            array($this->javaPath),
            $is64bit
                ? array('-server', '-XX:+TieredCompilation')
                : array('-client', '-d32'),
            array('-jar', $this->jarPath)
        ));

        if (null !== $this->timeout) {
            $pb->setTimeout($this->timeout);
        }

        if (null !== $this->compilationLevel) {
            $pb->add('--compilation_level')->add($this->compilationLevel);
        }

        if (null !== $this->jsExterns) {
            $cleanup[] = $externs = FilesystemUtils::createTemporaryFile('google_closure');
            file_put_contents($externs, $this->jsExterns);
            $pb->add('--externs')->add($externs);
        }

        if (null !== $this->externsUrl) {
            $cleanup[] = $externs = FilesystemUtils::createTemporaryFile('google_closure');
            file_put_contents($externs, file_get_contents($this->externsUrl));
            $pb->add('--externs')->add($externs);
        }

        if (null !== $this->excludeDefaultExterns) {
            $pb->add('--use_only_custom_externs');
        }

        if (null !== $this->formatting) {
            $pb->add('--formatting')->add($this->formatting);
        }

        if (null !== $this->useClosureLibrary) {
            $pb->add('--manage_closure_dependencies');
        }

        if (null !== $this->warningLevel) {
            $pb->add('--warning_level')->add($this->warningLevel);
        }

        if (null !== $this->language) {
            $pb->add('--language_in')->add($this->language);
        }

        if (null !== $this->flagFile) {
            $pb->add('--flagfile')->add($this->flagFile);
        }

		if(!empty($this->sourceMap)) {
            $pb->add('--create_source_map')->add($this->sourceMap);
		}

		if(isset($extra['source_map_location_mapping']))
		{
			$pb->add('--source_map_location_mapping')->add($extra['source_map_location_mapping']);
		}

	//	$source = $asset->getSourceDirectory() . DIRECTORY_SEPARATOR . $asset->getSourcePath();
		
		foreach($assets as $asset)
		{
			$pb->add('--js')->add($asset);
		}
		$proc = $pb->getProcess();
		$code = $proc->run();

		if (0 !== $code) {
			throw CompilerException::fromProcess($proc)->setInput(implode(",",$assets));
		}

		return $proc->getOutput();
	}
	
	public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    public function setCompilationLevel($compilationLevel)
    {
        $this->compilationLevel = $compilationLevel;
    }

    public function setJsExterns($jsExterns)
    {
        $this->jsExterns = $jsExterns;
    }

    public function setExternsUrl($externsUrl)
    {
        $this->externsUrl = $externsUrl;
    }

    public function setExcludeDefaultExterns($excludeDefaultExterns)
    {
        $this->excludeDefaultExterns = $excludeDefaultExterns;
    }

    public function setFormatting($formatting)
    {
        $this->formatting = $formatting;
    }

    public function setUseClosureLibrary($useClosureLibrary)
    {
        $this->useClosureLibrary = $useClosureLibrary;
    }

    public function setWarningLevel($warningLevel)
    {
        $this->warningLevel = $warningLevel;
    }

    public function setLanguage($language)
    {
        $this->language = $language;
    }
}