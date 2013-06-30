<?php

namespace JedenWeb\Plupload\DI;

use Nette;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class PluploadExtension extends Nette\Config\CompilerExtension
{
	
	/** @var array */
	private $defauls = array(
		'uploader' => 'JedenWeb\Plupload\Uploaders\DefaultUploader',
		'settings' => array(
			'runtimes' => array(
				'html5'
			),
			'maxFileSize' => '10mb',
			'maxChunkSize' => '1mb',
		),
		'resourcesDir' => '%wwwDir%/mfu',
		'magic' => TRUE,
	);
	
	
	public function loadConfiguration()
	{
		$config = $this->getConfig($this->defauls);
		$container = $this->getContainerBuilder();
		
		$container->addDefinition($this->prefix('uploader'))
				->setClass($config['uploader'])
				->addSetup('setTempDir', array('%tempDir%/upload'));
		
		$container->addDefinition($this->prefix('settings'))
				->setClass('JedenWeb\Plupload\PluploadSettings')
				->addSetup('setRuntimes', array($config['settings']['runtimes']))
				->addSetup('setMaxFileSize', array());
		
		$container->addDefinition($this->prefix('plupload'))
				->setClass('JedenWeb\Plupload\Plupload')
				->addSetup('setWwwDir', array('%wwwDir%'))
				->addSetup('setResourcesDir', array($config['resourcesDir']));
		
		$container->addDefinition($this->prefix('widget'))
				->setClass('JedenWeb\Plupload\Widget\JQueryUIWidget');
	}
	
	
	/**
	 * @param Nette\Utils\PhpGenerator\ClassType $class
	 */
	public function afterCompile(Nette\Utils\PhpGenerator\ClassType $class)
	{
		$config = $this->getConfig($this->defauls);
		$initialize = $class->methods['initialize'];
		
		$initialize->addBody('$this->getService(?)->setBasePath($this->application->presenter->template->basePath);', array("$this->name.plupload"));
		
		if ($config['magic'] === FALSE) {
			$initialize->addBody('$this->getService(?)->disableMagic()', array("$this->name.plupload"));
		}
	}
	
}
