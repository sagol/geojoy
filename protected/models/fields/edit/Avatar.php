<?php

namespace app\models\fields\edit;


/**
 * Поле аватар
 */
class Avatar extends Field {


	/**
	 * Тип аватара
	 * @var string 
	 * upload = 1
	 * webcam = 2
	 * gravatar = 3
	 */
	protected $_typeImg = 1;

	/**
	 * Загруженный аватар
	 * @var string 
	 */
	protected $_uploadImg;

	/**
	 * Email граватара
	 * @var string 
	 */
	protected $_gravatarEmail;

	/**
	 * Аватар снятый на веб камеру
	 * @var string 
	 */
	protected $_webcamImg;


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::AVATAR;

		parent::init();

		// проверка на соответствие типу
		$this->_rules[] = array($this->_name . 'Gravatar', 'email');
		// проверка на длину, пределах от 6 до 50 символов
		$this->_rules[] = array($this->_name . 'Gravatar', 'length', 'min' => 6, 'max' => 50);
		// проверка на нижний регистр
		$this->_rules[] = array($this->_name . 'Gravatar', 'filter', 'filter' => 'mb_strtolower');
	}


	/**
	 * Получение свойств
	 * @param string $name
	 * @return mix 
	 */
	public function __get($name) {
		if($name == $this->_name . 'Set')
			return $this->_typeImg;
		elseif($name == $this->_name . 'Upload')
			return empty($this->_uploadImg) ? \Yii::app()->request->getBaseUrl() . \Yii::app()->params['avatarDefault'] : $this->_uploadImg;
		elseif($name == $this->_name . 'Webcam')
			return $this->_webcamImg;
		elseif($name == $this->_name . 'Gravatar')
			return $this->_gravatarEmail;

		return parent::__get($name);
	}


	/**
	 * Установка свойств
	 * @param string $name
	 * @param mix $value
	 * @return mix 
	 */
	public function __set($name, $value) {
		if($name == $this->_name . 'Upload') {
			$this->_uploadImg = $value;
			echo $value;
			die;
		}
		elseif($name == $this->_name . 'Webcam')
			$this->_webcamImg = $value;
		elseif($name == $this->_name . 'Gravatar')
			$this->_gravatarEmail = $value;

		else
			return parent::__set($name, $value);
	}


	/**
	 * Get either a Gravatar URL or complete image tag for a specified email address.
	 *
	 * @param string $email The email address
	 * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
	 * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
	 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
	 * @param boole $img True to return a complete IMG tag False for just the URL
	 * @param array $atts Optional, additional key/value attributes to include in the IMG tag
	 * @return String containing either just a URL or a complete image tag
	 * @source http://gravatar.com/site/implement/images/php/
	 *
	 * function get_gravatar( $email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {
	 * 	$url = 'http://www.gravatar.com/avatar/';
	 * 	$url .= md5( strtolower( trim( $email ) ) );
	 * 	$url .= "?s=$s&d=$d&r=$r";
	 * 	if ( $img ) {
	 * 		$url = '<img src="' . $url . '"';
	 * 		foreach ( $atts as $key => $val )
	 * 			$url .= ' ' . $key . '="' . $val . '"';
	 * 		$url .= ' />';
	 * 	}
	 * 	return $url;
	 * }
	 */


	/**
	 * Распаковка значения поля
	 * @param type string
	 * @return boolean 
	 */
	public function unPackValue($value) {
		$sql = $this->sqlSelect();
		$value = $value[$sql[0]['field']];
		if($this->isSetFieldIndex()) {
			if(isset($value[$this->_fieldIndex]))
				$value = $value[$this->_fieldIndex];
			else
				return false;
		}
		if(empty($value))
			return false;

		// $value = typeImg;uploadedImg;webcamImg;gravatarEmail
		$value = explode(';', $value);
		if(isset($value[0]))
			$this->_typeImg = $value[0];
		else
			$this->_typeImg = 1;
		if(isset($value[1]))
			$this->_uploadImg = $value[1];
		if(isset($value[2]))
			$this->_webcamImg = $value[2];
		if(isset($value[3]))
			$this->_gravatarEmail = $value[3];


		return true;
	}


	/**
	 * Упаковка значения поля
	 * @return string 
	 */
	public function packValue() {
		if(!$this->isSetFieldIndex())
			$quotes = "'";
		else
			$quotes = '"';

		$value = "$this->_typeImg;$this->_uploadImg;$this->_webcamImg;$this->_gravatarEmail";


		return $quotes . $value . $quotes;
	}


	/**
	 * Установка значения
	 * @param mix $value 
	 */
	public function setValue($value) {
		if(!empty($value['Set']))
			$this->_typeImg = $value['Set'];
		if(!empty($value['Upload']))
			$this->_uploadImg = $value['Upload'];
		if(!empty($value['Webcam']))
			$this->_webcamImg = $value['Webcam'];
		if(!empty($value['Gravatar']))
			$this->_gravatarEmail = $value['Gravatar'];
	}


	/**
	 * Получения значения
	 * @return mix 
	 */
	public function getValue() {
		if($this->_typeImg == 1)
			return $this->getUpload();
		elseif($this->_typeImg == 2)
			return $this->getWebcam();
		elseif($this->_typeImg == 3)
			return $this->getGravatar();


		return \Yii::app()->request->getBaseUrl() . \Yii::app()->params['avatarDefault'];
	}


	/**
	 * Проверка установленного типа аватара
	 * @param string $type
	 * @return string 
	 */
	public function getDefault($type) {
		$set = array(
			'upload' => 1,
			'webcam' => 2,
			'gravatar' => 3,
		);
		if($set[$type] == $this->_typeImg)
			return ' set';


		return '';
	}


	/**
	 * Возвращает загруженный аватар
	 * @return string 
	 */
	public function getUpload() {
		return empty($this->_uploadImg) ? \Yii::app()->request->getBaseUrl() . \Yii::app()->params['avatarDefault'] : $this->_uploadImg;
	}


	/**
	 * Возвращает аватар снятый на веб камеру
	 * @return string 
	 */
	public function getWebcam() {
		return $this->_webcamImg;
	}


	/**
	 * Возвращает граватар
	 * @return string 
	 */
	public function getGravatar() {
		$size = \Yii::app()->params['avatarSize'];
		$type = \Yii::app()->params['gavatarType'];
		if($type == 'default')
			$type = urlencode(\Yii::app()->getRequest()->getBaseUrl(true) . \Yii::app()->params['avatarDefault']);

		return 'http://www.gravatar.com/avatar/' . md5(strtolower(trim($this->_gravatarEmail))) . '?s=' . $size . '&d=' . $type . '&r=g';
	}


	/**
	 * Событие выполняется до сохранения объявления (insert)
	 * @return boolean 
	 */
	public function beforeInsert() {
		if(strpos($this->_webcamImg, 'webcam_tmp_') !== false) {
			$new = str_replace('webcam_tmp_', 'webcam_', $this->_webcamImg);
			if(rename(SITE_PATH . $this->_webcamImg, SITE_PATH . DS . $new))
				$this->_webcamImg = $new;
		}


		return true;
	}


	/**
	 * Событие выполняется до сохранения объявления (update)
	 * @return boolean 
	 */
	public function beforeUpdate() {
		if(strpos($this->_webcamImg, 'webcam_tmp_') !== false) {
			$new = str_replace('webcam_tmp_', 'webcam_', $this->_webcamImg);
			if(rename(SITE_PATH . $this->_webcamImg, SITE_PATH . DS . $new))
				$this->_webcamImg = $new;
		}


		return true;
	}


	/**
	 * Событие выполняется после сохранения объявления (insert)
	 * @return boolean 
	 */
	public function afterInsert() {
		$userId = \Yii::app()->user->id;
		$uploadDir = \Yii::getPathOfAlias(\Yii::app()->params['uploadDir']) . DS . 'fields';
		foreach(glob($uploadDir . DS . $userId . DS . $this->_name . DS . 'webcam_*') as $name) {
			if(SITE_PATH . $this->_webcamImg != $name)
				unlink($name);
		}
	}


	/**
	 * Событие выполняется после сохранения объявления (update)
	 * @return boolean 
	 */
	public function afterUpdate() {
		$userId = \Yii::app()->user->id;
		$uploadDir = \Yii::getPathOfAlias(\Yii::app()->params['uploadDir']) . DS . 'fields';
		foreach(glob($uploadDir . DS . $userId . DS . $this->_name . DS . 'webcam_*') as $name) {
			if(SITE_PATH . $this->_webcamImg != $name)
				unlink($name);
		}
	}


	/**
	 * Событие выполняется после удаления объявления
	 * @return boolean 
	 */
	public function afterDelete() {
		$userId = \Yii::app()->user->id;
		$uploadDir = \Yii::getPathOfAlias(\Yii::app()->params['uploadDir']) . DS . 'fields';
		foreach(glob($uploadDir . DS . $userId . DS . $this->_name . DS . '*') as $name)
			unlink($name);
	}


}
