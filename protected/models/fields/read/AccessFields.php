<?php

namespace app\models\fields\read;

/**
 * Поле права доступа 
 */
class AccessFields extends Field {


	protected $_field = 'access_fields';


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::ACCESS_FIELDS;

		parent::init();
	}


	/**
	 * Распаковка значения поля
	 * @param type string
	 * @return boolean 
	 */
	public function unPackValue($value) {
		$sql = $this->sqlSelect();
		$value = $value[$sql[0]['field']];
		if($this->isSetFieldIndex()) {
			if(isset($value[$this->_fieldIndex])) $value = $value[$this->_fieldIndex];
			else return false;
		}

		$values = $this->_manager->toArray($value, false);

		foreach($values as $value) {
			$data = explode('=', $value);
			$this->_value[trim($data[0])] = trim($data[1]);
		}


		return true;
	}


}