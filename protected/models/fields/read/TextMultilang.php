<?php

namespace app\models\fields\read;

/**
 * Поле мультиязычный текст
 */
class TextMultilang extends FieldMultiLang {


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::TEXT_MULTILANG;

		parent::init();
	}


}