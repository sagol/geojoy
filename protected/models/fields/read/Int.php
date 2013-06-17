<?php

namespace app\models\fields\read;

/**
 * Поле числовое
 */
class Int  extends Field {


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::INT;

		parent::init();
	}



}