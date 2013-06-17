<?php

namespace app\models\fields\read;

/**
 * Поле карма
 */
class Karma extends Field {


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::KARMA;

		parent::init();
	}


	public function show() {
		$idUser = $this->_manager->getId();
		// разрешается только залогиненым и не самому себе ))
		if(!($voted = \Yii::app()->user->id) || $idUser == \Yii::app()->user->id) return false;

		$sql = 'SELECT idobj_karma 
			FROM obj_karma 
			WHERE idusers = :id AND voted = :voted LIMIT 1';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':id', $idUser, \PDO::PARAM_INT);
		$command->bindParam(':voted', $voted, \PDO::PARAM_INT);
		$dataReader = $command->query();

		if(($data = $dataReader->read()) !== false) return false;
		else return true;
	}


}