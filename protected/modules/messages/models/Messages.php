<?php

namespace app\modules\messages\models;

/**
 * 
 */
class Messages {


	public function mark($id, $mark) {
		if(!$id) return false;

		$sql = 'UPDATE messages SET mark = :mark
			WHERE idmessages = :id AND owner = :owner';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':id', $id, \PDO::PARAM_INT);
		$command->bindParam(':mark', $mark, \PDO::PARAM_INT);
		$command->bindValue(':owner', \Yii::app()->user->id, \PDO::PARAM_INT);
		$rowCount = $command->execute();


		return $rowCount;
	}

	static function isNew() {
		$user = \Yii::app()->user->id;
		$paramsNewMessage = \Yii::app()->params['cache']['newMessage'];
		if($paramsNewMessage !== -1) {
			// получение из кеша
			$cache = \Yii::app()->cache;
			$count = $cache->get('newMessage-' . $user);

			if($count !== false) return $count;
		}

		$sql = 'SELECT COUNT(*) 
			FROM messages m 
			WHERE notread = 1 AND owner = :owner';

		$command = \Yii::app()->db->createCommand($sql);
		$command->bindValue(':owner', $user, \PDO::PARAM_INT);
		$dataReader = $command->query();
		if(($data = $dataReader->read()) !== false) {
			if($paramsNewMessage !== -1) $cache->set('newMessage-' . $user, $data['count'], $paramsNewMessage);
			return $data['count'];
		}


		return false;
	}

}