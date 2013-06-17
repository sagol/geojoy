<?php

namespace app\models\fields\read;

/**
 * Поле аватар
 */
class Avatar extends Field {

	/**
	 * Тип аватара
	 * upload = 1
	 * webcam = 2
	 * gravatar = 3
	 */
	protected $_typeImg = 1;
	protected $_uploadImg;
	protected $_gravatarEmail;
	protected $_webcamImg;


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::AVATAR;

		parent::init();
	}


	/**
	 * Получение свойств
	 * @param string $name
	 * @return mix 
	 */
	public function __get($name) {
		if($name == $this->_name . 'Set') return $this->_typeImg;
		elseif($name == $this->_name . 'Upload') return empty($this->_uploadImg) ? \Yii::app()->request->getBaseUrl() . \Yii::app()->params['avatarDefault'] : $this->_uploadImg;
		elseif($name == $this->_name . 'Webcam') return $this->_webcamImg;
		elseif($name == $this->_name . 'Gravatar') return $this->_gravatarEmail;

		return parent::__get($name);
	}

	/**
	 * Установка свойств
	 * @param string $name
	 * @param mix $value
	 * @return mix 
	 */
	public function __set($name, $value) {
		if($name == $this->_name . 'Gravatar') {
			$this->_gravatarEmail = $value;
			return true;
		}

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
	 *function get_gravatar( $email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {
	 *	$url = 'http://www.gravatar.com/avatar/';
	 *	$url .= md5( strtolower( trim( $email ) ) );
	 *	$url .= "?s=$s&d=$d&r=$r";
	 *	if ( $img ) {
	 *		$url = '<img src="' . $url . '"';
	 *		foreach ( $atts as $key => $val )
	 *			$url .= ' ' . $key . '="' . $val . '"';
	 *		$url .= ' />';
	 *	}
	 *	return $url;
	 *}
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
			if(isset($value[$this->_fieldIndex])) $value = $value[$this->_fieldIndex];
			else return false;
		}
		if(empty($value)) return false;

		// $value = typeImg;uploadedImg;webcamImg;gravatarEmail
		$value = explode(';', $value);
		if(isset($value[0])) $this->_typeImg = $value[0];
		else $this->_typeImg = 1;
		if(isset($value[1])) $this->_uploadImg = $value[1];
		if(isset($value[2])) $this->_webcamImg = $value[2];
		if(isset($value[3])) $this->_gravatarEmail = $value[3];


		return true;
	}


	/**
	 * Получения значения
	 * @return mix 
	 */
	public function getValue() {
		if($this->_typeImg == 1) return $this->getUpload();
		elseif($this->_typeImg == 2) return $this->getWebcam();
		elseif($this->_typeImg == 3) return $this->getGravatar();


		return \Yii::app()->request->getBaseUrl() . \Yii::app()->params['avatarDefault'];
	}


	public function getDefault($type) {
		$set = array(
			'upload' => 1,
			'webcam' => 2,
			'gravatar' => 3,
		);
		if($set[$type] == $this->_typeImg) return ' set';


		return '';
	}


	public function getUpload() {
		return empty($this->_uploadImg) ? \Yii::app()->request->getBaseUrl() . \Yii::app()->params['avatarDefault'] : $this->_uploadImg;
	}


	public function getWebcam() {
		return $this->_webcamImg;
	}


	public function getGravatar() {
		$size = \Yii::app()->params['avatarSize'];
		$type = \Yii::app()->params['gavatarType'];
		if($type == 'default') $type = urlencode(\Yii::app()->getRequest()->getBaseUrl(true) . \Yii::app()->params['avatarDefault']);

		return 'http://www.gravatar.com/avatar/' . md5(strtolower(trim($this->_gravatarEmail))) . '?s=' . $size . '&d=' . $type . '&r=g';
	}


}