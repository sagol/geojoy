<?php

namespace app\models\fields\edit;


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
	 * Упаковка значения поля
	 * @return string 
	 */
	public function packValue() {
		if(!$this->isSetFieldIndex())
			$quotes = "'";
		else
			$quotes = '"';

		foreach($this->_value as $name => $value)
			$values[] = "$name=$value";

		return $quotes . implode(',', $values) . $quotes;
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
			if(isset($value[$this->_fieldIndex]))
				$value = $value[$this->_fieldIndex];
			else
				return false;
		}

		$values = $this->_manager->toArray($value, false);

		foreach($values as $value) {
			$data = explode('=', $value);
			$this->_value[trim($data[0])] = trim($data[1]);
		}


		return true;
	}


}
