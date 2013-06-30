<?php

namespace JedenWeb\Plupload\DI;

use Nette;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class PluploadExtension extends Nette\DI\CompilerExtension
{
	
	/**
	 * @var array
	 */
	private $defauls = array(
		'resourcesDir' => '%wwwDir%/mfu',
		'tempDir' => '%tempDir%/upload',
		'magic' => TRUE,
		'runtimes' => array(
			'html5',
		),
		'maxFileSize' => '10mb',
		'maxChunkSize' => '1mb',
	);
	
	
	/**
	 * 
	 */
	public function loadConfiguration()
	{
		$config = $this->getConfig($this->defauls);
		$container = $this->getContainerBuilder();
		
		$container->addDefinition($this->prefix('uploader'))
				->setClass('JedenWeb\Plupload\Uploaders\DefaultUploader', array($config['tempDir']));
		
		$container->addDefinition($this->prefix('settings'))
				->setClass('JedenWeb\Plupload\Settings')
				->addSetup('setRuntimes', array($config['runtimes']))
				->addSetup('setMaxFileSize', array($config['maxFileSize']))
				->addSetup('setMaxChunkSize', array($config['maxChunkSize']));
		
		$container->addDefinition($this->prefix('plupload'))
				->setClass('JedenWeb\Plupload\Plupload');
		
		$container->addDefinition($this->prefix('magic'))
				->setClass('JedenWeb\Plupload\Magic', array($container->parameters['wwwDir'], $config['resourcesDir']));
		
		if ($config['magic'] === TRUE) {
			$container->getDefinition($this->prefix('magic'))
					->addSetup('cast');
		}
	}
	
}
