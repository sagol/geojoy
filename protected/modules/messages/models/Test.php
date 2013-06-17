<?php

namespace app\modules\messages\models;

/**
 * 
 */
class Test extends \CModel {


	public $id;
	public $name;
	public $writer;
	public $replay;
	public $text;
	public $notread;
	public $mark;
	public $date;


	public function attributeNames() {
	}

	static function object($data, $row, $column) {
		$interlocutor = $data['writer'] == \Yii::app()->user->id ? $data['replay'] : $data['writer'];
		if($data['id']) {
			return \CHtml::link(
				\Yii::t('app\modules\messages\MessagesModule.messages', 'OBJECT_ID', array('{id}' => $data['id'])),
				array('/site/objects/view/', 'id' => $data['id']),
				array('class' => '', 'target' => '_blank')
			);
		}
		else
			return \CHtml::link(
				\app\models\users\User::name($interlocutor),
				array('/site/user/profile', 'id' => $interlocutor),
				array('class' => '', 'target' => '_blank')
			);
	}


	static function lastMessage($data, $row, $column) {
		return \Yii::t('app\modules\messages\MessagesModule.messages', 'MESSAGE_FROM') .
			'' .$data['writer_name'] .
			'<p>' .
			$data['text'].
			'</p>';
	}


	static function viewUrl($data, $row, $column) {
		$filter = $column->buttons['view']['filter'];

		if($data['id']) 
			return \Yii::app()->controller->createUrl('/messages/threads/thread', array(
				'filter' => $filter,
				'id' => $data['id'],
			));
		else
			return \Yii::app()->controller->createUrl('/messages/threads/thread', array(
				'filter' => $filter,
				'id' => $data['id'],
				'user' => $data['writer'],
			));
	}


	public function search($filter) {
		if($filter == 'all') {
			$sql = 'SELECT idthread AS id, writer, replay, uw.name AS writer_name, ur.name AS replay_name, text, notread, mark, m.date, 
					CASE WHEN writer = :owner THEN ur.name
						ELSE uw.name
					END
					AS interlocutor 
				FROM messages m
				LEFT JOIN users uw ON uw.idusers = m.writer
				LEFT JOIN users ur ON ur.idusers = m.replay
				WHERE owner = :owner AND m.date = (
					SELECT MAX(date)
					FROM messages
					WHERE owner = :owner AND idthread = m.idthread
					GROUP BY idthread
				)';

			/*$sqlCount = 'SELECT COUNT(idthread) 
				FROM messages m
				LEFT JOIN users u ON u.idusers = m.writer
				WHERE owner = :owner AND m.date = (
					SELECT MAX(date)
					FROM messages
					WHERE owner = :owner AND idthread = m.idthread
					GROUP BY idthread
				)';*/
			$sqlCount = 'SELECT COUNT(idthread) 
				FROM messages m
				WHERE owner = :owner AND m.date = (
					SELECT MAX(date)
					FROM messages
					WHERE owner = :owner AND idthread = m.idthread
					GROUP BY idthread
				)';

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
				'SELECT idthread AS id, writer, replay, uw.name AS writer_name, ur.name AS replay_name, text, notread, mark, m.date, 
					CASE WHEN writer = :owner THEN ur.name
						ELSE uw.name
					END
					AS interlocutor  
				FROM messages m
				LEFT JOIN users uw ON uw.idusers = m.writer
				LEFT JOIN users ur ON ur.idusers = m.replay
				WHERE owner = :owner AND mark = 1';

			$sqlCount = 'SELECT COUNT(idthread) 
				FROM messages m
				LEFT JOIN users u ON u.idusers = m.writer
				WHERE owner = :owner AND mark = 1';
		}
		elseif($filter == 'notread') {
			$sql = 'SELECT idthread AS id, writer, replay, uw.name AS writer_name, ur.name AS replay_name, text, notread, mark, m.date, 
					CASE WHEN writer = :owner THEN ur.name
						ELSE uw.name
					END
					AS interlocutor  
				FROM messages m
				LEFT JOIN users uw ON uw.idusers = m.writer
				LEFT JOIN users ur ON ur.idusers = m.replay
				WHERE owner = :owner AND m.date = (
					SELECT MAX(date)
					FROM messages
					WHERE owner = :owner AND idthread = m.idthread AND notread = 1
					LIMIT 1
				)';

			$sqlCount = 'SELECT COUNT(idthread) 
				FROM messages m
				LEFT JOIN users u ON u.idusers = m.writer
				WHERE owner = :owner AND m.date = (
					SELECT MAX(date)
					FROM messages
					WHERE owner = :owner AND idthread = m.idthread AND notread = 1
					LIMIT 1
				)';
		}
		elseif($filter == 'notreply') {
			$sql = 'SELECT idthread AS id, writer, replay, uw.name AS writer_name, ur.name AS replay_name, text, notread, mark, m.date, 
					CASE WHEN writer = :owner THEN ur.name
						ELSE uw.name
					END
					AS interlocutor 
				FROM messages m
				LEFT JOIN users uw ON uw.idusers = m.writer
				LEFT JOIN users ur ON ur.idusers = m.replay
				WHERE replay = :owner AND owner = :owner AND m.date = (
					SELECT MAX(date)
					FROM messages
					WHERE owner = :owner AND idthread = m.idthread
					LIMIT 1
				)';

			$sqlCount = 'SELECT COUNT(idthread) 
				FROM messages m
				LEFT JOIN users u ON u.idusers = m.writer
				WHERE replay = :owner AND owner = :owner AND m.date = (
					SELECT MAX(date)
					FROM messages
					WHERE owner = :owner AND idthread = m.idthread
					LIMIT 1
				)';
		}
		elseif($filter == 'reservation') {
			$sql = 'SELECT idthread AS id, writer, replay, uw.name AS writer_name, ur.name AS replay_name, text, notread, mark, m.date, 
					CASE WHEN writer = :owner THEN ur.name
						ELSE uw.name
					END
					AS interlocutor 
				FROM messages m
				LEFT JOIN users uw ON uw.idusers = m.writer
				LEFT JOIN users ur ON ur.idusers = m.replay
				WHERE owner = :owner AND m.date = (
					SELECT MAX(date)
					FROM messages
					WHERE owner = :owner AND idthread = m.idthread AND reservation = 1
					LIMIT 1
				)';

			$sqlCount = 'SELECT COUNT(idthread) 
				FROM messages m
				LEFT JOIN users u ON u.idusers = m.writer
				WHERE owner = :owner AND m.date = (
					SELECT MAX(date)
					FROM messages
					WHERE owner = :owner AND idthread = m.idthread AND reservation = 1
					LIMIT 1
				)';
		}


		$command = \Yii::app()->db->createCommand($sqlCount);
		$command->bindValue(':owner', \Yii::app()->user->id, \PDO::PARAM_INT);
		$count = $command->queryScalar();

		$dataProvider = new \CSqlDataProvider($sql, array(
			'totalItemCount' => $count,
			'sort' => array(
				'defaultOrder' => 'm.date DESC',
				'attributes' => array('id', 'writer_name', 'interlocutor', 'replay_name', 'text', 'notread', 'date',),
			),
			'pagination' => array(
				'pageSize' => 50,
			),
		));
		$dataProvider->params[':owner'] = \Yii::app()->user->id;

		return $dataProvider;
	}

}