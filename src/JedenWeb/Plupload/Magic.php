<?php

namespace JedenWeb\Plupload;

use Nette;
use Nette\Http\Request;

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
	public $loadedJs = array();

	/** @var array */
	public $loadedCss = array();
	
	/** @var string */
	private $fullPath;
	
	/** @var string */
	private $resourcesDir;

	

	/**
	 * @param string $wwwDir
	 * @param string $resourcesDir
	 * @param \Nette\Http\Request $httpRequest
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
		Nette\Utils\FileSystem::createDir($this->fullPath);
		
		if (!file_exists($this->fullPath . '/copied')) {
			Nette\Utils\FileSystem::copy(__DIR__ . '/resources', $this->fullPath);
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
		if(!in_array($shortPath, $this->loadedJs)) {
			$this->loadedJs[] = $shortPath;
			return \Nette\Utils\Html::el('script')
					->type('text/javascript')
					->src($this->resourcesDir.$shortPath);
		}
	}



	/**
	 * @param string $shortPath
	 * @return \Nette\Utils\Html
	 */
	public function registerCss($shortPath)
	{
		if(!in_array($shortPath, $this->loadedCss)) {
			$this->loadedCss[] = $shortPath;
			return \Nette\Utils\Html::el('link')
					->rel('stylesheet')
					->type('text/css')
					->href($this->resourcesDir.$shortPath);
		}
	}

}
