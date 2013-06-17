<?php

namespace app\models\users;

/**
 * Создание мульти акка
 */
class MultiAccount extends \CModel {


	/**
	 * Код для создания мульти акка
	 * @var string 
	 */
	public $code;


	/**
	 * Правила валидации модели
	 * @return array 
	 */
	public function rules() {
		return array(
			array('code', 'checkCode'),
			array('code', 'length', 'max' => 32),
		);
	}


	/**
	 * Проверка кода
	 * @param string $attribute
	 * @param array $params 
	 */
	public function checkCode($attribute, $params) {
		$user = \app\models\TableCode::selectUser($this->code, \app\models\TableCode::MULTI_ACCOUNT);

		if($user === false)
			$this->addError($attribute, \Yii::t('nav', 'FORM_MULTI_ACCOUNT_ERROR_CODE_NOT_EXIST'));
		elseif($user == \Yii::app()->user->id)
			$this->addError($attribute, \Yii::t('nav', 'FORM_MULTI_ACCOUNT_ERROR_CODE_SELF'));
	}


	/**
	 * Labels для полей формы
	 * @return array 
	 */
	public function attributeLabels() {
		return array(
			'code' => \Yii::t('fields', 'USER_CODE'),
			'createCode' => \Yii::t('fields', 'USER_CREATE_CODE'),
		);
	}


	/**
	 * Заглушка для CModel
	 */
	public function attributeNames() {
	}


	/**
	 * Все аккаунты пользователя присоединеные кодом
	 * @return array 
	 */
	public function userAccounts() {
		$accounts = array();
		$user = \Yii::app()->user;
		if($user->checkAccess('company')) {
			$sql = 'SELECT idusers, multiuser, role, profile, status, name 
				FROM users 
				WHERE multiuser = :multiuser';
			$command = \Yii::app()->db->createCommand($sql);
			$command->bindValue(':multiuser', $user->multiUser, \PDO::PARAM_INT);
		}
		else {
			$sql = 'SELECT idusers, multiuser, role, profile, status, name 
				FROM users 
				WHERE idusers IN (:multiuser, :id) OR multiuser IN (:multiuser, :id)';
			$command = \Yii::app()->db->createCommand($sql);
			$command->bindValue(':multiuser', $user->multiUser, \PDO::PARAM_INT);
			$command->bindValue(':id', $user->id, \PDO::PARAM_INT);
		}
		$dataReader = $command->query();

		$accounts[$user->multiUser] = array();
		while(($data = $dataReader->read()) !== false)
			$accounts[$data['idusers']] = $data;


		return $accounts;
	}


	/**
	 * Все аккаунты пользователя присоединеные кодом
	 * @return array 
	 */
	public static function userSocialAccounts() {
		$accounts = array();
		$user = \Yii::app()->user;
		if(!$user->checkAccess('company')) {
			$sql = 'SELECT u.idusers, multiuser, role, profile, status, name, s.service, s.social_info 
				FROM users u
				LEFT JOIN services s USING(idusers)
				WHERE (u.idusers IN (:multiuser, :id) OR multiuser IN (:multiuser, :id)) AND NOT s.idusers IS NULL AND u.status = 1';
			$command = \Yii::app()->db->createCommand($sql);
			$command->bindValue(':multiuser', $user->multiUser, \PDO::PARAM_INT);
			$command->bindValue(':id', $user->id, \PDO::PARAM_INT);
		}
		$dataReader = $command->query();

		while(($data = $dataReader->read()) !== false) {
			if(!empty($data['social_info'])) $data['social_info'] = unserialize(stream_get_contents($data['social_info']));
			else continue;

			$accounts[$data['idusers']] = $data;
		}


		return $accounts;
	}


	public function withdraw($id) {
		$withdraw = new \app\models\users\User;
		$withdraw->user($id, '');

		$user = & \Yii::app()->user;
		if(!$user->checkAccess('multiAccountWithdraw', array('curUser' => $user, 'multiUser' => $withdraw->multiUser, 'idusers' => $id))) return false;

		if($user->id == $id) {
			if($user->checkAccess('company')) return false;
			$user->setMultiUser($id);
			$user->setRole(\app\models\users\User::ROLE_USER);

		}

		// сброс мульти акка у пользователя
		$sql = 'UPDATE users SET multiuser = :id, role = :role 
			WHERE idusers = :id';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':id', $id, \PDO::PARAM_INT);
		$command->bindValue(':role', $user->getRole(), \PDO::PARAM_INT);
		$rowCount = $command->execute();

		if($rowCount) {
			// сброс мульти акка у объявлений пользователя
			$sql = 'UPDATE objects SET multiuser = :id 
				WHERE idusers = :id';
			$command = \Yii::app()->db->createCommand($sql);
			$command->bindParam(':id', $id, \PDO::PARAM_INT);
			$command->execute();

			// сброс мульти акка у подписок пользователя
			$sql = 'UPDATE obj_bookmarks SET multiuser = :id WHERE idusers = :id';
			$command = \Yii::app()->db->createCommand($sql);
			$command->bindParam(':id', $id, \PDO::PARAM_INT);
			$command->execute();
		}

		return true;
	}


