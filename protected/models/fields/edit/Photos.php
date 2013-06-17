<?php

namespace app\models\fields\edit;

/**
 * Поле загружающие фото
 */
class Photos extends FieldPhoto {


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::PHOTOS;
		$this->_isFiltered = true;
		$this->_maxUploadFiles = 10;

		parent::init();
	}


}