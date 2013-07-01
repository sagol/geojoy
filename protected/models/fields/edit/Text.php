<?php

namespace app\models\fields\edit;

/**
 * Поле текст
 */
class Text extends FieldLang {


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::TEXT;
		$this->setPurifierOptions(__CLASS__);

		parent::init();
	}


}