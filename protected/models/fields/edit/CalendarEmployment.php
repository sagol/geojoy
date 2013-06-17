<?php

namespace app\models\fields\edit;


/**
 * Поле календарь
 */
class CalendarEmployment extends Field {


	/**
	 * Данные поля
	 * @var array 
	 */
	protected $_data;


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::CALENDAR_EMPLOYMENT;

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
			if(isset($value[$this->_fieldIndex]))
				$value = $value[$this->_fieldIndex];
			else
				return false;
		}

		$data = explode(';', $value);

		foreach($data as $value) {
			if(!empty($value)) {
				$d = explode('-', $value);
				$this->_data[$d[0]][$d[1]] = 1;
			}
		}
		unset($data);


		return true;
	}


	/**
	 * Возвращает данные поля
	 * @var array 
	 */
	public function getData() {
		return $this->_data;
	}


}
