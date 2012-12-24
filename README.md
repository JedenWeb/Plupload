Plupload component for Nette
============================

Implementation:
---------------

Don't forget to load jQuery and jQuery UI manually.

There is no need for including any extra JS or Css in head. Everything is done automatically. If you prefer doing it yourself then disable magic.


Usage
-----

	/**
	 * @param string $name
	 * @return \Echo511\Plupload\Widget\JQueryUIWidget
	 */
    public function createComponentPlupload($name)
    {
        $uploader = new Echo511\Plupload\Plupload();

        // $uploader->disableMagic();

        $uploader->setWwwDir(WWW_DIR) // Full path to your frontend directory
                 ->setBasePath($this->template->basePath) // BasePath provided by Nette
                 ->setResourcesDir(WWW_DIR . '/mfu'); // Full path to the resources location (js, css)

        $uploader->createSettings()
                 ->setRuntimes(array('html5')) // Available: gears, flash, silverlight, browserplus, html5
                 ->setMaxFileSize('1000mb')
                 ->setMaxChunkSize('1mb');

        $uploader->createUploader()
                 ->setTempDir(WWW_DIR . '/../temp') // Where should be placed temporary files
                 ->onSuccess[] = callback($this, 'handleUploadFile');

        return $uploader->getComponent();
    }


	public function handleUploadFile(\Nette\Http\FileUpload $file)
	{
		...
	}