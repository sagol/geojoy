<?php

namespace app\models\fields\edit;

/**
 * Поле скрытое
 */
class Hidden extends Field {


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::HIDDEN;

		parent::init();
	}


}