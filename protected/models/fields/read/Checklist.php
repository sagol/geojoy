<?php

namespace app\models\fields\read;

/**
 * Поле группа ckeckbox элементов
 */
class Checklist extends FieldList {


	/**
	 * Получение свойств
	 * @param string $name
	 * @return mix 
	 */
	public function __get($name) {
		if($name == $this->_name) return $this->_value;

		return parent::__get($name);
	}


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::CHECKLIST;

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

		if(!empty($value)) {
			$value = explode(';', $value);
			foreach($value as $val)
				$this->_value[$val] = $val;
		}
		else $this->_value = array();


		return true;
	}


	/**
	 * Возвращает массив текстовых значений
	 * @return array 
	 */
	public function getValueText() {
		$values = array();
		if(!empty($this->_lists) && !empty($this->_value))
			foreach($this->_value as $id => $value) {
				if(array_key_exists($value, $this->_lists) && $this->_lists[$value]['translate'])
					$values[$id] = \Yii::t('lists', $this->_lists[$value]['value']);
			}


		return $values;
	}


}