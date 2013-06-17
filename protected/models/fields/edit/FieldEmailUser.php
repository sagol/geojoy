<?php

namespace app\models\fields\edit;

/**
 * Поле email пользователя
 */
class FieldEmailUser extends Email {


	/**
	 * Проверка уникальности Email`а для пользователей, таблица users
	 * отличие от оригинала - аккаунт соцсети (\app\models\users\User::ACCOUNT_SOCIAL) может иметь не уникальные email`ы
	 * @param string $attribute
	 * @param array $params 
	 */
	public function uniqueEmail($attribute, $params) {
		$users = array();
		$curUserId = (int)\Yii::app()->user->id;

		$email = $this->getValue();
		$sql = "SELECT u.*, idservices 
			FROM users u 
			LEFT JOIN services s ON u.idusers = s.idusers 
			WHERE u.idusers = :user OR $this->_field = :email OR $this->_field LIKE :email1 OR $this->_field LIKE :email2";
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindValue(':email', $email, \PDO::PARAM_STR);
		$command->bindValue(':email1', "%;$email;%", \PDO::PARAM_STR);
		$command->bindValue(':email2', "%]$email;%", \PDO::PARAM_STR);
		$command->bindParam(':user', $curUserId, \PDO::PARAM_INT);
		$dataReader = $command->query();

		while(($data = $dataReader->read()) !== false)
			$users[$data['idusers']] = $data;

		$count = count($users);
		if($curUserId == 0 && $count >= 1) {
			$this->addError($attribute, \Yii::t('app\modules\emailField\EmailFieldModule.fields', 'FIELDS_EMAIL_ERROR_EMAIL_EXIST'));
			return;
		}

		if($count > 1) {
			// если это аккаунт не из соцсети (\app\models\users\User::ACCOUNT_DEFAULT)
			// удаляем из массива аккаунты соцсетей.
			if(empty($users[$curUserId]['idservices'])) {
				foreach($users as $id => $user)
					if($user['idservices']) unset($users[$id]);
				
				if(count($users) > 1) {
					$this->addError($attribute, \Yii::t('app\modules\emailField\EmailFieldModule.fields', 'FIELDS_EMAIL_ERROR_EMAIL_EXIST'));
					return;
				}
			}
			// аккаунт соцсети может иметь несколько email`ов
			// удаляем остальные акки и переходим к подготовке подтверждения email`а
			else {
				foreach($users as $id => $user)
					if($id != $curUserId) unset($users[$id]);
			}
		}

		if(count($users) == 1) {
			// распаковываем полученные из базы старый ($old) и текущий email ($value)
			// старый ($old) - подтвержденный email, его наличие означает, что пользователь вводил уже email для смены ($value) и еще не подтвердил его.
			// текущий email ($value) - при отсутствии старого ($old) является действтельным и подтвержденным пользователем.
			list($old, $value) = $this->_unPackOldValue($users[$curUserId][$this->_field]);

			// старого нету
			if($old === null) {
				// смена текущего email`а ($value) на новый ($this->getValue()) введенный пользователем
				if($value != $this->getValue()) {
					$this->_oldEmail = $value;
					if(!$this->_skipConfirmationEmail) $this->_info = \Yii::t('app\modules\emailField\EmailFieldModule.fields', 'FIELDS_EMAIL_INFO_CONFIRM_EMAIL');
					$this->_sendConfirmationEmail = !$this->_skipConfirmationEmail;
				}
			}
			// старый есть, уже была попытка смены email`а
			else {
				// старый и введенный пользователем равны, т.е. пользователь отменяет смену email`а, тут подтверждение не нужно
				if($old == $this->getValue()) $this->_oldEmail = null;
				// если $value == $this->getValue(), то подтверждение тоже не нужно, пользователь еще не подтвердил email, а равны, т.к. он меняет другую информацию
				// пользователь не подтвердил email ($value) и решил ввести другой.
				elseif($value != $this->getValue()) {
					$this->_info = \Yii::t('app\modules\emailField\EmailFieldModule.fields', 'FIELDS_EMAIL_INFO_CONFIRM_EMAIL');
					$this->_sendConfirmationEmail = !$this->_skipConfirmationEmail;
				}
			}
		}
	}


}