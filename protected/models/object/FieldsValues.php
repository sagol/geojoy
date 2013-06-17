<?php

namespace app\models\object;

/**
 * Значения полей
 */
class FieldsValues extends \CActiveRecord {


	/**
	 *  Поля
	 * @var array 
	 */
	private static $_objectFields = array();
	/**
	 * Родители
	 * @var array 
	 */
	private static $_parent = array();


	/**
	 * Создание AR модели
	 * @param string $className
	 * @return \app\models\object\FieldsValues 
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}


	/**
	 * Таблица базы, с которой работает AR модель
	 * @return string 
	 */
	public function tableName() {
		return 'obj_fields_values';
	}


	/**
	 * Правила валидации модели
	 * @return array 
	 */
	public function rules() {
		return array(
			array('idobj_fields, value', 'required'),
			array('parent, idobj_fields, translate, disabled, count', 'numerical', 'integerOnly' => true),
			array('orders', 'safe'),
			array('idobj_fields_values, parent, idobj_fields, value, translate, orders, disabled, count', 'safe', 'on' => 'search'),
		);
	}


	/**
	 * Labels для полей формы
	 * @return array 
	 */
	public function attributeLabels() {
		return array(
			'idobj_fields_values' => 'ID',
			'idobj_fields' => \Yii::t('admin', 'FIELDS_VALUES_FIELD_FIELD'),
			'value' => \Yii::t('admin', 'FIELDS_VALUES_FIELD_VALUE'),
			'orders' => \Yii::t('admin', 'FIELDS_VALUES_FIELD_ORDERS'),
			'disabled' => \Yii::t('admin', 'FIELDS_VALUES_FIELD_DISABLED'),
			'parent' => \Yii::t('admin', 'FIELDS_VALUES_FIELD_PARENT'),
			'parent_val' => \Yii::t('admin', 'FIELDS_VALUES_FIELD_PARENT'),
			'translate' => \Yii::t('admin', 'FIELDS_VALUES_FIELD_TRANSLATE'),
		);
	}


