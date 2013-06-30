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
class PluploadSettings extends Nette\Object
{

	/**
	 * @var array
	 */
	private $runtimes = array('html5');

	/**
	 * @var string
	 */
	private $maxFileSize = '10mb';

	/**
	 * @var string
	 */
	private $maxChunkSize = '5mb';



	/*********************** setters ***********************/



	/**
	 * @param array $runtimes
	 * @return \PavelJurasek\Plupload\PluploadSettings
	 * @throws Nette\InvalidArgumentException
	 */
	public function setRuntimes(array $runtimes)
	{
		$possible = array('gears', 'flash', 'silverlight', 'browserplus', 'html5');
		foreach($runtimes as $runtime) {
			if(!in_array($runtime, $possible)) {
				throw new Nette\InvalidArgumentException('There is no runtime called: '.$runtime);
			}
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
		return $this->runtimes;
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
