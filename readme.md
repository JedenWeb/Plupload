# Deprecated

Use [original repo](http://github.com/echo511/Plupload) instead.

# Plupload

Simple file uploader for [Nette Framework](http://nette.org/)

Originaly from Nikolas Tsiongas, new BSD License.

## Instalation

Plupload requires **jQuery** and **jQueryUI**.

The best way to install jedenweb/images is using  [Composer](http://getcomposer.org/):


```json
{
	"require" {
		"jedenweb/plupload": "dev-master"
	}
}
```

After that you have to register extension in config.neon.

```neon
extensions:
	plupload: JedenWeb\Plupload\DI\PluploadExtension
```


## Usage

### Creating component

In presenter

```php
	/**
	 * @inject
	 * @var \JedenWeb\Plupload\Plupload
	 */
	public $plupload;


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
```

### Dummy way to show uploaded files

In presenter

```php
	public function actionDefault()
	{
		$this->template->images = \Nette\Utils\Finder::find('*')->from(WWW_DIR . '/media/upload');
	}
```

In template

```latte
	{control plupload}

	{snippet images}
		{foreach $images as $image}
			&lt;img src="{$basePath}/media/upload/{$image->getFilename()}" /&gt;
		{/foreach}
	{/snippet}
```
