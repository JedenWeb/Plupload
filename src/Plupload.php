<?php

namespace PavelJurasek\Plupload;

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
	 * @param string $class
	 * @return \PavelJurasek\Plupload\class
	 */
    public function getComponent($class = '\PavelJurasek\Plupload\Widget\JQueryUIWidget')
    {
        return new $class($this);
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
	 * @return \PavelJurasek\Plupload\Plupload
	 */
    public function disableMagic()
    {
        $this->useMagic = false;
        return $this;
    }



	/*********************** setters ***********************/



	/**
	 * @param string $dir
	 * @return \PavelJurasek\Plupload\Plupload
	 */
    public function setWwwDir($dir)
    {
        $this->wwwDir = $dir;
        return $this;
    }



	/**
	 * @param string $basePath
	 * @return \PavelJurasek\Plupload\Plupload
	 */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
        return $this;
    }



	/**
	 * @param string $dir
	 * @return \PavelJurasek\Plupload\Plupload
	 */
    public function setResourcesDir($dir)
    {
        $this->resourcesDir = $this->returnDir($dir);
        return $this;
    }



	/**
	 * @param \PavelJurasek\Plupload\PluploadSettings $settings
	 * @return \PavelJurasek\Plupload\Plupload
	 */
    public function setPluploadSettings(PluploadSettings $settings)
    {
        $this->pluploadSettings = $settings;
        return $this;
    }



	/**
	 * @param \PavelJurasek\Plupload\Uploaders\IUploader $uploader
	 * @return \PavelJurasek\Plupload\Plupload
	 */
    public function setUploader(Uploaders\IUploader $uploader)
    {
        $this->uploader = $uploader;
        return $this;
    }



    /*********************** getters ***********************/



	/**
	 * @return string
	 */
    public function getResourcesDir()
    {
        if($this->isMagical()) {
            if(!file_exists($this->resourcesDir . '/copied'))
                self::copy(__DIR__ . '/front', $this->resourcesDir);
        }

        return $this->basePath.str_replace($this->wwwDir, '', $this->resourcesDir);
    }



	/**
	 * @return PluploadSettings
	 */
    public function getPluploadSettings()
    {
        return $this->pluploadSettings;
    }



    /*********************** factories ***********************/



	/**
	 * @param string $class
	 * @return \PavelJurasek\Plupload\class
	 */
    public function createSettings($class = '\PavelJurasek\Plupload\PluploadSettings')
    {
        $settings = new $class;
        $this->setPluploadSettings($settings);
        return $settings;
    }



	/**
	 * @param string $class
	 * @return \PavelJurasek\Plupload\class
	 */
    public function createUploader($class = '\PavelJurasek\Plupload\Uploaders\DefaultUploader')
    {
        $uploader = new $class;
        $this->setUploader($uploader);
        return $uploader;
    }



	/*********************** upload ***********************/



    public function upload()
    {
        $this->uploader->upload();
    }



    /*********************** helpers ***********************/



	/**
	 * @param string $dir
	 * @return string
	 */
    private function returnDir($dir)
    {
        if( is_dir($dir) ) {
            return $dir;
        } else {
            if($this->isMagical())
                mkdir($dir, 0, true);
            return $dir;
        }
    }



	/**
	 * @param string $source
	 * @param string $dest
	 * @param bool $overwrite
	 */
    public static function copy($source, $dest, $overwrite = true)
	{
        $dir = opendir($source);
        @mkdir($dest);
        while(false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if(is_dir($source . '/' . $file)) {
                    self::copy($source . '/' . $file, $dest . '/' . $file);

                } else {
                    if($overwrite || !file_exists($dest . '/' . $file)) {
                        copy($source . '/' . $file, $dest . '/' . $file);
                    }
                }
            }
        }
        closedir($dir);
    }

}
