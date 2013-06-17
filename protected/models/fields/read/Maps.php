<?php

namespace app\models\fields\read;

/**
 * Поле карта
 */
class Maps extends Field {


	protected $_brand;
	protected $_onMap = 0;
	protected $_onMapField = 'on_map';


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::MAPS;

		parent::init();
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
				'field' => $this->_onMapField,
				'index' => Field::FIELD_INDEX_DISABLED,
			),
		);
	}


	/**
	 * Распаковка значения поля
	 * @param type string
	 * @return boolean 
	 */
	public function unPackValue($value) {
		$this->_onMap = $value[$this->_onMapField];

		$sql = $this->sqlSelect();
		$value = $value[$sql[0]['field']];
		if($this->isSetFieldIndex()) {
			if(isset($value[$this->_fieldIndex])) $value = $value[$this->_fieldIndex];
			else return false;
		}

		if(!empty($value)) {
			$f = strpos($value, ':');
			if($f !== false) {
				$this->_brand = substr($value, 0, $f);
				$this->_value = substr($value, $f+1);
			}
			else {
				$this->_brand = \Yii::app()->params['defaultMapInterface'];
				$this->_value = $value;
			}
		}


		return true;
	}


	public function getBrand() {
		if(!$this->_brand) $this->_brand = \Yii::app()->params['defaultMapInterface'];
		if($this->_brand == 'g') return 'Google';
		if($this->_brand == 'y') return 'Yandex';
	}


	public function getCoord() {
		return $this->_value;
	}


	public function getOnMap() {
		return $this->_onMap;
	}

}