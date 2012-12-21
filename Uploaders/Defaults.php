<?php

namespace Echo511\Plupload\Uploaders;

use Nette;
use Nette\Utils\Strings;

/**
 * This file is a part of Plupload component for Nette Framework.
 *
 * @author     Nikolas Tsiongas
 * @package    Plupload component
 * @license    New BSD License
 */
class Defaults extends \Nette\Object implements IUploader
{

    /**
	 * Callback called when file is uploaded
	 *
	 * @var array
	 */
    public $onSuccess = array();

    /**
	 * Directory for temp files
	 *
	 * @var string
	 */
    public $tempUploadsDir;

    /**
	 * Useful when uploading files with the same names from two components at the same time
	 *
	 * @var string
	 */
    public $token = 'eufwd';


    /*********** Is ready? ***********/
    public function isReady()
    {
        if(empty($this->onSuccess) || $this->tempUploadsDir === null) {
            throw new \Nette\InvalidStateException("Uploader is not setted up correctly.");
        }

        return true;
    }


    /*********** Setters ***********/
    public function setTempUploadsDir($dir)
    {
        $this->tempUploadsDir = $this->returnDir($dir);
        return $this;
    }

    public function setToken($token)
    {
        $this->token = md5($token);
        return $this;
    }



    /*********** Helpers ***********/
    private function returnDir($dir)
    {
        if(!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

		return $dir;
    }


    /*********** Upload handler ***********/
    public function upload()
    {
        $this->isReady();

        if(!is_dir($this->tempUploadsDir)) {
            throw new \Nette\InvalidArgumentException('Missing temp directory.');
        }

        // HTTP headers for no cache etc
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        // Settings
        $tempDir = realpath($this->tempUploadsDir);

        //$cleanupTargetDir = false; // Remove old files
        //$maxFileAge = 60 * 60; // Temp file age in seconds

        // 5 minutes execution time
        @set_time_limit(5 * 60);

        // Uncomment this one to fake upload time
        // usleep(5000);

        // Get parameters
        $chunk = isset($_REQUEST["chunk"]) ? (int) $_REQUEST["chunk"] : 0;
        $chunks = isset($_REQUEST["chunks"]) ? (int) $_REQUEST["chunks"] : 0;
        $fileNameOriginal = isset($_REQUEST["name"]) ? $_REQUEST["name"] : Strings::random(8);
        $fileName = sha1($this->token.$chunks.$fileNameOriginal);
        $filePath = "$tempDir/$fileName";

		# sha1 generates ^[a-f0-9]{40}$
        // Clean the fileName for security reasons
//        $fileName = preg_replace('/[^\w\._]+/', '', $fileName); sha1 generates only


        // Make sure the fileName is unique but only if chunking is disabled
        if ($chunks < 2 && file_exists($tempDir . DIRECTORY_SEPARATOR . $fileName)) {
			$ext = strrpos($fileName, '.');
			$fileName_a = substr($fileName, 0, $ext);
			$fileName_b = substr($fileName, $ext);

			$count = 1;
			while (file_exists($tempDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b)) {
				$count++;
			}

			$fileName = $fileName_a . '_' . $count . $fileName_b;
        }

        // Look for the content type header
        if (isset($_SERVER["HTTP_CONTENT_TYPE"])) {
			$contentType = $_SERVER["HTTP_CONTENT_TYPE"];
		}

        if (isset($_SERVER["CONTENT_TYPE"])) {
			$contentType = $_SERVER["CONTENT_TYPE"];
		}

        // Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
        if (strpos($contentType, "multipart") !== false) {
			if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
				// Open temp file
				if ($out = fopen($filePath, $chunk === 0 ? "wb" : "ab")) {
						// Read binary input stream and append it to temp file
						if ($in = fopen($_FILES['file']['tmp_name'], "rb")) {
							while ($buff = fread($in, 4096)) {
								fwrite($out, $buff);
							}
						} else {
							die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
						}

						fclose($in);
						fclose($out);

						@unlink($_FILES['file']['tmp_name']);
						unset($_FILES['file']['tmp_name']);
				} else {
					die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
				}
			} else {
				die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
			}
        } else {
			// Open temp file
			if ($out = fopen($filePath, $chunk === 0 ? "wb" : "ab")) {
					// Read binary input stream and append it to temp file
					if ($in = fopen("php://input", "rb")) {
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
        $lastChunk = ($chunk+1) === $chunks;
        if($lastChunk || $nonChunkedTransfer) {
			$upload = new \Nette\Http\FileUpload(array(
				'name' => $fileNameOriginal,
				'type' => "",
				'size' => filesize($filePath),
				'tmp_name' => $filePath,
				'error' => UPLOAD_ERR_OK
			));

//			$this->onSuccess->invokeArgs(array($upload));
			$this->onSuccess($this, $upload);

			// Remove from temp after callback
			@unlink($filePath);
        }
    }

}
