<?php

namespace app\models\fields\edit;

/**
 * Поле заглушка
 */
class None  extends Field{


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::NONE;
		$this->_isFiltered = false;
		$this->_disabled = true;

		parent::init();
	}


	/**
	 * Получения значения
	 * @return mix 
	 */
	public function getValue() {
		return false;
	}


	/**
	 * Установка значения
	 * @param mix $value 
	 */
	public function setValue($value) {
		return false;
	}
}