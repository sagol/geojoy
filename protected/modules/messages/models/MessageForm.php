<?php

namespace app\modules\messages\models;

/**
 * 
 */
class MessageForm extends \CFormModel {


	/**
	 * Пароль пользователя
	 * @var string 
	 */
	public $idThread = 0;
	public $idObjects = 0;
	public $user;
	public $text;
	public $reservation = 0;


	/**
	 * 
	 * @return array 
	 */
	public function rules() {
		return array(
			array('text', 'required'),
			array('text', 'filter', 'filter' => 'strip_tags'),
			array('user', 'checkContact', 'on' => 'writerContacts'),
		);
	}


	public function checkContact($attribute, $params) {
		$sql = 'SELECT * 
			FROM contacts 
			WHERE owner = :owner AND idusers = :user';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindValue(':owner', \Yii::app()->user->id, \PDO::PARAM_INT);
		$command->bindParam(':user', $this->user, \PDO::PARAM_INT);
		$dataReader = $command->query();


		if(($data = $dataReader->read()) === false) {
			$this->addError($attribute, \Yii::t('app\modules\messages\MessagesModule.messages', 'ERROR_CONTACT_NOT_EXIST'));
			\Yii::app()->message->add(\Yii::t('app\modules\messages\MessagesModule.messages', 'ERROR_CONTACT_NOT_EXIST'));
		}
	}


	/**
	 * Labels для полей формы
	 * @return array 
	 */
	public function attributeLabels() {
		return array(
			'text' => \Yii::t('app\modules\messages\MessagesModule.messages', 'FIELD_TEXT'),
		);
	}


	public function save() {
		$writer = \Yii::app()->user->id;
		$sql = 'INSERT INTO messages (idthread, idobjects, writer, replay, text, reservation, owner, notread)
			VALUES (:idthread, :idobjects, :writer, :replay, :text, :reservation, :owner, :notread)
			RETURNING *';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':idthread', $this->idThread, \PDO::PARAM_INT);
		$command->bindParam(':idobjects', $this->idObjects, \PDO::PARAM_INT);
		$command->bindValue(':writer', $writer, \PDO::PARAM_INT);
		$command->bindParam(':replay', $this->user, \PDO::PARAM_INT);
		$command->bindParam(':text', $this->text, \PDO::PARAM_STR);
		$command->bindParam(':reservation', $this->reservation, \PDO::PARAM_INT);
		$command->bindValue(':owner', $writer, \PDO::PARAM_INT);
		$command->bindValue(':notread', 0, \PDO::PARAM_INT);
		$data = $command->query()->read();


		$sql = 'INSERT INTO messages (idthread, idobjects, writer, replay, text, reservation, owner, notread)
			VALUES (:idthread, :idobjects, :writer, :replay, :text, :reservation, :owner, :notread)';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':idthread', $this->idThread, \PDO::PARAM_INT);
		$command->bindParam(':idobjects', $this->idObjects, \PDO::PARAM_INT);
		$command->bindValue(':writer', $writer, \PDO::PARAM_INT);
		$command->bindParam(':replay', $this->user, \PDO::PARAM_INT);
		$command->bindParam(':text', $this->text, \PDO::PARAM_STR);
		$command->bindParam(':reservation', $this->reservation, \PDO::PARAM_INT);
		$command->bindValue(':owner', $this->user, \PDO::PARAM_INT);
		$command->bindValue(':notread', 1, \PDO::PARAM_INT);
		$rowCount = $command->execute();

		$paramsNewMessage = \Yii::app()->params['cache']['newMessage'];
		if($paramsNewMessage !== -1) {
			// получение из кеша
			$cache = \Yii::app()->cache;
			$count = (int)$cache->get('newMessage-' . $this->user);
			$count++;
			$cache->set('newMessage-' . $this->user, $count, $paramsNewMessage);
		}

		if($writer != $this->user) {
			$sql = 'INSERT INTO contacts (idusers, owner)
				SELECT :idusers, :owner 
				FROM contacts 
				WHERE NOT EXISTS (
					SELECT 1 
					FROM contacts 
					WHERE idusers = :idusers AND owner = :owner
				)
				LIMIT 1';

			$command = \Yii::app()->db->createCommand($sql);
			$command->bindValue(':owner', $writer, \PDO::PARAM_INT);
			$command->bindValue(':idusers', $this->user, \PDO::PARAM_INT);
			$command->execute();
		}

		return $data;
	}


}