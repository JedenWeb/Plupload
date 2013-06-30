Plupload component for Nette
============================

Implementation:
---------------

	{
		"require" {
			"paveljurasek/plupload": "dev-master"
		}
	}

Requires jQuery and jQueryUI.

There is no need for including any extra JS or Css in head. Everything is done automatically. If you prefer doing it yourself then disable magic.


Usage
-----

	/**
	 * @param string $name
	 * @return \Echo511\Plupload\Widget\JQueryUIWidget
	 */
    public function createComponentPlupload($name)
    {
        $uploader = new \JedenWeb\Plupload\Plupload();

        // $uploader->disableMagic();

        $uploader->setWwwDir(WWW_DIR) // Full path to your frontend directory
                 ->setBasePath($this->template->basePath) // BasePath provided by Nette
                 ->setResourcesDir(WWW_DIR . '/mfu'); // Full path to the resources location (js, css)

        $uploader->getSettings()
                 ->setRuntimes(array('html5')) // Available: gears, flash, silverlight, browserplus, html5
                 ->setMaxFileSize('1000mb')
                 ->setMaxChunkSize('1mb');

        $uploader->getUploader()
                 ->setTempDir(WWW_DIR . '/../temp') // Where should be placed temporary files
                 ->onSuccess[] = callback($this, 'handleUploadFile');

        return $uploader->getComponent();
    }



	/**
	 * @param \Nette\Http\FileUpload $file
	 */
	public function handleUploadFile(\Nette\Http\FileUpload $file)
	{
		...
	}
