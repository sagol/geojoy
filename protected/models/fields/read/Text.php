<?php

namespace app\models\fields\read;

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