<?php

namespace app\models\fields\edit;

/**
 * Базавое поле полей содержащих выбираемые данные
 */
class FieldList  extends Field {


	/**
	 * Id поля родителя
	 * @var integer 
	 */
	protected $_parent;
	/**
	 * Имя поля родителя
	 * @var string 
	 */
	protected $_parentName;

	protected $_lists;
	protected $_orders_values = 0;
	protected $skipInitLists = false;


	/**
	 * Создание поля
	 * @param array $data
	 * @param \app\managers\Manager $manager 
	 */
	public function __construct($data, &$manager) {
		if(!empty($data['orders_values'])) $this->_orders_values = $data['orders_values'];

		parent::__construct($data, $manager);
	}


	/**
	 * Инициализация поля 
	 */
	public function init() {
		// получение возможных значений поля при отсутствии у него родителя
		if($this->_id && !$this->_parent && (empty($this->initOptions['skipInitLists']) || (!empty($this->initOptions['skipInitLists']) && !$this->initOptions['skipInitLists']))) {
			$sql = 'SELECT idobj_fields_values, value, translate, count 
				FROM obj_fields_values 
				WHERE idobj_fields = :id AND disabled = 0 
				ORDER BY orders, idobj_fields_values';
			$command = \Yii::app()->db->createCommand($sql);
			$command->bindParam(':id', $this->_id, \PDO::PARAM_INT);
			$dataReader = $command->query();

			while(($data = $dataReader->read()) !== false) {
				$id = $data['idobj_fields_values'];
				unset($data['idobj_fields_values']);
				$this->_lists[$id] = $data;
			}
		}

		if($this->_parent) {
			$sql = 'SELECT name, type 
				FROM obj_fields 
				WHERE idobj_fields = :id';
			$command = \Yii::app()->db->createCommand($sql);
			$command->bindParam(':id', $this->_parent, \PDO::PARAM_INT);
			$dataReader = $command->query();

			if(($data = $dataReader->read()) !== false) {
				$this->_parentName = $data['name'];
			}
		}

		unset($dataReader, $values, $firstValue);


		parent::init();
	}


	public function data($needUse = false) {
		$data = array();

		if($this->_parent === null && !empty($this->_lists)) {
			foreach($this->_lists as $id => $value)
				if(!$needUse || ($needUse && $value['count'])) {
					if($value['translate']) $data[$id] = \Yii::t('lists', $value['value']);
					else $data[$id] = $value['value'];
				}

			// натуральная сортировка без учета регистра
			if($this->_orders_values) asort($data, SORT_NATURAL | SORT_FLAG_CASE);
		}


		return $data;
	}


	/**
	 * Возвращает текстовое значение
	 * @return string 
	 */
	public function getValueText() {
		if(!empty($this->_lists) && array_key_exists($this->_value, $this->_lists)) {
			if($this->_lists[$this->_value]['translate'])
				return \Yii::t('lists', $this->_lists[$this->_value]['value']);


			return $this->_lists[$this->_value]['value'];
		}


		return $this->_value;
	}


	public function getParent() {
		return $this->_parent;
	}


	public function getParentName() {
		return $this->_parentName;
	}


	public function getLists() {
		return $this->_lists;
	}


	public function ajaxData($data) {
		$this->_lists = array();
		$sql = 'SELECT * 
			FROM obj_fields_values 
			WHERE idobj_fields = :id AND parent = :parent AND disabled = 0 ' . 
			(isset($data['needUse']) && $data['needUse'] ? 'AND count > 0 ' : '') . 
			'ORDER BY orders, idobj_fields_values';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':id', $data['fieldId'], \PDO::PARAM_INT);
		$command->bindParam(':parent', $data['parentValue'], \PDO::PARAM_INT);
		$dataReader = $command->query();

		while(($data = $dataReader->read()) !== false) {
			$this->_lists[$data['idobj_fields_values']] = $data['translate'] ? \Yii::t('lists', $data['value']) : $data['value'];

		}

		asort($this->_lists);

		// return $values;
	}


	/**
	 * Установка значения
	 * @param mix $value 
	 */
	public function setValue($value) {
		// преобразование в целое число, можно попробовать для увеличения/уменьшения ))) производительности
		// так же надо раскоментить код в Checklist.php
		if(empty($value)) $this->_value = null;
		elseif(is_array($value)) {
			foreach($value as &$val)
				settype($val, 'int');

			$this->_value = $value;
		}
		else $this->_value = (int)$value;

		//$this->_value = $value;
	}


}
