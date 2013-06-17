<?php

namespace app\models;

class TableCode {


	/**
	 * Код регистрации
	 */
	const REGISTRATION = 0;
	/**
	 * Код восстановления пароля
	 */
	const RECOVERY_PASS = 1;
	/**
	 * Код создания мульти акка
	 */
	const MULTI_ACCOUNT = 2;
	/**
	 * Код подтверждения email`a
	 */
	const CONFIRMATION_EMAIL = 3;


	static function select($code = null, $user = null, $type) {
		if($code === null && $user === null) return false;

		$sql = 'SELECT * 
			FROM codes 
			WHERE type = :type';
		if($code) $sql .= ' AND code = :code';
		if($user) $sql .= ' AND idusers = :user';

		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':type', $type, \PDO::PARAM_INT);
		if($code) $command->bindParam(':code', $code, \PDO::PARAM_STR);
		if($user) $command->bindParam(':user', $user, \PDO::PARAM_INT);
		$dataReader = $command->query();


		if(($data = $dataReader->read()) !== false) {
			if(!empty($data['info'])) $data['info'] = unserialize(stream_get_contents($data['info']));

			return $data;
		}

		return false;
	}


	static function selectUser($code, $type) {
		$sql = 'SELECT idusers 
			FROM codes 
			WHERE code = :code AND type = :type';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':code', $code, \PDO::PARAM_STR);
		$command->bindParam(':type', $type, \PDO::PARAM_INT);

		return $command->queryScalar();
	}


	static function selectCode($user, $type) {
		$sql = 'SELECT code 
			FROM codes 
			WHERE idusers = :user AND type = :type';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':user', $user, \PDO::PARAM_INT);
		$command->bindParam(':type', $type, \PDO::PARAM_INT);

		return $command->queryScalar();
	}


	static function insert($iduser, $type, $info = null) {
		/* code character(32) NOT NULL UNIQUE */
		$code = \app\helpers\Main::randomString(32);

		$sql = "INSERT INTO codes (idusers, code, type, info) 
			VALUES (:idusers, :code, :type, :info)";
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':idusers', $iduser, \PDO::PARAM_INT);
		$command->bindParam(':code', $code, \PDO::PARAM_STR);
		$command->bindParam(':type', $type, \PDO::PARAM_INT);
		if(empty($info)) $command->bindParam(':info', new \CDbExpression('NULL'));
		else $command->bindParam(':info', serialize($info), \PDO::PARAM_LOB);

		if($command->execute()) return $code;

		return false;
	}


	static function deleteUser($iduser, $type = null) {
		$sql = 'DELETE FROM codes
			WHERE idusers = :idusers';
		if($type !== null) $sql .= ' AND type = :type';

		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':idusers', $iduser, \PDO::PARAM_INT);
		if($type !== null) $command->bindParam(':type', $type, \PDO::PARAM_INT);

		return $command->execute();
	}


	static function deleteCode($code, $type = null) {
		$sql = 'DELETE FROM codes 
			WHERE code = :code';
		if($type !== null) $sql .= ' AND type = :type';

		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':code', $code, \PDO::PARAM_STR);
		if($type !== null) $command->bindParam(':type', $type, \PDO::PARAM_INT);

		return $command->execute();
	}


}
