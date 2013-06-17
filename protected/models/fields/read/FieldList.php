<?php

namespace app\models\fields\read;

/**
 * Базавое поле полей содержащих выбираемые данные
 */
class FieldList  extends Field {


	protected $_lists;

	public function loadData() {
		if(empty($this->_value)) return false;

		$paramsFieldData = \Yii::app()->params['cache']['fieldData'];
		$cache = \Yii::app()->cache;

		if(is_array($this->_value)) {
			// получение из кеша
			if($paramsFieldData !== -1) {
				foreach($this->_value as $value) {
					$data = $cache->get("fieldData-$value");
					if($data === false) $values[] = $value;
					else
						foreach($data as $id => $d)
							$this->_lists[$id] = $d;
				}
				
				if(empty($values)) return true;
			}
			else $values = $this->_value;

			// получение из базы
			$sql = 'SELECT idobj_fields_values, value, translate, count 
				FROM obj_fields_values 
				WHERE disabled = 0 AND idobj_fields_values IN (' . implode(', ', $values) . ')';
			$command = \Yii::app()->db->createCommand($sql);
		}
		else {
			// получение из кеша
			if($paramsFieldData !== -1) {
				$dataLists = $cache->get("fieldData-$this->_value");
				if($dataLists !== false) {
					$this->_lists = $dataLists;
					return true;
				}
			}

			// получение из базы
			$sql = 'SELECT idobj_fields_values, value, translate, count 
				FROM obj_fields_values 
				WHERE disabled = 0 AND idobj_fields_values = :id';
			$command = \Yii::app()->db->createCommand($sql);
			$command->bindParam(':id', $this->_value, \PDO::PARAM_INT);
		}
		$dataReader = $command->query();

		while(($data = $dataReader->read()) !== false) {
			$id = $data['idobj_fields_values'];
			unset($data['idobj_fields_values']);
			$this->_lists[$id] = $data;
		}

		if(!is_array($this->_value)) {
			if($paramsFieldData !== -1) {
				$cache = \Yii::app()->cache;
				$dataLists = $cache->set("fieldData-$this->_value", $this->_lists, $paramsFieldData);
			}
		}

		return true;
	}


	public function data($needUse = false) {
		$data = array();

		if($this->_parent === null && !empty($this->_lists)) {
			foreach($this->_lists as $id => $value)
				if(!$needUse || ($needUse && $value['count'])) {
					if($value['translate']) $data[$id] = \Yii::t('lists', $value['value']);
					else $data[$id] = $value['value'];
				}

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


	public function getLists() {
		return $this->_lists;
	}


}
