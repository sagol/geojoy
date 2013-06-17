<?php

namespace app\models\fields\edit;


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
		if($name == $this->_name)
			return $this->_value;

		return parent::__get($name);
	}


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::CHECKLIST;
		$this->_isFiltered = true;

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

		if(!empty($value)) {
			$value = explode(';', $value);
			foreach($value as $val)
				$this->_value[$val] = $val;
		}
		else
			$this->_value = array();


		return true;
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

		return $quotes . @implode(';', $this->_value) . $quotes;
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


	/**
	 * Формирует sql для выбоке по полю
	 * @staticvar array $signs
	 * @param array $param
	 * @param array $names
	 * @param array $values 
	 */
	public function createCondition(&$param, $names, &$values) {
		static $signs =
		array(
			'!()' => ':in__', // NOT IN ()
			'()' => ':not_in__', // IN ()

			'!><' => '', // NOT IS NULL
			'><' => '', // IS NULL

			'>=' => ':more_equal__',
			'<=' => ':less_equal__',

			'!=' => ':not_equally__',
			'<>' => ':not_equally__',

			'|~' => ':match_first__', // LIKE text%
			'~|' => ':match_last__', // LIKE %text

			'>' => ':more__',
			'<' => ':less__',

			'=' => ':equally__',
			'~' => ':match_overlap__', // LIKE %text%
		);

		$name = $param['name'];
		$sign = $param['sign'];

		$setParam = $signs[$sign] . $name . '_' . (empty($names[$name]) ? 0 : count($names[$name]));
		$name = $this->_fieldFullName;

		switch($sign) {
			case '!><':
				$param['condition'] = 'NOT ' . $name . ' IS NULL';
				break;
			case '><':
				$param['condition'] = $name . ' IS NULL';
				break;
			case '=':
				if($this->getValue() !== null) {
					$i = 0;
					foreach($this->getValue() as $value) {
						$values[$setParam . '_' . $i] = $value;
						$params[] = $setParam . '_' . $i;
						$i++;
					}
					unset($condition, $i);

					// преобразование в целое число, можно попробовать для увеличения/уменьшения ))) производительности
					// if($this->isSetFieldIndex()) $param['condition'] = 'cast(string_to_array(' . $name . ', \';\') as int4[]) @> ARRAY[' . implode(', ', $params) . ']::int4[]';
					// else $param['condition'] = $name . ' @> ARRAY[' . implode(', ', $params) . ']::int4[]';
					if($this->isSetFieldIndex())
						$param['condition'] = 'string_to_array(' . $name . ', \';\') @> ARRAY[' . implode(', ', $params) . ']::text[]';
					else
						$param['condition'] = $name . ' @> ARRAY[' . implode(', ', $params) . ']::text[]';
				}
				else
					$param['condition'] = false;
				break;
			default:
				$param['condition'] = false;
		}
	}


}
