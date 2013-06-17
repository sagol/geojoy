<?php

namespace app\models\fields\edit;

/**
 * Базавое поле полей загружающих фото
 */
class FieldPhoto extends Field {


	const FILE_EXIST = 1;
	const FILE_SET_PARAM = 2;
	const FILE_UPLOADED = 3;
	const FILE_DELETE = 4;

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


	/**
	 * Инициализация поля 
	 */
	public function init() {
		// формируем имя поля для формы
		if($this->_nameInForm === null) $this->_nameInForm = 'app\models\fields[' . $this->_name . ']';

		parent::init();
	}


	public function getMaxUploadFiles() {
		return $this->_maxUploadFiles;
	}


	public function setMaxUploadFiles($value) {
		$this->_maxUploadFiles = $value;
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
			$session = \Yii::app()->getComponent('session');
			if($session) {
				$uploadFiles = $session->get('uploadFiles' . $this->_table);
				$id = 'A' . $this->_manager->getId();
				if(empty($uploadFiles[$id][$this->_name])) {
					$uploadFiles[$id][$this->_name] = $this->_files;
					$session->add('uploadFiles' . $this->_table, $uploadFiles);
				}
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
	 * Упаковка значения поля
	 * @return string 
	 */
	public function packValue() {
		if(!$this->isSetFieldIndex()) $quotes = "'";
		else $quotes = '"';

		if(empty($this->_files)) return $quotes . $quotes;

		$baseDir = \Yii::getPathOfAlias(\Yii::app()->params['uploadDir']);
		if(!empty($this->_main['file'])) {
			$file = $this->_main['file'];
			$f = strpos($file, $baseDir);
			$files[] = 'main:' . ($f === 0 ? substr($file, strlen($baseDir)) : $file);
		}

		$baseUrl = \Yii::app()->request->getBaseUrl() . \Yii::app()->params['uploadUrl'];
		foreach($this->_files as $id => $file) {
			$f = strpos($file['file'], $baseDir);
			$files[] = $f === 0 ? substr($file['file'], strlen($baseDir)) : $file['file'];
			$f = strpos($file['url'], $baseUrl);
			$files[] =  $f === 0 ? substr($file['url'], strlen($baseUrl)) : $file['url'];
		}


		return $quotes . implode(';', $files) . $quotes;
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


	public function setAttribute($name, $values) {
		return true;
	}


	/**
	 * Применение изменений с файлами
	 * @return boolean 
	 */
	protected function _setPhotos() {
		$adv = $this->adv;
		$session = \Yii::app()->getComponent('session');
		if($session) $uploadFiles = $session->get('uploadFiles' . $this->_table);
		if(!empty($uploadFiles[$adv][$this->_name]))
			$files = $uploadFiles[$adv][$this->_name];
		else return true;

		foreach($files as $id => &$file) {
			if($file['status'] == self::FILE_SET_PARAM) {
				if($file['main']) {
					unset($file['main']);
					$this->_main = $file;
				}
			}
			elseif($file['status'] == self::FILE_UPLOADED) {
				$idUser = \Yii::app()->user->id;
				$strrpos = strrpos($file['file'], DS);
				$fileName = substr($file['file'], $strrpos+1);
				$oldPath = substr($file['file'], 0, $strrpos+1);
				$newPath = str_replace('/tmp/', "/$idUser/", $oldPath);
				$file['url'] = str_replace('/tmp/', "/$idUser/", $file['url']);

				if(!file_exists($newPath))
					mkdir($newPath, 0777, true);
				if($file['file'] != $newPath . $fileName) {
					rename($file['file'], $newPath . $fileName);
					$file['file'] = $newPath . $fileName;
				}
				if(!file_exists($newPath . 'thumbnails' . DS))
					mkdir($newPath . 'thumbnails' . DS, 0777, true);
				if($oldPath . 'thumbnails' . DS . $fileName != $newPath . 'thumbnails' . DS . $fileName)
					rename($oldPath . 'thumbnails' . DS . $fileName, $newPath . 'thumbnails' . DS . $fileName);

				$this->_files[$file['file']] = $file;

				if($file['main']) $this->_main = $file;
			}
			elseif($file['status'] == self::FILE_DELETE) {
				$strrpos = strrpos($file['file'], DS);
				$fileName = $strrpos === false ? $file['file'] : substr($file['file'], $strrpos+1);
				@unlink($file['file']);
				unset($this->_files[$file['file']], $files[$id]);
				@unlink(substr($file['file'], 0, $strrpos) . DS . 'thumbnails' . DS . $fileName);
			}
		}

		if(count($files) == 1 && empty($this->_main)) $this->_main = current($files);
		$this->_files = $files;
		unset($uploadFiles[$adv][$this->_name]);
		$session->add('uploadFiles' . $this->_table, $uploadFiles);

		return true;
	}


	/**
	 * Событие выполняется до сохранения объявления (insert)
	 * @return boolean 
	 */
	public function beforeInsert() {
		$this->adv = 'A' . $_REQUEST['adv'];
		return $this->_setPhotos();
	}


	/**
	 * Событие выполняется до сохранения объявления (update)
	 * @return boolean 
	 */
	public function beforeUpdate() {
		$this->adv = 'A' . $this->_manager->getId();
		return $this->_setPhotos();
	}


	/**
	 * Событие выполняется после удаления объявления
	 * @return boolean 
	 */
	public function afterDelete() {
		if(empty($this->_files)) return true;

		$adv = $this->adv = 'A' . $this->_manager->getId();
		foreach($this->_files as &$file)
			$file['status'] = self::FILE_DELETE;

		$uploadFiles[$adv][$this->_name] = $this->_files;
		$session = \Yii::app()->getComponent('session');
		if($session) $session->add('uploadFiles' . $this->_table, $uploadFiles);
		$this->_setPhotos();


		return true;
	}


}