<?php

namespace app\modules\photosField\models;

class UploadPhoto extends \app\components\UploadHandlerFields {


	const FILE_EXIST = 1;
	const FILE_SET_PARAM = 2;
	const FILE_UPLOADED = 3;
	const FILE_DELETE = 4;

	public $id;
	public $table;
	public $name;
	public $files = array();


	protected function get_file_objects() {
		if($this->files) {
			return array_values(array_filter(array_map(
				array($this, 'get_file_object'),
				$this->files
			)));
		}
	}


	protected function get_file_object($fileInfo) {
		if($fileInfo['status'] == self::FILE_DELETE) return null;

		$file_name = basename($fileInfo['file']);
		$file_path = $fileInfo['file'];

		if (is_file($file_path) && $file_name[0] !== '.') {
			$file = new \stdClass();
			$file->name = $file_name;
			$file->size = filesize($file_path);
			$file->url = $this->options['upload_url'].rawurlencode($file->name);
			$file->main = isset($fileInfo['main']) ? $fileInfo['main'] : 0;
			foreach($this->options['image_versions'] as $version => $options) {
				if (is_file($options['upload_dir'].$file_name)) {
					$file->{$version.'_url'} = $options['upload_url']
						.rawurlencode($file->name);
				}
			}
			$this->set_file_delete_url($file);
			return $file;
		}
		return null;
	}


	public function get() {
		parent::get();

		/*if(!empty($this->files)) {
			$session = \Yii::app()->session;
			$uploadFiles = $session->get('uploadFiles' . $this->table);
			foreach($this->files as $file => $data) {
				if(!empty($data['status'])) continue;
				$data['status'] = self::FILE_UPLOADED;
				$data['main'] = 0;
				$uploadFiles[$this->id][$this->name][$file] = $data;
			}
			$session->add('uploadFiles' . $this->table, $uploadFiles);
		}*/
	}


	public function post() {
		parent::post();

		if(!empty($this->files)) {
			$session = \Yii::app()->session;
			$uploadFiles = $session->get('uploadFiles' . $this->table);
			foreach($this->files as $file => $data) {
				if(!empty($data['status'])) continue;
				$data['status'] = self::FILE_UPLOADED;
				$data['main'] = 0;
				$uploadFiles[$this->id][$this->name][$file] = $data;
			}
			$session->add('uploadFiles' . $this->table, $uploadFiles);
		}
	}


	public function delete() {
		if(!isset($_REQUEST['file'])) return false;

		$file = basename(stripslashes($_REQUEST['file']));

		$session = \Yii::app()->session;
		$uploadFiles = $session->get('uploadFiles' . $this->table);
		if(empty($uploadFiles[$this->id][$this->name][$this->options['upload_dir'] . $file])) return false;
		else $uploadFiles[$this->id][$this->name][$this->options['upload_dir'] . $file]['status'] = self::FILE_DELETE;

		$session->add('uploadFiles'  . $this->table, $uploadFiles);
	}


	protected function handle_file_upload($uploaded_file, $name, $size, $type, $error) {
		$file = new \stdClass();
		$file->name = $this->trim_file_name($name, $type);
		$file->size = intval($size);
		$file->type = $type;
		$file->main = 0;
		$error = $this->has_error($uploaded_file, $file, $error);
		if (!$error && $file->name) {
			$file_path = $this->options['upload_dir'].$file->name;
			$append_file = !$this->options['discard_aborted_uploads'] &&
				is_file($file_path) && $file->size > filesize($file_path);
			clearstatcache();
			if ($uploaded_file && is_uploaded_file($uploaded_file)) {
				// multipart/formdata uploads (POST method uploads)
				if ($append_file) {
					file_put_contents(
						$file_path,
						fopen($uploaded_file, 'r'),
						FILE_APPEND
					);
				} else {
					if(move_uploaded_file($uploaded_file, $file_path)) {
						$this->files[$file_path]['file'] = $file_path;
						$this->files[$file_path]['url'] = $this->options['upload_url'] . rawurlencode($file->name);
					}
				}
			} else {
				// Non-multipart uploads (PUT method support)
				file_put_contents(
				$file_path,
				fopen('php://input', 'r'),
				$append_file ? FILE_APPEND : 0
				);
			}
			$file_size = filesize($file_path);
			if ($file_size === $file->size) {
				if ($this->options['orient_image']) {
					$this->orient_image($file_path);
				}
				$file->url = $this->options['upload_url'].rawurlencode($file->name);
				foreach($this->options['image_versions'] as $version => $options) {
					if ($this->create_scaled_image($file->name, $options)) {
						if ($this->options['upload_dir'] !== $options['upload_dir']) {
							$file->{$version.'_url'} = $options['upload_url']
								.rawurlencode($file->name);
						} else {
							clearstatcache();
							$file_size = filesize($file_path);
						}
					}
				}
			} else if ($this->options['discard_aborted_uploads']) {
				unlink($file_path);
				$file->error = 'abort';
			}
			$file->size = $file_size;
			$this->set_file_delete_url($file);
		} else {
			$file->error = $error;
		}
		return $file;
	}


	public function setMain() {
		$session = \Yii::app()->session;
		$uploadFiles = $session->get('uploadFiles' . $this->table);

		$foto = $_GET['foto'];
		$baseUrl = \Yii::app()->request->getBaseUrl();
		if(strpos($foto, $baseUrl) === 0) $foto = substr($foto, strlen($baseUrl));
		if(!empty($uploadFiles[$this->id][$this->name][SITE_PATH . $foto])) {
			$main = &$uploadFiles[$this->id][$this->name][SITE_PATH . $foto];
			foreach($uploadFiles[$this->id][$this->name] as &$file)
				$file['main'] = 0;

			$main['main'] = 1;
			if($file['status']  == self::FILE_EXIST) $main['status'] = self::FILE_SET_PARAM;
			$this->_main = $main;
			$session->add('uploadFiles' . $this->table, $uploadFiles);
			$result['status'] = 'ok';
		}
		else $result['status'] = 'error';


		// header('Content-type: application/json');
		echo json_encode($result);
	}


}