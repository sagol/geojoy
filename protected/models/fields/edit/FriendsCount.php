<?php

namespace app\models\fields\edit;

/**
 * Поле количество друзей
 */
class FriendsCount extends Field {


	protected $_servise;
	// структура в базе
	protected $_table = 'services';
	protected $_tableAlias = 's';
	protected $_field = 'friends_count';
	protected $_fieldIndex = Field::FIELD_INDEX_DISABLED;

	protected $_fieldUrl = 'url_social';
	protected $_valueUrl;


	public function sqlClear() {
		return array(
			array(
				'table' => $this->_table,
				'field' => $this->_field,
				'index' => Field::FIELD_INDEX_DISABLED,
				'value' => "''",
			),
			array(
				'table' => $this->_table,
				'field' => $this->_fieldUrl,
				'index' => Field::FIELD_INDEX_DISABLED,
				'value' => "''",
			),
		);
	}


	public function sqlSelect() {
		return array(
			array(
				'table' => $this->_table,
				'tableAlias' => $this->_tableAlias,
				'field' => $this->_field,
				'index' => Field::FIELD_INDEX_DISABLED,
			),
			array(
				'table' => $this->_table,
				'tableAlias' => $this->_tableAlias,
				'field' => $this->_fieldUrl,
				'index' => Field::FIELD_INDEX_DISABLED,
			),
		);
	}


	public function sqlInsert() {
		return array(
			array(
				'table' => $this->_table,
				'field' => $this->_field,
				'index' => Field::FIELD_INDEX_DISABLED,
				'value' => $this->packValue(),
			),
			array(
				'table' => $this->_table,
				'field' => $this->_fieldUrl,
				'index' => Field::FIELD_INDEX_DISABLED,
				'value' => "'$this->_valueUrl'",
			),
		);
	}


	public function sqlUpdate() {
		return array(
			array(
				'table' => $this->_table,
				'field' => $this->_field,
				'index' => Field::FIELD_INDEX_DISABLED,
				'value' => $this->packValue(),
			),
			array(
				'table' => $this->_table,
				'field' => $this->_fieldUrl,
				'index' => Field::FIELD_INDEX_DISABLED,
				'value' => "'$this->_valueUrl'",
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
			array(
				'table' => $this->_table,
				'tableAlias' => $this->_tableAlias,
				'field' => $this->_fieldUrl,
				'index' => Field::FIELD_INDEX_DISABLED,
				'value' => $this->_valueUrl,
			),
		);
	}


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::FRIENDS_COUNT;
		$this->_isFiltered = false;

		parent::init();
	}


	/**
	 * Распаковка значения поля
	 * @param type string
	 * @return boolean 
	 */
	public function unPackValue($value) {
		$this->_valueUrl = $value[$this->_fieldUrl];

		$sql = $this->sqlSelect();
		$value = $value[$sql[0]['field']];
		if($this->isSetFieldIndex()) {
			if(isset($value[$this->_fieldIndex])) $value = $value[$this->_fieldIndex];
			else return false;
		}

		if($value === null) $this->_disabled = true;
		elseif(!empty($value)) {
			list($this->_servise, $value) = explode(';', $value);
			if($value != -1) $this->_value = $value;
			else $this->_disabled = true;
		}


		return true;
	}

	/**
	 * Упаковка значения поля
	 * @return string 
	 */
	public function packValue() {
		if(!$this->isSetFieldIndex()) $quotes = "'";
		else $quotes = '"';

		$value = $this->_value;
		if($value === null || $value === '') $value = -1;


		return $quotes . $value . $quotes;
	}


	public function getServise() {
		return $this->_servise;
	}


	public function setServise($value) {
		$this->_servise = $value;
	}


	public function getUrl() {
		return $this->_valueUrl;
	}


	public function setUrl($value) {
		$this->_valueUrl = $value;
	}


}