<?php

namespace app\components;

include_once(\Yii::getPathOfAlias('app.vendors.jQuery-File-Upload') . DS . 'upload.class.php');

class UploadHandlerFields extends \UploadHandler {


	const  PARAM_NAME = 'app\models\fields';


	function __construct($options=null) {
		$this->options = array(
			'script_url' => $this->getFullUrl() . '/',
			'upload_dir' => dirname($_SERVER['SCRIPT_FILENAME']) . '/files/',
			'upload_url' => $this->getFullUrl() . '/files/',
			'param_name' => 'files',
			// Set the following option to 'POST', if your server does not support
			// DELETE requests. This is a parameter sent to the client:
			'delete_type' => 'DELETE',
			// The php.ini settings upload_max_filesize and post_max_size
			// take precedence over the following max_file_size setting:
			'max_file_size' => null,
			'min_file_size' => 1,
			'accept_file_types' => '/.+$/i',
			'max_number_of_files' => null,
			// Set the following option to false to enable resumable uploads:
			'discard_aborted_uploads' => true,
			// Set to true to rotate images based on EXIF meta data, if available:
			'orient_image' => false,
			'image_versions' => array(
				// Uncomment the following version to restrict the size of
				// uploaded images. You can also add additional versions with
				// their own upload directories:
				/*
				'large' => array(
					'upload_dir' => dirname($_SERVER['SCRIPT_FILENAME']).'/files/',
					'upload_url' => $this->getFullUrl().'/files/',
					'max_width' => 1920,
					'max_height' => 1200,
					'jpeg_quality' => 95
				), */
				/* 'thumbnail' => array(
					'upload_dir' => dirname($_SERVER['SCRIPT_FILENAME']).'/thumbnails/',
					'upload_url' => $this->getFullUrl().'/thumbnails/',
					'max_width' => 80,
					'max_height' => 80
				), */
			)
		);

		if($options) $this->options = array_replace_recursive($this->options, $options);
	}


	public function post() {
		if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {
			return $this->delete();
		}

		/*$upload = isset($_FILES[$this->options['param_name']]) ?
			$_FILES[$this->options['param_name']] : null;*/
		$upload = isset($_FILES[self::PARAM_NAME]) ?
			$_FILES[self::PARAM_NAME] : null;


		$info = array();
		if ($upload && is_array($upload['tmp_name'])) {
			// param_name is an array identifier like "files[]",
			// $_FILES is a multi-dimensional array:
			foreach ($upload['tmp_name'] as $index => $value) {
				$info[] = $this->handle_file_upload(
				$upload['tmp_name'][$index],
				isset($_SERVER['HTTP_X_FILE_NAME']) ?
					$_SERVER['HTTP_X_FILE_NAME'] : $upload['name'][$index],
				isset($_SERVER['HTTP_X_FILE_SIZE']) ?
					$_SERVER['HTTP_X_FILE_SIZE'] : $upload['size'][$index],
				isset($_SERVER['HTTP_X_FILE_TYPE']) ?
					$_SERVER['HTTP_X_FILE_TYPE'] : $upload['type'][$index],
				$upload['error'][$index]
				);
			}
		} elseif ($upload || isset($_SERVER['HTTP_X_FILE_NAME'])) {
			// param_name is a single object identifier like "file",
			// $_FILES is a one-dimensional array:
			$info[] = $this->handle_file_upload(
				isset($upload['tmp_name']) ? $upload['tmp_name'] : null,
				isset($_SERVER['HTTP_X_FILE_NAME']) ?
				$_SERVER['HTTP_X_FILE_NAME'] : (isset($upload['name']) ?
					$upload['name'] : null),
				isset($_SERVER['HTTP_X_FILE_SIZE']) ?
				$_SERVER['HTTP_X_FILE_SIZE'] : (isset($upload['size']) ?
					$upload['size'] : null),
				isset($_SERVER['HTTP_X_FILE_TYPE']) ?
				$_SERVER['HTTP_X_FILE_TYPE'] : (isset($upload['type']) ?
					$upload['type'] : null),
				isset($upload['error']) ? $upload['error'] : null
			);
			}
		header('Vary: Accept');
		$json = json_encode($info);
		$redirect = isset($_REQUEST['redirect']) ?
		stripslashes($_REQUEST['redirect']) : null;
		if ($redirect) {
			header('Location: '.sprintf($redirect, rawurlencode($json)));
			return;
		}
		if (isset($_SERVER['HTTP_ACCEPT']) &&
			(strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
				header('Content-type: application/json');
		} else {
			header('Content-type: text/plain');
		}
		echo $json;
	}


	protected function handle_file_upload($uploaded_file, $name, $size, $type, $error) {
		if($error == UPLOAD_ERR_INI_SIZE) $error = 'uploadErrIniSize';
		elseif($error == UPLOAD_ERR_FORM_SIZE) $error = 'uploadErrFormSize';
		elseif($error == UPLOAD_ERR_FORM_SIZE) $error = 'uploadErrPartial';
		elseif($error == UPLOAD_ERR_NO_FILE) $error = 'uploadErrNoFile';

		return parent::handle_file_upload($uploaded_file, $name, $size, $type, $error);
	}


}