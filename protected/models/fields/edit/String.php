<?php

namespace app\models\fields\edit;

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