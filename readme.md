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

Register extension in config.neon

	/** 
	 * @inject
	 * @var \JedenWeb\Plupload\Plupload
	 */
	public $plupload;


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
		$this->plupload->getUploader()
				 ->onSuccess[] = callback($this, 'handleUploadFile');

		return $this->plupload;
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
