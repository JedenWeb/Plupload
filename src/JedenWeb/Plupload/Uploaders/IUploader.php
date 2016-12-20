<?php

namespace JedenWeb\Plupload\Uploaders;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
interface IUploader
{

	/**
	 * Handle file upload
	 */
	public function upload();

}
