<?php

namespace app\models\fields\edit;

/**
 * Поле мультиязычный текст
 */
class TextMultilang extends FieldMultiLang {


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::TEXT_MULTILANG;
		$this->setPurifierOptions(__CLASS__);

		parent::init();
	}


}