	/**
	 * Код создания мульти акка для этого пользователя
	 * @return string 
	 */
	public function multiAccountCode() {
		return \app\models\TableCode::selectCode(\Yii::app()->user->id, \app\models\TableCode::MULTI_ACCOUNT);
	}


	/**
	 * Создание кода мульти акка
	 * @return string|boolean 
	 */
	public static function multiAccountCreateCode() {
		return \app\models\TableCode::insert(\Yii::app()->user->id, \app\models\TableCode::MULTI_ACCOUNT);
	}


	/**
	 * Создание мульти акка
	 * @return boolean 
	 */
	public function createMultiAccount() {
		$multiUserNew = \app\models\TableCode::selectUser($this->code, \app\models\TableCode::MULTI_ACCOUNT);

		if(!$multiUserNew) return false;

		$mainUser = new \app\models\users\User;
		$mainUser->user($multiUserNew, '');

		if($mainUser->getProfile() == \app\models\users\User::PROFILE_COMPANY &&
			\Yii::app()->user->getRole() == \app\models\users\User::ROLE_COMPANY)
				return false;

		$multiUserCur = \Yii::app()->user->multiUser;
		if(\Yii::app()->user->getRole() == \app\models\users\User::ROLE_COMPANY_USER) {
			\Yii::app()->user->setMultiUser($multiUserNew);
			if($mainUser->getProfile() == \app\models\users\User::PROFILE_USER)
				\Yii::app()->user->setRole(\app\models\users\User::PROFILE_USER);

			// присвоение нового мульти акка текущему пользователю
			$sql = 'UPDATE users SET multiuser = :new, role = :role 
				WHERE idusers = :id';
			$command = \Yii::app()->db->createCommand($sql);
			$command->bindParam(':new', $multiUserNew, \PDO::PARAM_INT);
			$command->bindParam(':id', \Yii::app()->user->id, \PDO::PARAM_INT);
			$command->bindParam(':role', \Yii::app()->user->getRole(), \PDO::PARAM_INT);
			$rowCount = $command->execute();

			if($rowCount) {
				// присвоение нового мульти акка объявлениям текущего мульти акка текущего пользователя
				$sql = 'UPDATE objects SET multiuser = :new 
					WHERE multiuser = :cur';
				$command = \Yii::app()->db->createCommand($sql);
				$command->bindParam(':new', $multiUserNew, \PDO::PARAM_INT);
				$command->bindParam(':cur', $multiUserCur, \PDO::PARAM_INT);
				$command->execute();

				if($mainUser->getProfile() == \app\models\users\User::PROFILE_USER) {
					// присвоение нового мульти акка подпискам текущего мульти акка текущего пользователя
					$sql = 'UPDATE obj_bookmarks SET multiuser = :new WHERE multiuser = :cur';
					$command = \Yii::app()->db->createCommand($sql);
					$command->bindParam(':new', $multiUserNew, \PDO::PARAM_INT);
					$command->bindParam(':cur', $multiUserCur, \PDO::PARAM_INT);
					$command->execute();
				}
			}
		}
		else {
			\Yii::app()->user->setMultiUser($multiUserNew);
			if($mainUser->getProfile() == \app\models\users\User::PROFILE_COMPANY)
				\Yii::app()->user->setRole(\app\models\users\User::ROLE_COMPANY_USER);

			// присвоение нового мульти акка пользователям текущего мульти акка текущего пользователя
			$sql = 'UPDATE users SET multiuser = :new, role = :role 
				WHERE multiuser = :cur';
			$command = \Yii::app()->db->createCommand($sql);
			$command->bindParam(':new', $multiUserNew, \PDO::PARAM_INT);
			$command->bindParam(':cur', $multiUserCur, \PDO::PARAM_INT);
			$command->bindValue(':role', \Yii::app()->user->getRole(), \PDO::PARAM_INT);
			$rowCount = $command->execute();

			if($rowCount) {
				// присвоение нового мульти акка объявлениям текущего мульти акка текущего пользователя
				$sql = 'UPDATE objects SET multiuser = :new 
					WHERE multiuser = :cur';
				$command = \Yii::app()->db->createCommand($sql);
				$command->bindParam(':new', $multiUserNew, \PDO::PARAM_INT);
				$command->bindParam(':cur', $multiUserCur, \PDO::PARAM_INT);
				$command->execute();

				if($mainUser->getProfile() == \app\models\users\User::PROFILE_USER) {
					// присвоение нового мульти акка подпискам текущего мульти акка текущего пользователя
					$sql = 'UPDATE obj_bookmarks SET multiuser = :new WHERE multiuser = :cur';
					$command = \Yii::app()->db->createCommand($sql);
					$command->bindParam(':new', $multiUserNew, \PDO::PARAM_INT);
					$command->bindParam(':cur', $multiUserCur, \PDO::PARAM_INT);
					$command->execute();
				}
			}
		}


		if($rowCount) return true;


		return false;
	}


}