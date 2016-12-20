<?php

namespace JedenWeb\Plupload;

use Nette\Application\UI\Control;
use Nette\Utils\Random;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class Plupload extends Control
{

	/** @var Magic */
	private $magic;

	/** @var Uploaders\IUploader */
	private $uploader;

	/** @var Settings */
	private $settings;

    /** @var string */
    private $templateFile;


	/**
	 * @param Magic $magic
	 * @param Uploaders\IUploader $uploader
	 * @param Settings $settings
	 */
	public function __construct(
		Magic $magic,
		Uploaders\IUploader $uploader,
		Settings $settings
	) {
		parent::__construct();

		$this->magic = $magic;
		$this->uploader = $uploader;
		$this->settings = $settings;
        $this->templateFile = __DIR__ . '/template.latte';
	}
	

	/**
	 * Trigger file uploading.
	 */
	public function handleUpload()
	{
		$this->uploader->upload();
	}


    /**
     * @param string
     *
     * @return $this
     */
    public function setTemplateFile($templateFile)
    {
        $this->templateFile = $templateFile;
        return $this;
    }


	/**
	 * @param string $token
	 */
	public function render($token = NULL)
	{
		if ($token === NULL) {
			$token = Random::generate();
		}
		
		$this->template->settings = $this->settings;
		$this->template->magic = $this->magic;
		$this->template->token = $token;

		$this->template->setFile($this->templateFile);
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
