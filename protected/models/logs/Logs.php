<?php

namespace app\models\logs;

/**
 * Логи
 */
class Logs extends \CActiveRecord {


	/**
	 * Создание AR модели
	 * @param string $className
	 * @return \app\models\logs\Logs 
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}


	/**
	 * Таблица базы, с которой работает AR модель
	 * @return string 
	 */
	public function tableName() {
		return 'logs';
	}


	/**
	 * Labels для полей формы
	 * @return array 
	 */
	public function attributeLabels() {
		return array(
			'idlogs' => 'ID',
			'type' => \Yii::t('admin', 'LOGS_FIELD_TYPE'),
			'action' => \Yii::t('admin', 'LOGS_FIELD_ACTION'),
			'title' => \Yii::t('admin', 'LOGS_FIELD_TITLE'),
			'message' => \Yii::t('admin', 'LOGS_FIELD_MESSAGE'),
			'date' => \Yii::t('admin', 'LOGS_FIELD_DATE'),
		);
	}


	/**
	 * Провайдер для AR модели
	 * @return \CActiveDataProvider 
	 */
	public function search() {
		// обработка значений числовых полей (для фильтра)
		if(!is_numeric($this->idlogs)) $this->idlogs = '';
		if(!is_numeric($this->type)) $this->type = '';

		$criteria = new \CDbCriteria;

		$criteria->compare('idlogs', $this->idlogs);
		$criteria->compare('type', $this->type);
		$criteria->compare('action', $this->action, true);
		$criteria->compare('message', $this->message, true);
		$criteria->compare('date', $this->date, true);

		return new \CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array('defaultOrder' => 'date DESC'),
			'pagination' => array(
				'pageSize' => 50,
			),
		));
	}


	/**
	 * Операции перед сохранением (запрет создания/изменения)
	 * @return boolean 
	 */
	public function beforeSave() {
		return false;
	}


	static function value($data, $row, $column) {
		$value = $data->attributes[$column->name];

		$values = $data->attributes['params'];
		$params = array();
		if(!empty($values)){
			$values = explode(',', $values);
			if(!empty($values))
				foreach($values as $val) {
					$f = strpos($val, '=');
					$name = trim(substr($val, 0, $f));
					$val = trim(substr($val, $f+1));
					$params[$name] = $val;
				}
		}


		return \Yii::t('logs', $value, $params);
	}

}