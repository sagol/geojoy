<?php

namespace app\models\fields\read;

/**
 * Базавое поле полей загружающих фото
 */
class FieldPhoto extends Field {


	const FILE_EXIST = 1;

	protected $adv;

	protected $_lists;
// 	array(
// 		'file' =>
// 		'url' =>
// 		'status' =>
// 		'main' =>
// 	)
	protected $_files;
	protected $_conv;
	// при загрузке файлов используется только для записи
	protected $_main;
	protected $_maxUploadFiles;


	public function getMaxUploadFiles() {
		return $this->_maxUploadFiles;
	}

	/**
	 * Распаковка значения поля
	 * @param type string
	 * @return boolean 
	 */
	public function unPackValue($value) {
		$sql = $this->sqlSelect();
		$value = $value[$sql[0]['field']];
		if($this->isSetFieldIndex()) {
			if(isset($value[$this->_fieldIndex])) $value = $value[$this->_fieldIndex];
			else return false;
		}

		if(empty($value)) return false;

		$files = explode(';', $value);
		if($this->_maxUploadFiles > 1 && substr($files[0], 0, 5) == 'main:') {
			$main = substr($files[0], 5);
			unset($files[0]);
		}
		else $main = '';

		$baseUrl = \Yii::app()->request->getBaseUrl() . \Yii::app()->params['uploadUrl'];
		$baseDir = \Yii::getPathOfAlias(\Yii::app()->params['uploadDir']);
		$file = reset($files);
		do {
			$id = $baseDir . $file;
			$this->_files[$id]['file'] = $id;
			$url = next($files);
			$this->_files[$id]['url'] = $baseUrl . $url;
			$this->_files[$id]['status'] = self::FILE_EXIST;
			$this->_files[$id]['main'] = (int)($file == $main);
			if($file == $main) $this->_main = $this->_files[$id];
		} while($file = next($files));

		$this->_value = @$files[0]; // TODO: проверить правильность присвоения
		if(empty($this->_main) && !empty($this->_files))
			$this->_main = reset($this->_files);

		if(!empty($this->_files)) {
			$session = \Yii::app()->session;
			$uploadFiles = $session->get('uploadFiles' . $this->_table);
			$id = 'A' . $this->_manager->getId();
			if(empty($uploadFiles[$id][$this->_name])) {
				$uploadFiles[$id][$this->_name] = $this->_files;
				$session->add('uploadFiles' . $this->_table, $uploadFiles);
			}
		}

		if(!empty($this->_main)) {
			$url = $this->_main['url'];
			$f = strrpos($url, DS);
			$src = substr($url, 0, $f+1) . 'thumbnails' . substr($url, $f);
			$this->_main['thumbnails'] = $src;
		}


		return true;
	}


	/**
	 * Получения значения
	 * @return mix 
	 */
	public function getValue() {
		if(empty($this->_main)) return;


		return $this->_main['url'];
	}


	public function data($type = null) {
		if($type == 'files' || $type == null) return $this->_files;

		if($type == 'url') {
			if(empty($this->_conv['url']) && !empty($this->_files)) {
				foreach($this->_files as $file)
					$this->_conv['url'][] = $file['url'];
			}
			return $this->_conv['url'];
		}

		if($type == 'file') {
			if(empty($this->_conv['file']) && !empty($this->_files)) {
				foreach($this->_files as $file)
					$this->_conv['file'][] = $file['file'];
			}
			return $this->_conv['file'];
		}

		if($type == 'main') {
			if(empty($this->_conv['main']) && !empty($this->_files)) {
				foreach($this->_files as $file)
					$this->_conv['main'][] = $file['main'];
			}
			return $this->_conv['main'];
		}

		foreach($imgs as $url) {
			$f = strrpos($url, DS);
			$src = substr($url, 0, $f+1) . 'thumbnails' . substr($url, $f);
			echo '<a href="' . $url . '" title="Foto ' . $i . '" rel="gallery"><img src="' . $src . '" /></a>';
			$i++;
		}

		if($type == 'thumbnails') {
			if(empty($this->_conv['thumbnails'])) {
				foreach($this->_files as $file) {
					$url = $file['url'];
					$f = strrpos($url, DS);
					$src = substr($url, 0, $f+1) . 'thumbnails' . substr($url, $f);
					$this->_conv['thumbnails'][] = $src;
				}
			}
			return $this->_conv['thumbnails'];
		}
	}


	public function mainPhoto($thumb = true) {
		if($thumb) return $this->_main['thumbnails'];

		return $this->_main['url'];
	}


}