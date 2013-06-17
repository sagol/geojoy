<?php

namespace app\models\fields\edit;

/**
 * Поле срок жизни
 */
class Lifetime extends Field {


	protected $_fieldDate = 'lifetime_date';
	protected $_valueDate;


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::LIFETIME;

		parent::init();
	}


	public function data() {
		return $list = array(
			1 => \Yii::t('fields', 'LIFETIME_WEEK'),
			2 => \Yii::t('fields', 'LIFETIME_TWO_WEEK'),
			3 => \Yii::t('fields', 'LIFETIME_MONTH'),
			4 => \Yii::t('fields', 'LIFETIME_THREE_MONTH'),
			5 => \Yii::t('fields', 'LIFETIME_SIX_MONTH'),
		);
	}


	public function getValueDate() {
		return $this->_valueDate;
	}


	public function fieldTwoValue() {
		switch($this->_value) {
			case 1:
				$interval = " + '7 day'::interval";
				break;
			case 2:
				$interval = " + '14 day'::interval";
				break;
			case 3:
				$interval = " + '1 month'::interval";
				break;
			case 4:
				$interval = " + '3 month'::interval";
				break;
			case 5:
				$interval = " + '6 month'::interval";
				break;
			default:
				$interval = '';
		}
		$value = 'NOW()' . $interval;



		return $value;
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
			array(
				'table' => $this->_table,
				'field' => $this->_fieldDate,
				'index' => Field::FIELD_INDEX_DISABLED,
				'value' => 'NULL',
			),
		);
	}


	public function sqlSelect() {
		return array(
			array(
				'table' => $this->_table,
				'tableAlias' => $this->_tableAlias,
				'field' => $this->_field,
				'index' => $this->getFieldIndex(),
			),
			array(
				'table' => $this->_table,
				'tableAlias' => $this->_tableAlias,
				'field' => $this->_fieldDate,
				'index' => Field::FIELD_INDEX_DISABLED,
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
			array(
				'table' => $this->_table,
				'field' => $this->_fieldDate,
				'index' => false,
				'value' => $this->fieldTwoValue(),
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
			array(
				'table' => $this->_table,
				'field' => $this->_fieldDate,
				'index' => false,
				'value' => $this->fieldTwoValue(),
			),
		);
	}

	/**
	 * Упаковка значения поля
	 * @return string 
	 */
	public function packValue() {
		if(!$this->isSetFieldIndex()) $quotes = "'";
		else $quotes = '"';

		if(empty($this->_value)) $this->_value = \Yii::app()->params['lifetime'];


		return $quotes . $this->_value . $quotes;
	}

	/**
	 * Распаковка значения поля
	 * @param type string
	 * @return boolean 
	 */
	public function unPackValue($value) {
		$this->_valueDate = $value[$this->_fieldDate];

		$sql = $this->sqlSelect();
		$value = $value[$sql[0]['field']];
		if($this->isSetFieldIndex()) {
			if(isset($value[$this->_fieldIndex])) $value = $value[$this->_fieldIndex];
			else return false;
		}

		$this->_value = $value;


		return true;
	}


	/**
	 * Возвращает текстовое значение
	 * игнорируем html вывод
	 * @return boolean 
	 */
	public function getValueText() {
		return false;
	}


}