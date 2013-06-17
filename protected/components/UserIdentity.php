<?php

namespace app\components;

/**
 * Аутентификация пользователей по таблице пользователей
 */
class UserIdentity extends \CUserIdentity {


	/**
	 * Не правильный email
	 */
	const ERROR_EMAIL_INVALID = 3;
	/**
	 * Пользователь не активен
	 */
	const ERROR_YOU_NOT_ACTIVATED = 10;
	/**
	 * Пользователь забанен
	 */
	const ERROR_YOU_BANNED = 11;
	/**
	 * @var string username
	 */
	public $email;


	/**
	 * Constructor.
	 * @param string $username username
	 * @param string $password password
	 */
	public function __construct($email, $password) {
		$this->email = $email;
		$this->password = $password;
	}


	/**
	 * Аутентификация
	 * @return boolean
	 * @throws \Exception сервер не поддерживает необходимую безопасность
	 */
	public function authenticate() {
		// TODO: Добавить в перевод
		if(CRYPT_SHA512 != 1) throw new \Exception('Hashing mechanism not supported.');

		$sql = 'SELECT u.* 
			FROM users u 
			LEFT JOIN services s ON u.idusers = s.idusers 
			WHERE s.idusers IS NULL AND (u.email = :email OR u.email LIKE :email1) 
			LIMIT 1';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindValue(':email', $this->email, \PDO::PARAM_STR);
		$command->bindValue(':email1', "%]$this->email;%", \PDO::PARAM_STR);
		$dataReader = $command->query();

		if(($data = $dataReader->read()) !== false) {
			if($data['status'] === 2) $this->errorCode = self::ERROR_YOU_BANNED;
			else {
				$data['pass'] = '$6$rounds=5000$' . substr($data['pass'], strpos($data['pass'], '$', 10)+1);
				if(crypt($this->password, $data['pass']) === $data['pass']) {
					if($data['status'] === 0) {
						$this->errorCode = self::ERROR_YOU_NOT_ACTIVATED;
						\Yii::app()->session->add('userNotActivated', array(
							'id' => $data['idusers'],
							'email' => $data['email'],
							'name' => $data['name']
						));
					}
					else {
						$this->errorCode = self::ERROR_NONE;
						$this->setState('__id', $data['idusers']);
						$this->setState('__email', $data['email']);
						$this->setState('__name', $data['name']);
						$this->setState('__role', $data['role']);
						$this->setState('__setting', \app\managers\Object::toArray($data['settings']));
						$this->setState('__multiUser', $data['multiuser']);
						$this->setState('__account', \app\models\users\User::ACCOUNT_DEFAULT);
						$this->setState('__language', $data['main_language']);
					}
				}
				else $this->errorCode = self::ERROR_PASSWORD_INVALID;
			}
		}
		else $this->errorCode = self::ERROR_EMAIL_INVALID;

		if($this->errorCode) {
			$errorMessage = array(
				2 => \Yii::t('nav', 'IDENTITY_USER_INCORRECT_PASSWORD'),
				3 => \Yii::t('nav', 'IDENTITY_USER_INCORRECT_EMAIL'),
				10 => \Yii::t('nav', 'IDENTITY_USER_YOU_NOT_ACTIVATED'),
				11 => \Yii::t('nav', 'IDENTITY_USER_YOU_BANNED'),
			);
			
			
			$this->errorMessage = $errorMessage[$this->errorCode];
		}


		return !$this->errorCode;
	}


	/**
	 * Возвращает id пользователя
	 * Перекрываем функцию ролителя CBaseUserIdentity
	 * @return integer 
	 */
	public function getId() {
		return $this->getState('__id');
	}


	/**
	 * Устанавливает id пользователя
	 * Перекрываем функцию ролителя CBaseUserIdentity
	 * @param integer $value
	 * @return integer 
	 */
	public function setId($value) {
		return $this->setState('__id', $value);
	}


}