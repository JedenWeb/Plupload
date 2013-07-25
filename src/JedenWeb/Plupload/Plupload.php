<?php

namespace JedenWeb\Plupload;

use Nette;

/**
 * This file is a part of Plupload component for Nette Framework.
 *
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 * @author Nikolas Tsiongas
 */
class Plupload extends Nette\Application\UI\Control
{

	/** @var Magic */
	private $magic;

	/** @var Uploaders\IUploader */
	private $uploader;

	/** @var Settings */
	private $settings;



	/**
	 * @param \JedenWeb\Plupload\Magic $magic
	 * @param \JedenWeb\Plupload\Uploaders\IUploader $uploader
	 * @param \JedenWeb\Plupload\Settings $settings
	 */
	public function __construct(
		Magic $magic,
		Uploaders\IUploader $uploader,
		Settings $settings
	) {
		$this->magic = $magic;
		$this->uploader = $uploader;
		$this->settings = $settings;
	}

	

	/**
	 * Trigger file uploading.
	 */
	public function handleUpload()
	{
		$this->uploader->upload();
	}



	/**
	 * @param string $token
	 */
	public function render($token = NULL)
	{
		if ($token === NULL) {
			$token = \Nette\Utils\Strings::random();
		}
		
		$this->template->settings = $this->settings;
		$this->template->magic = $this->magic;
		$this->template->token = $token;

		$this->template->setFile(__DIR__ . '/template.latte');
		$this->template->render();
	}
	
	
	
	/**
	 * @return Uploaders\IUploader
	 */
	public function getUploader()
	{
		return $this->uploader;
	}	

}
