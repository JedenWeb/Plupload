<?php

namespace JedenWeb\Plupload;

use Nette;

/**
 * This file is a part of Plupload component for Nette Framework.
 *
 * @author Nikolas Tsiongas
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 * @package Plupload component
 * @license New BSD License
 */
class Magic extends \Nette\Object
{

	/**
	 * @var string
	 */
	private $resourcesDir;

	/**
	 * @var array
	 */
	public $loadedJs = array();

	/**
	 * @var array
	 */
	public $loadedCss = array();




	/*********************** setters ***********************/



	/**
	 * @param string $resourcesDir
	 * @return \PavelJurasek\Plupload\Magic
	 */
	public function setResourcesDir($resourcesDir)
	{
		$this->resourcesDir = $resourcesDir;
		return $this;
	}



	/*********************** loading ***********************/



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
