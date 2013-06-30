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
class Plupload extends \Nette\Object
{

	/**
	 * @var string
	 */
	private $wwwDir;

	/**
	 * @var string
	 */
	private $basePath;

	/**
	 * @var string
	 */
	private $resourcesDir;

	/**
	 * @var PluploadSettings
	 */
	private $pluploadSettings;

	/**
	 * @var Uploaders\IUploader
	 */
	private $uploader;

	/**
	 * @var bool
	 */
	private $useMagic = true;
	
	/**
	 * @var \Symfony\Component\Filesystem\Filesystem
	 */
	private $io;
	
	
	
	/**
	 */
	public function __construct()
	{
		$this->io = new \Symfony\Component\Filesystem\Filesystem;
	}



	/**
	 * @return Widget\JQueryUIWidget
	 */
	public function getComponent()
	{
		return new Widget\JQueryUIWidget($this);
	}



	/*********************** magic ***********************/



	/**
	 * @return bool
	 */
	public function isMagical()
	{
		return (bool) $this->useMagic;
	}



	/**
	 * @return Plupload  provides fluent interface
	 */
	public function disableMagic()
	{
		$this->useMagic = FALSE;
		return $this;
	}



	/*********************** setters ***********************/



	/**
	 * @param string $dir
	 * @return Plupload  provides fluent interface
	 */
	public function setWwwDir($dir)
	{
		$this->wwwDir = $dir;
		return $this;
	}



	/**
	 * @param string $basePath
	 * @return Plupload  provides fluent interface
	 */
	public function setBasePath($basePath)
	{
		$this->basePath = $basePath;
		return $this;
	}



	/**
	 * @param string $dir
	 * @return Plupload  provides fluent interface
	 */
	public function setResourcesDir($dir)
	{
		if (!file_exists($dir)) {
			$this->io->mkdir($dir);
		}
		
		$this->resourcesDir = $dir;
		return $this;
	}



	/*********************** getters ***********************/



	/**
	 * @return string
	 */
	public function getResourcesDir()
	{
		if ($this->isMagical()) {
			if(!file_exists($this->resourcesDir . '/copied')) {
				$this->io->mirror(__DIR__ . '/front', $this->resourcesDir, NULL, array('override' => TRUE));
			}
		}

		return $this->basePath.str_replace($this->wwwDir, '', $this->resourcesDir);
	}

	

	/**
	 * @return Uploaders\IUploader
	 */
	public function getUploader()
	{
		if ($this->uploader === NULL) {
			$this->uploader = new Uploaders\DefaultUploader($this->io);
		}
		
		return $this->uploader;
	}	
	


	/**
	 * @return PluploadSettings
	 */
	public function getSettings()
	{
		if ($this->pluploadSettings === NULL) {
			$this->pluploadSettings = new PluploadSettings;
		}
		
		return $this->pluploadSettings;
	}



	/*********************** upload ***********************/



	public function upload()
	{
		$this->uploader->upload();
	}



	/*********************** helpers ***********************/



	/**
	 * @param string $source
	 * @param string $dest
	 * @param bool $override
	 */
	public static function copy($source, $dest, $override = true)
	{
		$source = new \Kdyby\Filesystem\Dir($source);
		return $source->mirror($source, $dest, NULL, array('override' => $override));
	}

}
