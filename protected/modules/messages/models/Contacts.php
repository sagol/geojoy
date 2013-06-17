<?php

namespace app\modules\messages\models;

/**
 * 
 */
class Contacts {


	public function getContacts($page) {
		$contacts = array();
		$sql = 'SELECT idcontacts AS id, c.idusers, name 
			FROM contacts c
			LEFT JOIN users u ON u.idusers = c.idusers
			WHERE owner = :owner
			ORDER BY u.name DESC';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindValue(':owner', \Yii::app()->user->id, \PDO::PARAM_INT);
		$dataReader = $command->query();

		while(($data = $dataReader->read()) !== false) {
			$contacts[$data['id']] = $data;
		}


		return $contacts;
	}


	public function del($contacts) {
		foreach($contacts as $id => $contact) {
			$contact = (int)$contact;
			if($contact == 0) unset($contacts[$id]);
			else $contacts[$id] = $contact;
		}

		if(empty($contacts)) $json['status'] = 'error';
		else {
			$sql = 'DELETE FROM contacts * 
				WHERE owner = :owner AND idcontacts IN (' . implode(',', $contacts) . ')';
			$command = \Yii::app()->db->createCommand($sql);
			$command->bindValue(':owner', \Yii::app()->user->id, \PDO::PARAM_INT);
			$rowCount = $command->execute();

			if($rowCount > 0) {
				$json['status'] = 'ok';
				$json['del'] = $contacts;
			}
			else $json['status'] = 'error';
		}

		return json_encode($json);
	}


}