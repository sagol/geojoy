<?php

namespace app\models\fields\read;

/**
 * Поле ckeckbox
 */
class Check extends Field {


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::CHECK;

		parent::init();
	}


}