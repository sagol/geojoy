<?php

namespace app\models\object;

/**
 * Группы связей
 */
class TiesGroups extends \CActiveRecord {


	/**
	 * Создание AR модели
	 * @param string $className
	 * @return \app\models\object\TiesGroups 
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}


	/**
	 * Таблица базы, с которой работает AR модель
	 * @return string 
	 */
	public function tableName() {
		return 'obj_ties_groups';
	}


	/**
	 * Правила валидации модели
	 * @return array 
	 */
	public function rules() {
		return array(
			array('name', 'required'),
			array('name', 'unique'),
			array('idobj_ties_groups, name, orders', 'safe', 'on' => 'search'),
		);
	}


	/**
	 * Labels для полей формы
	 * @return array 
	 */
	public function attributeLabels() {
		return array(
			'idobj_ties_groups' => 'ID',
			'name' => \Yii::t('admin', 'TIES_GROUP_FIELD_NAME'),
			'orders' => \Yii::t('admin', 'TIES_GROUP_FIELD_ORDERS'),
		);
	}


	/**
	 * Провайдер для AR модели
	 * @return \CActiveDataProvider 
	 */
	public function search() {
		// обработка значений числовых полей (для фильтра)
		if(!is_numeric($this->idobj_ties_groups)) $this->idobj_ties_groups = '';

		$criteria = new \CDbCriteria;

		$criteria->compare('idobj_ties_groups', $this->idobj_ties_groups);
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
		\app\models\object\Ties::deleteMany(array('type' => $this->idobj_ties_groups));
	}


}