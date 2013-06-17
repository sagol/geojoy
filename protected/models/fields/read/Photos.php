<?php

namespace app\models\fields\read;

/**
 * Поле загружающие фото
 */
class Photos extends FieldPhoto {


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::PHOTOS;
		$this->_maxUploadFiles = 10;

		parent::init();
	}


}