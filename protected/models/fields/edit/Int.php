<?php

namespace app\models\fields\edit;

/**
 * Поле числовое
 */
class Int  extends Field {


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::INT;
		$this->_isFiltered = true;
		$this->_rules[] = array($this->_name, 'numerical', 'integerOnly' => true);

		parent::init();
	}


	/**
	 * Установка значения
	 * @param mix $value 
	 */
	public function setValue($value) {
		if(is_array($value)) {
			foreach($value as &$val)
				settype($val, 'int');

			$this->_value = $value;
		}
		else $this->_value = (int)$value;
	}


	/**
	 * Формирует sql для выбоке по полю
	 * @staticvar array $signs
	 * @param array $param
	 * @param array $names
	 * @param array $values 
	 */
	public function createCondition(&$param, $names, &$values) {
		static $signs = array(
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

		if(is_array($this->getValue()))
			switch($sign) {
				case '!><':
					$param['condition'] = 'NOT ' . $name . ' IS NULL';
					break;
				case '><':
					$param['condition'] = $name . ' IS NULL';
					break;
				case '=':
					$value = $this->getValue();
					if(isset($value['from'])) {
						$condition[] = $name . '::int4 >= ' . $setParam . '_from';
						$values[$setParam . '_from'] = $value['from'];
					}
					if(isset($value['to'])) {
						$condition[] = $name . '::int4 <= ' . $setParam . '_to';
						$values[$setParam . '_to'] = $value['to'];
					}
					if(empty($condition)) $param['condition'] = false;
					else $param['condition'] = '(' . implode(' AND ', $condition) . ')';
					break;
				default:
					$param['condition'] = false;
			}
		else
			switch($sign) {
				case '!><':
					$param['condition'] = 'NOT ' . $name . ' IS NULL';
					break;
				case '><':
					$param['condition'] = $name . ' IS NULL';
					break;
				case '|~':
				case '~|':
				case '~':
				case '!()':
				case '()':
					$param['condition'] = false;
					break;
				case '!=':
					if($this->getValue() !== false) {
						$param['condition'] = 'NOT ' . $name . ' = ' . $setParam;
						$values[$setParam] = $this->getValue();
					}
					break;
				default:
					if($this->getValue() !== false) {
						$param['condition'] = $name . ' ' . $sign . ' ' . $setParam;
						$values[$setParam] = $this->getValue();
					}
					else $param['condition'] = false;
			}
	}


}