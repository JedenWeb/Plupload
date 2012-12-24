Plupload component for Nette
============================

Implementation:
---------------

Don't forget to load jQuery and jQuery UI manually.

There is no need for including any extra JS or Css in head. Everything is done automatically. If you prefer to do it by yourself then disable magic.


Usage
-----

	/**
	 * @param string $name
	 * @return \Echo511\Plupload\Widget\JQueryUIWidget
	 */
    public function createComponentPlupload($name)
    {
        $uploader = new Echo511\Plupload\Plupload();

        // Use magic for loading Js and Css?
        // $uploader->disableMagic();

        // Configuring paths
        $uploader->setWwwDir(WWW_DIR) // Full path to your frontend directory
                 ->setBasePath($this->template->basePath) // BasePath provided by Nette
                 ->setResourcesDir(WWW_DIR . '/mfu'); // Full path to the location of plupload libs (js, css)

        // Configuring plupload
        $uploader->createSettings()
                 ->setRuntimes(array('html5')) // Available: gears, flash, silverlight, browserplus, html5
                 ->setMaxFileSize('1000mb')
                 ->setMaxChunkSize('1mb'); // What is chunk you can find here: http://www.plupload.com/documentation.php

        // Configuring uploader
        $uploader->createUploader()
                 ->setTempDir(WWW_DIR . '/../temp') // Where should be placed temporaly files
                 ->setOnSuccess(array($this, 'tests')); // Callback when upload is successful: returns Nette\Http\FileUpload

        return $uploader->getComponent();
    }