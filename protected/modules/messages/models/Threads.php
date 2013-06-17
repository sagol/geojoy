<?php

namespace app\modules\messages\models;

/**
 * 
 */
class Threads {


	public function getThreads($page, $filter) {
		$threads = array();
		if($filter == 'all') {
			$sql = 'SELECT idthread AS id, name, writer, replay, text, notread, mark, m.date 
				FROM messages m
				LEFT JOIN users u ON u.idusers = m.writer
				WHERE owner = :owner AND m.date = (
					SELECT MAX(date)
					FROM messages
					WHERE owner = :owner AND idthread = m.idthread
					GROUP BY idthread
				)
				ORDER BY m.date DESC';
		}
		elseif($filter == 'mark') {
			$sql = /*'SELECT idthread AS id, name, writer, replay,  text, notread, mark, m.date 
				FROM messages m
				LEFT JOIN users u ON u.idusers = m.writer
				WHERE owner = :owner AND m.date = (
					SELECT MAX(date)
					FROM messages
					WHERE owner = :owner AND idthread = m.idthread AND mark = 1
					GROUP BY idthread
					LIMIT 1
				)
				ORDER BY m.date DESC';*/
				'SELECT idthread AS id, name, writer, replay,  text, notread, mark, m.date 
				FROM messages m
				LEFT JOIN users u ON u.idusers = m.writer
				WHERE owner = :owner AND mark = 1
				ORDER BY m.date DESC';
		}
		elseif($filter == 'notread') {
			$sql = 'SELECT idthread AS id, name, writer, replay, text, notread, mark, m.date 
				FROM messages m
				LEFT JOIN users u ON u.idusers = m.writer
				WHERE owner = :owner AND m.date = (
					SELECT MAX(date)
					FROM messages
					WHERE owner = :owner AND idthread = m.idthread AND notread = 1
					LIMIT 1
				)
				ORDER BY m.date DESC';
		}
		elseif($filter == 'notreply') {
			$sql = 'SELECT idthread AS id, name, writer, replay, text, notread, mark, m.date 
				FROM messages m
				LEFT JOIN users u ON u.idusers = m.writer
				WHERE replay = :owner AND owner = :owner AND m.date = (
					SELECT MAX(date)
					FROM messages
					WHERE owner = :owner AND idthread = m.idthread
					LIMIT 1
				)
				ORDER BY m.date DESC';
		}
		elseif($filter == 'reservation') {
			$sql = 'SELECT idthread AS id, name, writer, replay, text, notread, mark, m.date 
				FROM messages m
				LEFT JOIN users u ON u.idusers = m.writer
				WHERE owner = :owner AND m.date = (
					SELECT MAX(date)
					FROM messages
					WHERE owner = :owner AND idthread = m.idthread AND reservation = 1
					LIMIT 1
				)
				ORDER BY m.date DESC';
		}
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindValue(':owner', \Yii::app()->user->id, \PDO::PARAM_INT);
		$dataReader = $command->query();

		while(($data = $dataReader->read()) !== false) {
			$data['interlocutor'] = $data['writer'] == \Yii::app()->user->id ? $data['replay'] : $data['writer'];
			$threads[] = $data;
		}


		return $threads;
	}


	public function getThread($id, $user, &$messageForm, $limit = false) {
		$thread = array();
		$idThread = 0;
		$writer = \Yii::app()->user->id;

		$sql = 'SELECT idmessages AS id, idthread, idobjects, name, writer, replay, text, notread, mark, m.date 
			FROM messages m
			LEFT JOIN users u ON u.idusers = m.writer
			WHERE owner = :owner AND idthread = :thread ' . 
			($user ? 'AND ((writer = :user AND replay = :owner) OR (writer = :owner AND replay = :user))' : '') . 
			'ORDER BY m.date DESC' .
			($limit ? ' LIMIT ' . $limit : '');
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':owner', $writer, \PDO::PARAM_INT);
		$command->bindParam(':thread', $id, \PDO::PARAM_INT);
		if($user) $command->bindParam(':user', $user, \PDO::PARAM_INT);
		$dataReader = $command->query();

		while(($data = $dataReader->read()) !== false)
			$thread[$data['id']] = $data;

		if(!empty($thread)) {
			$data = current($thread);
			if($data['replay'] != $writer)  $messageForm->user = $data['replay'];
			else  $messageForm->user = $data['writer'];
			$messageForm->idThread = $data['idthread'];
			$messageForm->idObjects = $data['idobjects'];

			$sql = 'UPDATE messages SET notread = 0
				WHERE owner = :owner AND idthread = :thread';
			$command = \Yii::app()->db->createCommand($sql);
			$command->bindValue(':owner', \Yii::app()->user->id, \PDO::PARAM_INT);
			$command->bindParam(':thread', $id, \PDO::PARAM_INT);
			$rowCount = $command->execute();

			$paramsNewMessage = \Yii::app()->params['cache']['newMessage'];
			if($paramsNewMessage !== -1) \Yii::app()->cache->delete('newMessage-' . $writer);
		}


		return array($thread, $messageForm->user);
	}


	public function del($threads) {
		$threads = (array)$threads;
		foreach($threads as $id => $thread) {
			$thread = (int)$thread;
			if($thread == 0) unset($threads[$id]);
			else $threads[$id] = $thread;
		}

		if(empty($threads)) $json['status'] = 'error';
		else {
			$sql = 'DELETE FROM messages * 
				WHERE owner = :owner AND idthread IN (' . implode(',', $threads) . ')';
			$command = \Yii::app()->db->createCommand($sql);
			$command->bindValue(':owner', \Yii::app()->user->id, \PDO::PARAM_INT);
			$rowCount = $command->execute();

			if($rowCount > 0) {
				$json['status'] = 'ok';
				$json['del'] = $threads;
			}
			else $json['status'] = 'error';
		}

		return json_encode($json);
	}

}
