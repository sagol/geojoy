<?php

namespace app\models\fields\edit;


/**
 * Поле email
 */
class Email extends Field {


	/**
	 * Старый email
	 * @var string 
	 */
	protected $_oldEmail;

	/**
	 * Пропуск отправки email`а для его подтверждения
	 * @var boolean 
	 */
	protected $_sendConfirmationEmail = false;

	/**
	 * Пропуск подтверждения email`а
	 * @var boolean 
	 */
	protected $_skipConfirmationEmail = false;

	/**
	 * Пропуск проверки уникальности email`а
	 * @var boolean 
	 */
	protected $_skipUniqueEmail = false;


	/**
	 * Создание поля
	 * @param array $data
	 * @param \app\managers\Manager $manager 
	 */
	public function __construct($data, &$manager) {
		if(!empty($data['skipConfirmation']))
			$this->_skipConfirmationEmail = $data['skipConfirmation'];
		if(!empty($data['skipUniqueEmail']))
			$this->_skipUniqueEmail = $data['skipUniqueEmail'];

		parent::__construct($data, $manager);
	}


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::EMAIL;

		parent::init();

		// проверка на соответствие типу
		$this->_rules[] = array($this->_name, 'email');
		// проверка на длину, пределах от 6 до 50 символов
		$this->_rules[] = array($this->_name, 'length', 'min' => 6, 'max' => 50);
		// проверка на уникальность
		if(!$this->_skipUniqueEmail)
			$this->_rules[] = array($this->_name, 'uniqueEmail');
		// проверка на нижний регистр
		$this->_rules[] = array($this->_name, 'filter', 'filter' => 'mb_strtolower');
	}


	/**
	 * Проверка уникальности Email`а
	 * @param string $attribute
	 * @param array $params 
	 */
	public function uniqueEmail($attribute, $params) {
		$users = array();
		$curUserId = (int) \Yii::app()->user->id;

		$email = $this->getValue();
		$sql = 'SELECT * 
			FROM ' . $this->_table . ' ' .
			"WHERE idusers = :user OR $this->_field = :email OR $this->_field LIKE :email1 OR $this->_field LIKE :email2";
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindValue(':email', $email, \PDO::PARAM_STR);
		$command->bindValue(':email1', "%;$email;%", \PDO::PARAM_STR);
		$command->bindValue(':email2', "%]$email;%", \PDO::PARAM_STR);
		$command->bindParam(':user', $curUserId, \PDO::PARAM_INT);
		$dataReader = $command->query();

		while(($data = $dataReader->read()) !== false)
			$users[$data['idusers']] = $data;

		$count = count($users);
		if($count > 1 || ($curUserId == 0 && $count == 1)) {
			$this->addError($attribute, \Yii::t('app\modules\emailField\EmailFieldModule.fields', 'FIELDS_EMAIL_ERROR_EMAIL_EXIST'));
			return;
		}

		if($count == 1) {
			// распаковываем полученные из базы старый ($old) и текущий email ($value)
			// старый ($old) - подтвержденный email, его наличие означает, что пользователь вводил уже email для смены ($value) и еще не подтвердил его.
			// текущий email ($value) - при отсутствии старого ($old) является действтельным и подтвержденным пользователем.
			list($old, $value) = $this->_unPackOldValue($users[$curUserId][$this->_field]);

			// старого нету
			if($old === null) {
				// смена текущего email`а ($value) на новый ($this->getValue()) введенный пользователем
				if($value != $this->getValue()) {
					$this->_oldEmail = $value;
					if(!$this->_skipConfirmationEmail)
						$this->_info = \Yii::t('app\modules\emailField\EmailFieldModule.fields', 'FIELDS_EMAIL_INFO_CONFIRM_EMAIL');
					$this->_sendConfirmationEmail = !$this->_skipConfirmationEmail;
				}
			}
			// старый есть, уже была попытка смены email`а
			else {
				// старый и введенный пользователем равны, т.е. пользователь отменяет смену email`а, тут подтверждение не нужно
				if($old == $this->getValue())
					$this->_oldEmail = null;
				// если $value == $this->getValue(), то подтверждение тоже не нужно, пользователь еще не подтвердил email, а равны, т.к. он меняет другую информацию
				// пользователь не подтвердил email ($value) и решил ввести другой.
				elseif($value != $this->getValue()) {
					$this->_info = \Yii::t('app\modules\emailField\EmailFieldModule.fields', 'FIELDS_EMAIL_INFO_CONFIRM_EMAIL');
					$this->_sendConfirmationEmail = !$this->_skipConfirmationEmail;
				}
			}
		}
	}


	/**
	 * Упаковка значения поля
	 * @return string 
	 */
	public function packValue() {
		if(!$this->isSetFieldIndex())
			$quotes = "'";
		else
			$quotes = '"';

		if($this->_oldEmail != null)
			return $quotes . 'old:[' . date('Y-m-d H:i:s') . "]$this->_oldEmail;$this->_value;" . $quotes;

		return $quotes . $this->_value . $quotes;
	}


	/**
	 * Распаковка значения поля
	 * @param type string
	 * @return boolean 
	 */
	public function unPackValue($value) {
		$sql = $this->sqlSelect();
		$value = $value[$sql[0]['field']];
		if($this->isSetFieldIndex()) {
			if(isset($value[$this->_fieldIndex]))
				$value = $value[$this->_fieldIndex];
			else
				return false;
		}

		if(substr($value, 0, 4) == 'old:') {
			$f = strpos($value, ']') + 1;
			$date = new \DateTime(substr($value, 5, $f - 6));
			$curDate = new \DateTime();
			$interval = $date->diff($curDate);
			$f1 = strpos($value, ';');
			if($interval->days == 0) {
				$this->_oldEmail = substr($value, $f, $f1 - $f);
				$value = substr($value, $f1 + 1, -1);
			}
			else
				$value = substr($value, $f, $f1 - $f);
		}
		$this->_value = $value;


		return true;
	}


	/**
	 * Распаковка старого и текущего email`а
	 * @param string $value
	 * @return array(oldEmail, curEmail) 
	 */
	protected function _unPackOldValue($value) {
		$oldEmail = null;
		if(substr($value, 0, 4) == 'old:') {
			$f = strpos($value, ']') + 1;
			$date = new \DateTime(substr($value, 5, $f - 6));
			$curDate = new \DateTime();
			$interval = $date->diff($curDate);
			$f1 = strpos($value, ';');
			if($interval->days == 0) {
				$oldEmail = substr($value, $f, $f1 - $f);
				$value = substr($value, $f1 + 1, -1);
			}
			else
				$value = substr($value, $f, $f1 - $f);
		}


		return array($oldEmail, $value);
	}


	/**
	 * Событие выполняется после сохранения объявления (update)
	 * @return boolean 
	 */
	public function afterUpdate() {
		if($this->_sendConfirmationEmail && !empty($this->_oldEmail)) {
			$curUserId = \Yii::app()->user->id;
			// удаляем коды подтверждения email`a данного пользователя
			\app\models\TableCode::deleteUser($curUserId, \app\models\TableCode::CONFIRMATION_EMAIL);
			// создаем новый код
			$code = \app\models\TableCode::insert($curUserId, \app\models\TableCode::CONFIRMATION_EMAIL, $this->_manager);

			$user = \CHtml::link($curUserId, array('/site/user/profile', 'id' => $curUserId));
			if($code) {
				$mailer = \Yii::app()->mailer;
				$mailer->IsHTML(true);
				$mailer->AddAddress($this->getValue());
				$mailer->Subject = \Yii::t('mail', 'MAIL_CONFIRMATION_SUBJECT');
				$mailer->getView('confirmationEmail', array(
					'username' => \app\models\users\User::name($curUserId),
					'code' => $code,
				));

				if($mailer->Send())
					\Yii::app()->appLog->mail('EMAIL_VALIDATE_TITLE', 'EMAIL_VALIDATE', array('{user}' => $user, '{email}' => $this->getValue()));
				else {
					\Yii::app()->appLog->mail('EMAIL_VALIDATE_TITLE', 'EMAIL_VALIDATE_ERROR', array('{user}' => $user, '{email}' => $this->getValue(), '{error}' => $mailer->ErrorInfo), \app\components\AppLog::TYPE_ERROR);
					$code = false;
				}
			}
			else
				\Yii::app()->appLog->user('EMAIL_VALIDATE_TITLE', 'EMAIL_VALIDATE_ERROR_TABLE', array('{user}' => $user), \app\components\AppLog::TYPE_ERROR);


			return $code;
		}
	}


	/**
	 * Подтверждение email`а
	 * @return boolean 
	 */
	public function confirmation() {
		if(!empty($this->_value)) {
			$this->_oldEmail = null;
			return true;
		}


		return false;
	}


}
