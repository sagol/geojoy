<?php

namespace app\models\fields\read;

/**
 * Поле строка
 */
class String extends FieldLang {


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::STRING;

		parent::init();
	}


}