<?php

namespace app\models\messages;

/**
 * Логи
 */
class Messages extends \CActiveRecord {


	static $_users;


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
		return 'messages';
	}


	/**
	 * Labels для полей формы
	 * @return array 
	 */
	public function attributeLabels() {
		return array(
			'idmessages' => 'ID',
			'notread' => \Yii::t('admin', 'MESSAGES_FIELD_NOTREAD'),
			'reservation' => \Yii::t('admin', 'MESSAGES_FIELD_RESERVATION'),
			'writer' => \Yii::t('admin', 'MESSAGES_FIELD_WRITER'),
			'replay' => \Yii::t('admin', 'MESSAGES_FIELD_REPLAY'),
			'text' => \Yii::t('admin', 'MESSAGES_FIELD_TEXT'),
			'date' => \Yii::t('admin', 'MESSAGES_FIELD_DATE'),
		);
	}


	/**
	 * Провайдер для AR модели
	 * @return \CActiveDataProvider 
	 */
	public function search() {
		$criteria = new \CDbCriteria;

		$criteria->compare('idmessages', $this->text);
		$criteria->compare('notread', $this->notread);
		$criteria->compare('reservation', $this->reservation);
		$criteria->compare('writer', $this->writer);
		$criteria->compare('replay', $this->replay);
		$criteria->compare('text', $this->text, true);
		$criteria->compare('date', $this->date, true);

		return new \CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array('defaultOrder' => 'date DESC'),
			'pagination' => array(
				'pageSize' => 50,
			),
		));
	}


	public function __get($name) {
		if($name == 'writer_val') return $this->_name(parent::__get('writer'));
		elseif($name == 'replay_val') return $this->_name(parent::__get('replay'));
		else return parent::__get($name);
	}


	/**
	 * Операции перед сохранением (запрет создания/изменения)
	 * @return boolean 
	 */
	public function beforeSave() {
		return false;
	}


	static function link($data, $row, $column) {
		$user = $data->attributes[$column->name];
		if(empty(self::$_users[$user])) self::$_users[$user] = \app\models\users\User::name($user);
		$name = self::$_users[$user];


		return \CHtml::link($name, array('/site/user/profile/', 'id' => $user), array('target' => '_blank'));
	}


	private function _name($user) {
		if(empty(self::$_users[$user])) self::$_users[$user] = \app\models\users\User::name($user);


		return self::$_users[$user];
	}

}