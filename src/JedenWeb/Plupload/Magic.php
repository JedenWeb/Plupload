<?php

namespace JedenWeb\Plupload;

use Nette;
use Nette\Http\Request;
use Nette\Utils\FileSystem;
use Nette\Utils\Html;

/**
 * This file is a part of Plupload component for Nette Framework.
 *
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 * @author Nikolas Tsiongas
 */
class Magic extends Nette\Object
{
	
	/** @var bool */
	private $useMagic = FALSE;

	/** @var array */
	public $loadedJs = [];

	/** @var array */
	public $loadedCss = [];
	
	/** @var string */
	private $fullPath;
	
	/** @var string */
	private $resourcesDir;
	

	/**
	 * @param string $wwwDir
	 * @param string $resourcesDir
	 * @param Request $httpRequest
	 */
	public function __construct($wwwDir, $resourcesDir, Request $httpRequest)
	{
		$this->fullPath = $resourcesDir;
		
		$basePath = preg_replace('#https?://[^/]+#A', '', rtrim($httpRequest->getUrl()->getBaseUrl(), '/'));
		$this->resourcesDir = $basePath.str_replace($wwwDir, '', $resourcesDir);
	}
	
	
	/**
	 * @return Magic  provides fluent interface
	 */
	public function cast()
	{
		FileSystem::createDir($this->fullPath);
		
		if (!file_exists($this->fullPath . '/copied')) {
			FileSystem::copy(__DIR__ . '/resources', $this->fullPath);
		}
		
		$this->useMagic = TRUE;

		return $this;
	}
	
	
	/**
	 * @return bool
	 */
	public function isMagical()
	{
		return $this->useMagic;
	}
	
	
	/**
	 * @return string
	 */
	public function getResourcesDir()
	{
		return $this->resourcesDir;
	}


	/**
	 * @param string $shortPath
	 * @return \Nette\Utils\Html
	 */
	public function registerJs($shortPath)
	{
		if (!in_array($shortPath, $this->loadedJs)) {
			$this->loadedJs[] = $shortPath;
			return Html::el('script')
				->setAttribute('type', 'text/javascript')
				->setAttribute('src', $this->resourcesDir.$shortPath);
		}
	}


	/**
	 * @param string $shortPath
	 * @return \Nette\Utils\Html
	 */
	public function registerCss($shortPath)
	{
		if (!in_array($shortPath, $this->loadedCss)) {
			$this->loadedCss[] = $shortPath;
			return Html::el('link')
				->setAttribute('rel', 'stylesheet')
				->setAttribute('type', 'text/css')
				->href($this->resourcesDir.$shortPath);
		}
	}

}
