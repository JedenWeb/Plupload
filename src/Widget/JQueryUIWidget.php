<?php

namespace PavelJurasek\Plupload\Widget;

use Nette;

/**
 * This file is a part of Plupload component for Nette Framework.
 *
 * @author Nikolas Tsiongas
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 * @package Plupload component
 * @license New BSD License
 */
class JQueryUIWidget extends \Nette\Application\UI\Control
{

    /**
	 * @var \PavelJurasek\Plupload\Plupload
	 */
    private $plupload;



	/**
	 * @param \PavelJurasek\Plupload\Plupload $plupload
	 */
    public function __construct(\PavelJurasek\Plupload\Plupload $plupload)
    {
        $this->plupload = $plupload;
    }



    public function handleUpload()
    {
        $this->plupload->upload();
    }



	/**
	 * @param string $token
	 */
    public function render($token = '1')
    {
		$this->template->resourcesDir = $this->plupload->resourcesDir;
        $this->template->pluploadSettings = $this->plupload->pluploadSettings;
        $this->template->isMagical = $this->plupload->isMagical();
		$this->template->token = $token;


		if ($this->plupload->isMagical()) {
			$magic = new \PavelJurasek\Plupload\Magic;
            $magic->setResourcesDir($this->plupload->resourcesDir);
			$this->template->magic = $magic;
		}

		$this->template->setFile(__DIR__ . '/templates/default.latte');
        $this->template->render();
    }

}
