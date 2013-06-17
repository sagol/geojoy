<?php

namespace app\models\users;

/**
 * Карма
 */
class Karma extends \CActiveRecord {


	/**
	 * Имя пользователя
	 * @var string 
	 */
	public $username;
	/**
	 * Имя голосовавшего
	 * @var string 
	 */
	public $votedname;


	/**
	 * Создание AR модели
	 * @param string $className
	 * @return \app\models\users\Karma 
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}


	/**
	 * Таблица базы, с которой работает AR модель
	 * @return string 
	 */
	public function tableName() {
		return 'obj_karma';
	}


	/**
	 * Правила валидации модели
	 * @return array 
	 */
	public function rules() {
		return array();
	}


	/**
	 * Labels для полей формы
	 * @return array 
	 */
	public function attributeLabels() {
		return array(
			'idobj_karma' => 'ID',
			'username' => \Yii::t('admin', 'KARMA_FIELD_USER'),
			'idusers' => \Yii::t('admin', 'KARMA_FIELD_USER'),
			'votedname' => \Yii::t('admin', 'KARMA_FIELD_VOTED'),
			'voted' => \Yii::t('admin', 'KARMA_FIELD_VOTED'),
			'comment' => \Yii::t('admin', 'KARMA_FIELD_COMMENT'),
			'points' => \Yii::t('admin', 'KARMA_FIELD_POINTS'),
			'moderated' => \Yii::t('admin', 'KARMA_FIELD_MODERATED'),
		);
	}


	/**
	 * Параметры, применяемые по умолчанию к запросам AR
	 * @return array 
	 */
	public function defaultScope() {
		return array(
			'select' => 't.*, u.name as username, u1.name as votedname',
			'join' => '
				LEFT JOIN users u USING(idusers) 
				LEFT JOIN users u1 ON t.voted = u1.idusers
			',
		);
	}


	/**
	 * Провайдер для AR модели
	 * @return \CActiveDataProvider 
	 */
	public function search() {
		// обработка значений числовых полей (для фильтра)
		if(!is_numeric($this->idobj_karma)) $this->idobj_karma = '';
		if(!is_numeric($this->points)) $this->points = '';

		$criteria = new \CDbCriteria(array('condition' => 'moderated = 0'));

		$criteria->compare('username', $this->username, true);
		$criteria->compare('votedname', $this->votedname, true);
		$criteria->compare('comment', $this->comment, true);
		$criteria->compare('points', $this->points);

		return new \CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'pagination' => array(
				'pageSize' => 50,
			),
		));
	}


	/**
	 * Утверждение кармы поднятой/опущеной пользователем 
	 * @param integer $id
	 * @return integer 
	 */
	public function approve($id) {

		$sql = 'UPDATE users u SET karma = karma + ok.points FROM obj_karma ok
			WHERE idobj_karma = :id AND u.idusers = ok.idusers';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':id', $id, \PDO::PARAM_INT);
		$rowCount = $command->execute();

		if($rowCount) {
			$sql = 'UPDATE obj_karma SET moderated = 1 
				WHERE idobj_karma = :id';
			$command = \Yii::app()->db->createCommand($sql);
			$command->bindParam(':id', $id, \PDO::PARAM_INT);
			$rowCount = $command->execute();
		}

		$karma = \CHtml::link("($id)", array('admin/users/karma/view', 'id' => $id));
		if($rowCount) \Yii::app()->appLog->user('KARMA_TITLE', 'KARMA_APPROVED', array('{user}' => $id, '{karma}' => $karma));
		else \Yii::app()->appLog->user('KARMA_TITLE', 'KARMA_APPROVED_ERROR', array('{user}' => $id, '{karma}' => $karma), \app\components\AppLog::TYPE_ERROR);


		return $rowCount;
	}


	/**
	 * Отклонение кармы поднятой/опущеной пользователем
	 * @param integer $id
	 * @return integer 
	 */
	public function deflect($id) {
		$sql = 'UPDATE obj_karma SET moderated = -1 
			WHERE idobj_karma = :id';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':id', $id, \PDO::PARAM_INT);
		$rowCount = $command->execute();

		$karma = \CHtml::link("($id)", array('admin/users/karma/view', 'id' => $id));
		if($rowCount) \Yii::app()->appLog->user('KARMA_TITLE', 'KARMA_REJECTED', array('{karma}' => $karma));
		else \Yii::app()->appLog->user('KARMA_TITLE', 'KARMA_REJECTED_ERROR', array('{karma}' => $karma), \app\components\AppLog::TYPE_ERROR);


		return $rowCount;
	}


}