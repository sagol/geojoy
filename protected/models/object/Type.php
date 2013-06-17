<?php

namespace app\models\object;

/**
 * Типы объявлений
 */
class Type extends \CActiveRecord {


	/**
	 * Создание AR модели
	 * @param string $className
	 * @return \app\models\object\Type 
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}


	/**
	 * Таблица базы, с которой работает AR модель
	 * @return string 
	 */
	public function tableName() {
		return 'obj_type';
	}


	/**
	 * Правила валидации модели
	 * @return array 
	 */
	public function rules() {
		return array(
			array('name', 'required'),
			array('idobj_type, name', 'safe', 'on' => 'search'),
		);
	}


	/**
	 * Labels для полей формы
	 * @return array 
	 */
	public function attributeLabels() {
		return array(
			'idobj_type' => 'ID',
			'name' => \Yii::t('admin', 'OBJECTS_TYPES_FIELD_NAME'),
		);
	}


	/**
	 * Провайдер для AR модели
	 * @return \CActiveDataProvider 
	 */
	public function search() {
		// обработка значений числовых полей (для фильтра)
		if(!is_numeric($this->idobj_type)) $this->idobj_type = '';

		$criteria = new \CDbCriteria;

		$criteria->compare('idobj_type', $this->idobj_type);
		$criteria->compare('name', $this->name, true);

		return new \CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'pagination' => array(
				'pageSize' => 50,
			),
		));
	}


	/**
	 * Операции после удаления
	 */
	public function afterDelete() {
		\app\models\object\Ties::deleteMany(array('type' => $this->idobj_type));
		// удаление из кеша
		$cache = \Yii::app()->cache;
		$cache->delete("fieldsType-read-$this->idobj_type");
		$cache->delete("fieldsType-edit-$this->idobj_type");
	}


}