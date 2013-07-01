<?php

namespace app\models\fields\edit;

/**
 * Поле строка
 */
class String extends FieldLang {


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::STRING;
		$this->setPurifierOptions(__CLASS__);

		parent::init();
	}


}