	/**
	 * Провайдер для AR модели
	 * @return \CActiveDataProvider 
	 */
	public function search() {
		if(!is_numeric($this->idobj_fields)) $this->idobj_fields = array_search($this->idobj_fields, $this->idobj_fields_arr);
		if(!is_numeric($this->parent) && !empty($this->parent)) {
			$result = $this->find(("value = '$this->parent'"));
			if($result) $this->parent = $result->idobj_fields_values;
			else $this->parent = 0;
		}

		// обработка значений числовых полей (для фильтра)
		if(!is_numeric($this->idobj_fields_values)) $this->idobj_fields_values = '';
		if(!is_numeric($this->orders)) $this->orders = '';
		if(!is_numeric($this->translate)) $this->translate = '';
		if(!is_numeric($this->disabled)) $this->disabled = '';

		$criteria = new \CDbCriteria;
		$criteria->compare('idobj_fields_values', $this->idobj_fields_values);
		$criteria->compare('idobj_fields', $this->idobj_fields);
		$criteria->compare('value', $this->value, true);
		$criteria->compare('orders', $this->orders);
		$criteria->compare('disabled', $this->disabled);
		$criteria->compare('parent', $this->parent);

		return new \CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'pagination' => array(
				'pageSize' => 50,
			),
		));
	}


	/**
	 * Имя поля, которому принадлежит значение
	 * @param integer $id
	 * @return string 
	 */
	public function fieldName($id) {
		$sql = 'SELECT name 
			FROM obj_fields 
			WHERE idobj_fields = :id';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':id', $id, \PDO::PARAM_INT);
		$dataReader = $command->query();
		if(($data = $dataReader->read()) !== false) $fieldName = $data['name'];

		return $fieldName;
	}


	/**
	 * Получение значений
	 * @param string $name
	 * @return mixed 
	 */
	public function __get($name) {
		if($name == 'idobj_fields_val') return $this->idObjectFields(false, parent::__get('idobj_fields'), false);
		elseif($name == 'idobj_fields_arr') return $this->idObjectFields();
		elseif($name == 'idobj_fields_arr1') return $this->idObjectFields(true, null, false);

		elseif($name == 'parent_val') return $this->parent(parent::__get('idobj_fields'), false, parent::__get('parent'), false);
		elseif($name == 'parent_arr') return $this->parent(parent::__get('idobj_fields'));
		elseif($name == 'parent_arr1') return $this->parent(parent::__get('idobj_fields'), true, null, false);

		else return parent::__get($name);
	}


	/**
	 * Поля для которых возможно создание значений
	 * @param boolean $arr
	 * @param integer $id
	 * @param boolean $all
	 * @return array|string 
	 */
	public function idObjectFields($arr = true, $id = null, $all = true) {
		if(empty(self::$_objectFields)) {
			self::$_objectFields[''] = \Yii::t('nav', 'ALL');

			$sql = "SELECT idobj_fields, name 
				FROM obj_fields 
				WHERE type >=8 AND type <=11 
				ORDER BY name";
			$dataReader = \Yii::app()->db->createCommand($sql)->query();

			while(($data = $dataReader->read()) !== false)
				self::$_objectFields[$data['idobj_fields']] = $data['name'];
		}

		if($all) {
			if($arr) return self::$_objectFields;

			return @self::$_objectFields[$id];
		}
		else {
			$_objectFields = self::$_objectFields;
			unset($_objectFields['']);

			if($arr) return $_objectFields;

			return @$_objectFields[$id];
		}
	}


	/**
	 * Родители
	 * @param integer $fieldId
	 * @param boolean $arr
	 * @param integer $id
	 * @param boolean $all
	 * @return array|string 
	 */
	public function parent($fieldId, $arr = true, $id = null, $all = true) {
		if(empty(self::$_parent[$fieldId])) {
			self::$_parent[$fieldId][''] = \Yii::t('nav', 'ALL');
			$values = $this->getParentFieldData($fieldId);
			foreach($values as $idValue => $value)
				self::$_parent[$fieldId][$idValue] = $value;
		}

		if($all) {
			if($arr) return self::$_parent[$fieldId];

			return @self::$_parent[$fieldId][$id];
		}
		else {
			$_parent = self::$_parent[$fieldId];
			unset($_parent['']);

			if($arr) return $_parent;

			return @$_parent[$id];
		}
	}


	/**
	 * Значения поля родителя
	 * @param integer $field
	 * @return array 
	 */
	function getParentFieldData($field) {
		// будущее нет )))
		$values = array(0 => '_');

		$sql = 'SELECT fl.idobj_fields_values, fl.value, fl.translate,  fl1.value AS parent_value
			FROM obj_fields of 
			LEFT JOIN obj_fields_values fl ON of.parent = fl.idobj_fields 
			LEFT JOIN obj_fields_values fl1 ON fl.parent = fl1.idobj_fields_values 
			WHERE of.idobj_fields = :id AND fl.disabled = 0 
			ORDER BY fl.orders, fl.idobj_fields_values';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':id', $field, \PDO::PARAM_INT);
		$dataReader = $command->query();

		while(($data = $dataReader->read()) !== false) {
			if(!empty($data['parent_value'])) 
				$values[$data['idobj_fields_values']] = $data['translate'] ? \Yii::t('lists', $data['parent_value']) . ' - ' . \Yii::t('lists', $data['value']) : $data['parent_value'] . ' - ' . $data['value'];
			else $values[$data['idobj_fields_values']] = $data['translate'] ? \Yii::t('lists', $data['value']) : $data['value'];
		}

		asort($values);
		$values[0] = \Yii::t('nav', 'NO');


		return $values;
	}


	/**
	 * Операции перед сохранением
	 * @return boolean 
	 */
	public function beforeSave() {
		if(!$this->orders) unset($this->orders);

		if(!$this->isNewRecord) {
			$this->value = trim($this->value);
			return true;
		}

		$values = explode("\n", $this->value);

		foreach($values as $id => $value) {
			$values[$id] = trim($value);
			if($values[$id] == '') unset($values[$id]);
		}

		$count = count($values);
		if($count == 0) return false;
		elseif($count == 1) {
			$this->value = array_shift($values);
			return true;
		}

		for($i = 0; $i < $count-1; $i++) {
			$this->value = array_shift($values);
			$this->setIsNewRecord(true);
			$return = $this->save();
		}

		$this->value = array_shift($values);


		return true;
	}


	/**
	 * Сохранение
	 * @param boolean $runValidation
	 * @param array $attributes
	 * @return boolean 
	 */
	public function save($runValidation = true, $attributes = null) {
		if($attributes === null) {
			$attributes = $this->attributes;
			unset($attributes['idobj_fields_values']);
			$attributes = array_keys($attributes);
		}


		return parent::save($runValidation, $attributes);

	}


}