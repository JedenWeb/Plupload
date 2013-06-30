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
class Settings extends Nette\Object
{

	/** @var array */
	private $runtimes = array('html5');

	/** @var string */
	private $maxFileSize;

	/** @var string */
	private $maxChunkSize;



	/*********************** setters ***********************/


	/**
	 * @param string|array $runtimes
	 * @return Settings  provides fluent interface
	 * @throws Nette\InvalidArgumentException
	 */
	public function setRuntimes($runtimes)
	{
		if (is_string($runtimes)) {
			$runtimes = array($runtimes);
		}
		
		$possible = array('gears', 'flash', 'silverlight', 'browserplus', 'html5');
		
		if (($invalid = array_diff($runtimes, $possible)) !== array()) {
			throw new Nette\InvalidArgumentException('There is no runtime called: '. implode(', ', $invalid));
		}
		
		$this->runtimes = $runtimes;
		return $this;
	}



	/**
	 * @param string $expr
	 * @return \PavelJurasek\Plupload\PluploadSettings
	 */
	public function setMaxFileSize($expr)
	{
		$this->maxFileSize = $expr;
		return $this;
	}



	/**
	 * @param string $expr
	 * @return \PavelJurasek\Plupload\PluploadSettings
	 */
	public function setMaxChunkSize($expr)
	{
		$this->maxChunkSize = $expr;
		return $this;
	}



	/*********************** getters ***********************/
	
	

	/**
	 * @return array
	 */
	public function getRuntimes()
	{
		return (array) $this->runtimes;
	}



	/**
	 * @return string
	 */
	public function getMaxFileSize()
	{
		return $this->maxFileSize;
	}



	/**
	 * @return string
	 */
	public function getMaxChunkSize()
	{
		return $this->maxChunkSize;
	}

}
