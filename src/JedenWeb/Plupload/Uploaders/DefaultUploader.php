<?php

namespace JedenWeb\Plupload\Uploaders;

use Nette;

/**
 * This file is a part of Plupload component for Nette Framework.
 *
 * @author Nikolas Tsiongas
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 * @package Plupload component
 * @license New BSD License
 */
class DefaultUploader extends Nette\Object implements IUploader
{

	/**
	 * @var array
	 */
	public $onSuccess = array();

	/**
	 * @var string
	 */
	private $tempDir;

	/**
	 * @var string
	 */
	private $token;
	
	/**
	 * @var \Symfony\Component\Filesystem\Filesystem
	 */
	private $io;

	
	
	/**
	 * @param string $tempDir
	 * @param \Symfony\Component\Filesystem\Filesystem $io
	 */
	public function __construct($tempDir, \Symfony\Component\Filesystem\Filesystem $io = NULL)
	{
		if ($io === NULL) {
			$io = new \Symfony\Component\Filesystem\Filesystem;
		}
		$this->io = $io;
		
		if (!$io->exists($tempDir)) {
			$io->mkdir($tempDir);
		}
		
		$this->tempDir = $tempDir;
	}

	

	/**
	 * @return bool
	 */
	public function isReady()
	{
		if (!$this->token) {
			$this->setToken();
		}

		return !(!$this->onSuccess || !$this->tempDir);
	}



	/*********************** setters ***********************/


	/**
	 * @param string $token
	 * @return DefaultUploader  provides fluent interface
	 */
	public function setToken($token = null)
	{
		if (!$token) {
			$token = \Nette\Utils\Strings::random();
		}

		$this->token = md5($token);
		return $this;
	}


	/*********************** IUploader ***********************/



	public function upload()
	{
		if (!$this->isReady()) {
			throw new Nette\InvalidStateException("Uploader is not set up correctly.");
		}

		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

		$targetDir = realpath($this->tempDir);

		# 5 minutes execution time
		@set_time_limit(5 * 60);


		$chunk = isset($_REQUEST["chunk"]) ? $_REQUEST["chunk"] : 0;
		$chunks = isset($_REQUEST["chunks"]) ? $_REQUEST["chunks"] : 0;
		$fileName = isset($_REQUEST["name"])
			? \Nette\Utils\Strings::webalize($_REQUEST["name"], '.')
			: \Nette\Utils\Strings::random();
		$fileNameOriginal = $fileName;
		$fileName = sha1($this->token.$chunks.$fileNameOriginal);
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
			$out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
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
