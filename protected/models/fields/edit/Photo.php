<?php

namespace app\models\fields\edit;

/**
 * Поле загружающие одно фото
 */
class Photo extends FieldPhoto {


	protected $_noImg = false;


	/**
	 * Создание поля
	 * @param array $data
	 * @param \app\managers\Manager $manager 
	 */
	public function __construct($data, &$manager) {
		if(!empty($data['noImg'])) $this->_noImg = $data['noImg'];

		parent::__construct($data, $manager);
	}


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::PHOTO;
		$this->_isFiltered = true;
		$this->_maxUploadFiles = 1;

		parent::init();
	}


	public function setMaxUploadFiles($value) {
		return false;
	}


	public function getNoImg() {
		return $this->_noImg;
	}


}