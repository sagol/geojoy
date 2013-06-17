<?php

namespace app\models\fields\read;

/**
 * Поле строка
 */
class String extends Field {


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::STRING;

		parent::init();
	}


}