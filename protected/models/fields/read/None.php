<?php

namespace app\models\fields\read;

/**
 * Поле заглушка
 */
class None  extends Field{


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::NONE;
		$this->_disabled = true;

		parent::init();
	}


	/**
	 * Получения значения
	 * @return boolean 
	 */
	public function getValue() {
		return false;
	}


}