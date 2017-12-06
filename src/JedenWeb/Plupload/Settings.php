<?php

namespace JedenWeb\Plupload;

use Nette;

/**
 * This file is a part of Plupload component for Nette Framework.
 *
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 * @author Nikolas Tsiongas
 */
class Settings
{

	use Nette\SmartObject;

	/** @var array */
	private $runtimes = array('html5');

	/** @var string */
	private $maxFileSize;

	/** @var string */
	private $maxChunkSize;



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
	 * @param string $size
	 * @return \JedenWeb\Plupload\Settings  provides fluent interface
	 */
	public function setMaxFileSize($size)
	{
		$this->maxFileSize = $size;
		return $this;
	}



	/**
	 * @param string $size
	 * @return \JedenWeb\Plupload\Settings  provides fluent interface
	 */
	public function setMaxChunkSize($size)
	{
		$this->maxChunkSize = $size;
		return $this;
	}





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
