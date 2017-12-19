<?php

namespace JedenWeb\Plupload\Uploaders;

use Nette\Http\FileUpload;
use Nette\SmartObject;
use Nette\Utils\FileSystem;

/**
 * @method void onSuccess(FileUpload $file)
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class DefaultUploader implements IUploader
{

	use SmartObject;

	/**
	 * @var callable[]
	 */
	public $onSuccess = [];

	/**
	 * @var string
	 */
	private $tempDir;


	/**
	 * @param string $tempDir
	 */
	public function __construct($tempDir)
	{
		FileSystem::createDir($tempDir);

		$this->tempDir = $tempDir;
	}


	/**
	 * @return bool
	 */
	public function isReady()
	{
		return $this->onSuccess && $this->tempDir;
	}


	/*********************** IUploader ***********************/


	/**
	 * Handle file upload
	 *
	 * @throws UploaderNotReadyException
	 */
	public function upload()
	{
		if (!$this->isReady()) {
			throw new UploaderNotReadyException('Uploader is not set up correctly. There is no onSuccess hander or temp directory is not set.');
		}

		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", FALSE);
		header("Pragma: no-cache");

		$targetDir = realpath($this->tempDir);

		# 5 minutes execution time
		@set_time_limit(5 * 60);


		$chunk = isset($_REQUEST["chunk"]) ? (int) $_REQUEST["chunk"] : 0;
		$chunks = isset($_REQUEST["chunks"]) ? (int) $_REQUEST["chunks"] : 0;
		$fileName = isset($_REQUEST["name"])
			? \Nette\Utils\Strings::webalize($_REQUEST["name"], '.')
			: \Nette\Utils\Random::generate();
		$fileNameOriginal = $fileName;
		$fileName = sha1($chunks.$fileNameOriginal);
		$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;


		# Make sure the fileName is unique but only if chunking is disabled
		if ($chunks < 2 && file_exists($filePath)) {
			$ext = strrpos($fileNameOriginal, '.');
			$fileName_a = substr($fileNameOriginal, 0, $ext);
			$fileName_b = substr($fileNameOriginal, $ext);

			$count = 1;
			while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b)) {
				$count++;
			}

			$fileNameOriginal = $fileName_a . '_' . $count . $fileName_b;
		}

		$contentType = '';
		if (isset($_SERVER["HTTP_CONTENT_TYPE"])) {
			$contentType = $_SERVER["HTTP_CONTENT_TYPE"];
		}

		if (isset($_SERVER["CONTENT_TYPE"])) {
			$contentType = $_SERVER["CONTENT_TYPE"];
		}

		# Handle non multipart uploads, older WebKit versions didn't support multipart in HTML5
		if (strpos($contentType, "multipart") !== false) {
			if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
				$out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk === 0 ? "wb" : "ab");
				if ($out) {
					$in = fopen($_FILES['file']['tmp_name'], "rb");

					if ($in) {
						while ($buff = fread($in, 4096)) {
							fwrite($out, $buff);
						}
					} else {
						die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
					}

					fclose($in);
					fclose($out);
					@unlink($_FILES['file']['tmp_name']);
				} else {
					die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
				}
			} else {
				die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
			}
		} else {
			$out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk === 0 ? "wb" : "ab");
			if ($out) {
				$in = fopen("php://input", "rb");

				if ($in) {
					while ($buff = fread($in, 4096)) {
						fwrite($out, $buff);
					}
				} else {
					die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
				}

				fclose($in);
				fclose($out);
			} else {
				die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
			}
		}


		$nonChunkedTransfer = ($chunk === 0 && $chunks === 0);
		$lastChunk = $chunk + 1 === $chunks;
		if($lastChunk || $nonChunkedTransfer) {
			$upload = new \Nette\Http\FileUpload(array(
				'name' => $fileNameOriginal,
				'type' => $contentType,
				'size' => filesize($filePath),
				'tmp_name' => $filePath,
				'error' => UPLOAD_ERR_OK
			));

			$this->onSuccess($upload);

			@unlink($filePath);
		}
	}

}
