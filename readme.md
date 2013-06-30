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

	public function actionDefault()
	{
		$this->template->images = \Nette\Utils\Finder::find('*')->from(WWW_DIR . '/media/upload');
	}


	/**
	 * @param string $name
	 * @return \JedenWeb\Plupload\Widget\JQueryUIWidget
	 */
	public function createComponentPlupload($name)
	{
		$uploader = new \JedenWeb\Plupload\Plupload;

		// $uploader->disableMagic();

		$uploader->setWwwDir(WWW_DIR) // Full path to your frontend directory
				 ->setBasePath($this->template->basePath) // BasePath provided by Nette
				 ->setResourcesDir(WWW_DIR . '/mfu'); // Full path to the resources location (js, css)

		$uploader->getSettings()
				 ->setRuntimes(array('html5')) // Available: gears, flash, silverlight, browserplus, html5
				 ->setMaxFileSize('1000mb')
				 ->setMaxChunkSize('1mb');

		$uploader->getUploader()
				 ->setTempDir(WWW_DIR . '/../temp/upload') // Where should be placed temporary files
				 ->onSuccess[] = callback($this, 'handleUploadFile');

		return $uploader->getComponent();
	}
	
	
	/**
	 * @param \Nette\Http\FileUpload $file
	 */
	public function handleUploadFile(\Nette\Http\FileUpload $file)
	{
		$file->move(WWW_DIR . '/media/upload/'. $file->getSanitizedName());

		$this->invalidateControl('images');
	}


In template:
	
	{control plupload}

	{snippet images}
		{foreach $images as $image}
			<img src="{$basePath}/media/upload/{$image->getFilename()}" />
		{/foreach}
	{/snippet}
