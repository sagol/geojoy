<?php

namespace app\models\fields\edit;


/**
 * Поле select
 */
class Droplist extends FieldList {


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::DROPLIST;
		$this->_isFiltered = true;

		parent::init();
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
			case '|~':
			case '~|':
			case '~':
				$param['condition'] = false;
				break;
			case '!()':
			case '()':
				$param['condition'] = false;
				break;
			case '!=':
				if($this->getValue() !== null) {
					$param['condition'] = 'NOT cast(nullif(' . $name . ', \'\') as int4) = ' . $setParam;
					$values[$setParam] = $this->getValue();
				}
				break;
			default:
				if($this->getValue() !== null) {
					if($this->isSetFieldIndex())
						$param['condition'] = 'cast(nullif(' . $name . ', \'\') as int4) ' . $sign . ' ' . $setParam;
					else
						$param['condition'] = $name . ' ' . $sign . ' ' . $setParam;
					$values[$setParam] = $this->getValue();
				}
				else
					$param['condition'] = false;
		}
	}


}
