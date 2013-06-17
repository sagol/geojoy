<?php

namespace app\models\fields\edit;

/**
 * Поле пароль
 */
class Pass  extends Field {

	protected $_name2;
	protected $_title2;
	protected $_value2;
	protected $_nameInForm2;

	protected $_ckeckOldPass = false;
	protected $_titleOld;
	protected $_nameOld;
	protected $_valueOld;
	protected $_nameInFormOld;


	/**
	 * Создание поля
	 * @param array $data
	 * @param \app\managers\Manager $manager 
	 */
	public function __construct($data, &$manager) {
		if(!empty($data['name2'])) $this->_name2 = $data['name2'];
		else $this->_name2 = $data['name'] . '2';
		if(!empty($data['title2'])) $this->_title2 = $data['title2'];
		if(!empty($data['required'])) $this->_rules[] = array($this->_name2, 'required');

		if(isset($data['ckeckOldPass'])) $this->_ckeckOldPass = $data['ckeckOldPass'];
		if($this->_ckeckOldPass) {
			if(!empty($data['nameOld'])) $this->_nameOld = $data['nameOld'];
			else $this->_nameOld = $data['name'] . 'Old';
			if(!empty($data['titleOld'])) $this->_titleOld = $data['titleOld'];
			if(!empty($data['required'])) $this->_rules[] = array($this->_nameOld, 'required');
			$this->_rules[] = array($this->_nameOld, 'authenticate');
		}

		parent::__construct($data, $manager);
	}


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::PASS;

		parent::init();

		// длина пароля не менее 6 символов
		$this->_rules[] = array("$this->_name, $this->_name2, $this->_nameOld", 'length', 'min' => 6, 'max' => 30);
		// пароль должен совпадать с повторным паролем
		$this->_rules[] = array($this->_name2, 'compare', 'compareAttribute' => $this->_name);

		if($this->_nameInForm2 === null) {
			if(@$this->initOptions['shotNameInForm']) {
				$this->_nameInForm2 = $this->_name2;
				$this->_nameInFormOld = $this->_nameOld;
			}
			else {
				$this->_nameInForm2 = 'app\models\fields[' . $this->_name2 . ']';
				$this->_nameInFormOld = 'app\models\fields[' . $this->_nameOld . ']';
			}
		}
	}


	/**
	 * Аутентификация пользователей
	 * @param string $attribute
	 * @param array $params 
	 */
	public function authenticate($attribute, $params) {
		if(!$this->hasErrors()) {
			$identity = new \app\components\UserIdentity(\Yii::app()->user->email, $this->_valueOld);
			if(!$identity->authenticate()) $this->addError($this->_nameOld, $identity->errorMessage);
		}
	}


	/**
	 * Получение свойств
	 * @param string $name
	 * @return mix 
	 */
	public function __get($name) {
		if($name == $this->_name) return $this->getValue();
		if($name == $this->_name2) return $this->getValue2();
		if($name == $this->_nameOld) return $this->getValueOld();
		if($name == $this->_name . '_html') {
			if(substr($this->getValue(), 0, 10) == '$6$rounds=') return '';
			else return $this->getValue();
		}


		return parent::__get($name);
	}


	public function getName2() {
		return $this->_name2;
	}


	public function getNameInForm2() {
		return $this->_nameInForm2;
	}


	public function getTitle2() {
		return $this->_title2;
	}


	public function getValue2() {
		return $this->_value2;
	}


	public function setValue2($value) {
		$this->_value2 = $value;
	}


	public function getCkeckOldPass() {
		return $this->_ckeckOldPass;
	}


	public function setCkeckOldPass($value) {
		$this->_ckeckOldPass = $value;
	}


	public function getNameOld() {
		return $this->_nameOld;
	}


	public function getNameInFormOld() {
		return $this->_nameInFormOld;
	}


	public function getTitleOld() {
		return $this->_titleOld;
	}


	public function getValueOld() {
		return $this->_valueOld;
	}


	public function setValueOld($value) {
		$this->_valueOld = $value;
	}


	public function getAttributeLabel($attribute) {
		if($attribute == $this->_name) return \Yii::t($this->_labelDictionary, $this->_title);
		if($attribute == $this->_name2) return \Yii::t($this->_labelDictionary, $this->_title2);
		if($attribute == $this->_nameOld) return \Yii::t($this->_labelDictionary, $this->_titleOld);

		return parent::getAttributeLabel($attribute);
	}


	public function setAttribute($name, $values) {
		$value = @$values[$name];
		$this->setValue($value);
		$value = @$values[$this->_name2];
		$this->setValue2($value);
		$value = @$values[$this->_nameOld];
		$this->setValueOld($value);
	}

	/**
	 * Упаковка значения поля
	 * @return string 
	 */
	public function packValue() {
		if(!$this->isSetFieldIndex()) $quotes = "'";
		else $quotes = '"';

		$value = $this->_cryptPass($this->_value);

		return $quotes . $value . $quotes;
	}


	/**
	 * Шифрование пароля
	 * @param string $pass
	 * @return string 
	 */
	protected function _cryptPass($pass) {
		$pass = crypt($pass, '$6$rounds=5000$' . \app\helpers\Main::randomString(16) . '$');
		// маскируем количество раундов
		$rounds = mt_rand(10000, 100000);


		return str_replace('$rounds=5000$', "\$rounds=$rounds$", $pass);
	}


	public function createCode($user, $email, $userName) {
		// удаляем коды подтверждения пароля данного пользователя
		\app\models\TableCode::deleteUser($user, \app\models\TableCode::RECOVERY_PASS);
		// создаем новый код
		$code = \app\models\TableCode::insert($user, \app\models\TableCode::RECOVERY_PASS);

		$userLink = \CHtml::link($user, array('/site/user/profile', 'id' => $user));
		if($code) {
			$mailer = \Yii::app()->mailer;
			$mailer->IsHTML(true);
			$mailer->AddAddress($email);
			$mailer->Subject = \Yii::t('mail', 'MAIL_RESET_PASSWORD_SUBJECT');
			$mailer->getView('resetPassword', array(
				'username' => $userName,
				'code' => $code,
			));
			if($mailer->Send()) \Yii::app()->appLog->mail('EMAIL_RESET_TITLE', 'EMAIL_RESET', array('{user}' => $userLink, '{email}' => $email));
			else {
				\Yii::app()->appLog->mail('EMAIL_RESET_TITLE', 'EMAIL_RESET_ERROR', array('{user}' => $userLink, '{email}' => $email, '{error}' => $mailer->ErrorInfo), \app\components\AppLog::TYPE_ERROR);
				$code = false;
			}
		}
		else \Yii::app()->appLog->user('EMAIL_RESET_TITLE', 'EMAIL_RESET_ERROR_TABLE', array('{user}' => $userLink), \app\components\AppLog::TYPE_ERROR);


		return $code;
	}


}