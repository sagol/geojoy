<?php

namespace app\models\fields\edit;


/**
 * Поле ckeckbox
 */
class Check extends Field {


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::CHECK;
		$this->_isFiltered = true;

		parent::init();
	}


}
