<?php

namespace app\models\fields\edit;

/**
 * Поле мультиязычная строка
 */
class StringMultilang extends FieldMultiLang {


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::STRING_MULTILANG;
		$this->setPurifierOptions(__CLASS__);

		parent::init();
	}


}