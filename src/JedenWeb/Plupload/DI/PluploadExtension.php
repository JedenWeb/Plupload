<?php

namespace JedenWeb\Plupload\DI;

use JedenWeb\Plupload\Magic;
use JedenWeb\Plupload\Plupload;
use JedenWeb\Plupload\Settings;
use JedenWeb\Plupload\Uploaders\DefaultUploader;
use Nette\DI\CompilerExtension;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class PluploadExtension extends CompilerExtension
{

	/**
	 * @var array
	 */
	private $defauls = [
		'resourcesDir' => '%wwwDir%/mfu',
		'tempDir' => '%tempDir%/upload',
		'magic' => TRUE,
		'runtimes' => [
			'html5',
		],
		'maxFileSize' => '10mb',
		'maxChunkSize' => '1mb',
	];


	/***/
	public function loadConfiguration()
	{
		$config = $this->getConfig($this->defauls);
		$container = $this->getContainerBuilder();
		
		$container->addDefinition($this->prefix('uploader'))
			->setClass(DefaultUploader::class, [$config['tempDir']]);
		
		$container->addDefinition($this->prefix('settings'))
			->setClass(Settings::class)
			->addSetup('setRuntimes', [$config['runtimes']])
			->addSetup('setMaxFileSize', [$config['maxFileSize']])
			->addSetup('setMaxChunkSize', [$config['maxChunkSize']]);
		
		$container->addDefinition($this->prefix('plupload'))
			->setClass(Plupload::class);
		
		$container->addDefinition($this->prefix('magic'))
			->setClass(Magic::class, [$container->parameters['wwwDir'], $config['resourcesDir']]);
		
		if ($config['magic'] === TRUE) {
			$container->getDefinition($this->prefix('magic'))
				->addSetup('cast');
		}
	}
	
}
