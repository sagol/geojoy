<?php

namespace app\models\fields\edit;

/**
 * Базавое поле редактируемых полей
 */
class Field  extends \app\fields\Field {


	protected $_nameInForm;
	protected $_isFiltered = false;
	protected $_rules = array();


	/**
	 * Создание поля
	 * @param array $data
	 * @param \app\managers\Manager $manager 
	 */
	public function __construct($data, &$manager) {
		if($data !== null)
			if(!empty($data['parent'])) $this->_parent = $data['parent'];

		parent::__construct($data, $manager);
	}


	protected function init() {
		parent::init();

		// формируем имя поля для формы
		if($this->_nameInForm === null) 
			if(@$this->initOptions['shotNameInForm']) $this->_nameInForm = $this->_name;
			else $this->_nameInForm = 'app\models\fields[' . $this->_name . ']';

		// создаем правило для обязательных полей
		if($this->_required) {
			if($this->_multiLang) $this->_rules[] = array($this->_name, 'multiLangCheck');
			else $this->_rules[] = array($this->_name, 'required');
		}
	}


	public function sqlClear() {
		if(!$this->isSetFieldIndex()) $value = "''";
		else $value = '""';

		return array(
			array(
				'table' => $this->_table,
				'field' => $this->_field,
				'index' => $this->getFieldIndex(),
				'value' => $value,
			),
		);
	}


	public function sqlInsert() {
		return array(
			array(
				'table' => $this->_table,
				'field' => $this->_field,
				'index' => $this->getFieldIndex(),
				'value' => $this->packValue(),
			),
		);
	}


	public function sqlUpdate() {
		return array(
			array(
				'table' => $this->_table,
				'field' => $this->_field,
				'index' => $this->getFieldIndex(),
				'value' => $this->packValue(),
			),
		);
	}


	public function sqlDelete() {
		return array(
			array(
				'table' => $this->_table,
				'tableAlias' => $this->_tableAlias,
				'field' => $this->_field,
				'value' => $this->_value,
			),
		);
	}


	public function sqlFilter($values) {
		if(empty($values) && !$this->_isFiltered) return false;

		return array(
			$this->_table . ' ' . $this->_tableAlias => array(
				$this->_fieldFullName => $values,
			),
		);
	}


	public function getNameInForm() {
		return $this->_nameInForm;
	}


	public function getIsFiltered() {
		return $this->_isFiltered;
	}

	/**
	 * Упаковка значения поля
	 * @return string 
	 */
	public function packValue() {
		if(!$this->isSetFieldIndex()) $quotes = "'";
		else $quotes = '"';

		return $quotes . $this->_value . $quotes;
	}


	public function beforeFieldsClear() {
		return true;
	}


	public function afterFieldsClear() {
	}


	/**
	 * Событие выполняется до сохранения объявления (insert)
	 * @return boolean 
	 */
	public function beforeInsert() {
		return true;
	}


	/**
	 * Событие выполняется после сохранения объявления (insert)
	 * @return boolean 
	 */
	public function afterInsert() {
	}


	/**
	 * Событие выполняется до сохранения объявления (update)
	 * @return boolean 
	 */
	public function beforeUpdate() {
		return true;
	}


	/**
	 * Событие выполняется после сохранения объявления (update)
	 * @return boolean 
	 */
	public function afterUpdate() {
	}


	/**
	 * Событие выполняется до удаления объявления
	 * @return boolean 
	 */
	public function beforeDelete() {
		return true;
	}


	/**
	 * Событие выполняется после удаления объявления (update)
	 * @return boolean 
	 */
	public function afterDelete() {
		return true;
	}


	public function rules() {
		return $this->_rules;
	}


	public function isFiltered() {
		return $this->_isFiltered;
	}


	public function attributeNames() {
	}


	// временно
	/*public function attributeName() {
	}*/


	public function eventProcessing($controller, $action, $extData) {
		return;
	}


	public function setAttribute($name, $values) {
		if(isset($values[$name])) $this->setValue($values[$name]);
	}


	public function createParams($data) {
		return 'index.' . $this->_table . '.' . $this->_field . '=' . $data['index'];
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

		switch($sign) {
			case '!><':
				$param['condition'] = 'NOT ' . $name . ' IS NULL';
				break;
			case '><':
				$param['condition'] = $name . ' IS NULL';
				break;
			case '!()':
				if(isset($param['value'])) {
					$i = 0;
					foreach($param['value'] as $value) {
						$condition[] = $setParam . '_' . $i;
						$values[$setParam . '_' . $i] = $value;
						$i++;
					}
					$param['condition'] = $name . ' NOT IN (' . implode(', ', $condition) . ')';
					unset($condition, $i);
				}
				break;
			case '()':
				if(isset($param['value'])) {
					$i = 0;
					foreach($param['value'] as $value) {
						$values[$setParam . '_' . $i] = $value;
						$i++;
					}
					$param['condition'] = $name . ' IN (' . implode(', ', (array)$param['value']) . ')';
				}
				break;
			case '|~':
				if($this->getValue() !== false) {
					$param['condition'] = $name . ' LIKE ' . $setParam;
					$values[$setParam] = $this->getValue() . '%';
				}
				break;
			case '~|':
				if($this->getValue() !== false) {
					$param['condition'] = $name . ' LIKE ' . $setParam;
					$values[$setParam] = '%' . $this->getValue();
				}
				break;
			case '~':
				if($this->getValue() !== false) {
					$param['condition'] = $name . ' LIKE ' . $setParam;
					$values[$setParam] = '%' . $this->getValue() . '%';
				}
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


	// вызывается из CHtml::activeLabel
	public function getAttributeLabel($attribute) {
		if($attribute == $this->_name) return \Yii::t($this->_labelDictionary, $this->_title);

		return parent::getAttributeLabel($attribute);
	}


}