<?php

namespace app\models\fields\edit;

/**
 * Поле текст
 */
class Text extends Field {


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::TEXT;

		parent::init();
	}


}