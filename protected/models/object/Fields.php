<?php

namespace app\models\object;

/**
 * Поля
 */
class Fields extends \CActiveRecord {



	/**
	 * Родители
	 * @var array 
	 */
	private static $_parent = array();


	/**
	 * Создание AR модели
	 * @param string $className
	 * @return \app\models\object\Fields 
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}


	/**
	 * Таблица базы, с которой работает AR модель
	 * @return string 
	 */
	public function tableName() {
		return 'obj_fields';
	}


	/**
	 * Правила валидации модели
	 * @return array 
	 */
	public function rules() {
		return array(
			array('type, name, title', 'required'),
			array('parent, type, orders_values', 'numerical', 'integerOnly' => true),
			array('name, title, units', 'length', 'max' => 50),
			array('units, orders_values', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idobj_fields, parent, type, name, title, units, orders_values', 'safe', 'on' => 'search'),
		);
	}


	/**
	 * Labels для полей формы
	 * @return array 
	 */
	public function attributeLabels() {
		return array(
			'idobj_fields' => 'ID',
			'name' => \Yii::t('admin', 'FIELDS_FIELD_NAME'),
			'title' => \Yii::t('admin', 'FIELDS_FIELD_TITLE'),
			'type' => \Yii::t('admin', 'FIELDS_FIELD_TYPE'),
			'type_val' => \Yii::t('admin', 'FIELDS_FIELD_TYPE'),
			'parent' => \Yii::t('admin', 'FIELDS_FIELD_PARENT'),
			'parent_val' => \Yii::t('admin', 'FIELDS_FIELD_PARENT'),
			'units' => \Yii::t('admin', 'FIELDS_FIELD_UNITS'),
			'orders_values' => \Yii::t('admin', 'FIELDS_FIELD_ORDERS_VALUES'),
		);
	}


	/**
	 * Провайдер для AR модели
	 * @return \CActiveDataProvider 
	 */
	public function search() {
		if(!is_numeric($this->type)) $this->type = array_search($this->type, $this->typeField());
		if(!is_numeric($this->parent)) $this->parent = array_search($this->parent, $this->parent_arr);

		// обработка значений числовых полей (для фильтра)
		if(!is_numeric($this->idobj_fields)) $this->idobj_fields = '';
		if(!is_numeric($this->type)) $this->type = '';
		if(!is_numeric($this->parent)) $this->parent = '';

		$criteria = new \CDbCriteria;
		$criteria->compare('idobj_fields', $this->idobj_fields);
		$criteria->compare('type', $this->type);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('title', $this->title, true);
		$criteria->compare('parent', $this->parent);

		return new \CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'pagination' => array(
				'pageSize' => 50,
			),
		));
	}


	/**
	 * Получение значений
	 * @param string $name
	 * @return mixed 
	 */
	public function __get($name) {
		if($name == 'type_val') return $this->typeField(false, parent::__get('type'));

		elseif($name == 'parent_val') return $this->parent(false, parent::__get('parent'), false);
		elseif($name == 'parent_arr') return $this->parent();
		elseif($name == 'parent_arr1') return $this->parent(true, null, false);

		else return parent::__get($name);
	}


	/**
	 * Тип поля
	 * @param boolean $arr
	 * @param integer $id
	 * @param boolean $all
	 * @return array|string 
	 */
	public function typeField($arr = true, $id = null) {
		if($arr) {
			if($this->getIsNewRecord()) return \app\fields\Field::getTypes();
			else return \app\fields\Field::editType($this->type);
		}

		$_types = \app\fields\Field::getTypes();


		if(isset($_types[$id])) return $_types[$id];
		else return null;
	}


	/**
	 * Родители
	 * @param boolean $arr
	 * @param integer $id
	 * @param boolean $all
	 * @return array|string 
	 */
	public function parent($arr = true, $id = null, $all = true) {
		if(empty(self::$_parent)) {
			self::$_parent[''] = \Yii::t('nav', 'ALL');
			self::$_parent[0] = \Yii::t('nav', 'NO');

			$sql = "SELECT idobj_fields, name 
				FROM obj_fields 
				ORDER BY name";
			$dataReader = \Yii::app()->db->createCommand($sql)->query();

			while(($data = $dataReader->read()) !== false)
				self::$_parent[$data['idobj_fields']] = $data['name'];
		}

		if($all) {
			if($arr) return self::$_parent;

			return @self::$_parent[$id];
		}
		else {
			$_parent = self::$_parent;
			unset($_parent['']);

			if($arr) return $_parent;

			return @$_parent[$id];
		}
	}


	/**
	 * Операции перед удалением
	 * @return boolean 
	 */
	public function beforeDelete() {
		// удаление значений полей
		$sql = "DELETE FROM obj_fields_values 
			WHERE idobj_fields = $this->idobj_fields";
		\Yii::app()->db->createCommand($sql)->execute();


		return true;
	}


	/**
	 * Операции после удаления
	 */
	public function afterDelete() {
		\app\models\object\Ties::deleteMany(array('fields' => $this->idobj_fields));

		// очистка кеша
		\Yii::app()->cache->flush();
	}


	/**
	 * Операции после сохранения
	 */
	public function afterSave() {
		// очистка кеша
		\Yii::app()->cache->flush();
	}


}