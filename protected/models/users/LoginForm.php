<?php

namespace app\models\users;

/**
 * Форма логина
 */
class LoginForm extends \CFormModel {


	/**
	 * Имя пользователя
	 * @var string 
	 */
	public $email;
	/**
	 * Пароль пользователя
	 * @var string 
	 */
	public $password;
	/**
	 * Запомнить
	 * @var string 
	 */
	public $rememberMe;
	/**
	 * Аутентификация пользователей по таблице пользователей
	 * @var \app\components\UserIdentity 
	 */
	private $_identity;
	private $_errorCode;


	/**
	 * Правила валидации модели
	 * @return array 
	 */
	public function rules() {
		return array(
			// email and password are required
			array('email, password', 'required'),
			// rememberMe needs to be a boolean
			array('rememberMe', 'boolean'),
			// password needs to be authenticated
			array('password', 'authenticate'),
		);
	}


	/**
	 * Labels для полей формы
	 * @return array 
	 */
	public function attributeLabels() {
		return array(
			'email' => \Yii::t('nav', 'FORM_LOGIN_FIELD_EMAIL'),
			'password' => \Yii::t('nav', 'FORM_LOGIN_FIELD_PASSWORD'),
			'rememberMe' => \Yii::t('nav', 'FORM_LOGIN_FIELD_REMEMBER_ME'),
		);
	}


	/**
	 * Аутентификация пользователей
	 * @param string $attribute
	 * @param array $params 
	 */
	public function authenticate($attribute, $params) {
		if(!$this->hasErrors()) {
			$this->_identity = new \app\components\UserIdentity($this->email, $this->password);
			if(!$this->_identity->authenticate()) {
				$this->_errorCode = $this->_identity->errorCode;
				if($this->_identity->errorCode != \app\components\UserIdentity::ERROR_EMAIL_INVALID) $this->addError('email', $this->_identity->errorMessage);
				elseif($this->_identity->errorCode == \app\components\UserIdentity::ERROR_PASSWORD_INVALID) $this->addError('password', $this->_identity->errorMessage);
				else $this->addError('', $this->_identity->errorMessage);
			}
		}
	}


	public function getErrorCode() {
		return $this->_errorCode;
	}


	/**
	 * Логин
	 * @return boolean 
	 */
	public function login() {
		$identity = &$this->_identity;
		if($identity === null) {
			$identity = new \app\components\UserIdentity($this->email, $this->password);
			$identity->authenticate();
		}

		if($identity->errorCode === \app\components\UserIdentity::ERROR_NONE) {
			// TODO: временной интервал перенести в параметры
			$duration = $this->rememberMe ? 3600*24*30 : 0; // 30 days
			\Yii::app()->user->login($this->_identity, $duration);

			$idUser = \Yii::app()->user->id;
			$email = \Yii::app()->user->email;
			// назначение роли
			\Yii::app()->appLog->user('USER_ENTER_TITLE', 'USER_ENTER', array('{user}' => \CHtml::link("$idUser($email)", array('/site/user/profile', 'id' => $idUser))));


			return true;
		}
		else {
			\Yii::app()->appLog->user('USER_ENTER_TITLE', 'USER_ENTER_ERROR', array('{email}' => $this->email, '{error}' => $this->_identity->errorMessage), \app\components\AppLog::TYPE_ERROR);
			return false;
		}
	}


	/**
	 * Логин через соцсеть
	 * @param \app\components\ServiceUserIdentity $identity
	 * @return boolean 
	 */
	public function loginSocial(& $identity) {
		$sql = 'SELECT u.idusers, u.multiuser, u.name, u.role, u.status, u.settings, u.email, u.main_language, s.idservices  
			FROM services s 
			LEFT JOIN users u ON s.idusers = u.idusers 
			WHERE id = :id AND service = :service AND NOT u.idusers IS NULL ';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindValue(':id', $identity->getState('__id'), \PDO::PARAM_INT);
		$command->bindValue(':service', $identity->getState('__service'), \PDO::PARAM_STR);
		$dataReader = $command->query();

		if(($data = $dataReader->read()) !== false) {
			if($data['status'] !== 1) {
				if($data['status'] == 0) {
					$this->addError('loginSocial', \Yii::t('nav', 'IDENTITY_USER_YOU_NOT_ACTIVATED'));
					$this->_errorCode = \app\components\UserIdentity::ERROR_YOU_NOT_ACTIVATED;
					\Yii::app()->session->add('userNotActivated', array(
						'id' => $data['idusers'],
						'email' => $data['email'],
						'name' => $data['name']
					));
				}
				elseif($data['status'] == 2) {
					$this->_errorCode = \app\components\UserIdentity::ERROR_YOU_BANNED;
					$this->addError('loginSocial', \Yii::t('nav', 'IDENTITY_USER_YOU_BANNED'));
				}

				return false;
			}
			$identity->setState('__id', $data['idusers']);
			$identity->setState('__email', $data['email']);
			$identity->setState('__name', $data['name']);
			$identity->setState('__role', $data['role']);
			$identity->setState('__setting', \app\managers\User::toArray($data['settings']));
			$identity->setState('__multiUser', $data['multiuser']);
			$identity->setState('__account', \app\models\users\User::ACCOUNT_SOCIAL);
			$identity->setState('__language', $data['main_language']);

			$friendsCount = $identity->getState('__friendsCount');
			$urlSocial = $identity->getState('__urlSocial');
			$socialInfo = $identity->getState('__socialInfo');

			if($friendsCount === false) $friendsCount = -1;
			$sql = 'UPDATE services SET friends_count = :count, url_social = :url, social_info = :info
				WHERE idservices = :id';
			$command = \Yii::app()->db->createCommand($sql);
			$command->bindValue(':id', $data['idservices'], \PDO::PARAM_INT);
			$command->bindValue(':count', $friendsCount, \PDO::PARAM_INT);
			$command->bindValue(':url', $urlSocial, \PDO::PARAM_STR);
			$command->bindValue(':info', serialize($socialInfo), \PDO::PARAM_LOB);
			$command->execute();


			return \Yii::app()->user->login($identity);
		}
		else return false;
	}


	/**
	 * Операции после валидации
	 */
	public function afterValidate() {
		if(!empty($this->_identity) && $this->_identity->errorCode != \app\components\UserIdentity::ERROR_NONE)
			\Yii::app()->appLog->user('USER_ENTER_TITLE', 'USER_ENTER_ERROR', array('{email}' => $this->email, '{error}' => $this->_identity->errorMessage), \app\components\AppLog::TYPE_ERROR);
		elseif($errors = $this->getErrors()) {
			foreach($errors as $error)
				$msg[] = $error[0];
			\Yii::app()->appLog->user('USER_ENTER_TITLE', 'USER_ENTER_ERROR', array('{email}' => $this->email, '{error}' => implode(',', $msg)), \app\components\AppLog::TYPE_ERROR);
		}
	}


}