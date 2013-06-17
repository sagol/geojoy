<?php

namespace app\models\users;

/**
 * Форма восстановления пароля
 */
class RecoveryPasswordForm extends \CFormModel {


	/**
	 * Email
	 * @var string 
	 */
	public $email;


	/**
	 * Правила валидации модели
	 * @return array 
	 */
	public function rules() {
		return array(
			array('email', 'checkFields'),
		);
	}


	/**
	 * Labels для полей формы
	 * @return array 
	 */
	public function attributeLabels() {
		return array(
			'email' => \Yii::t('nav', 'FORM_RECOVERY_PASSWORD_FIELD_EMAIL'),
		);
	}


	/**
	 * Проверка полей
	 * @param string $attribute
	 * @param array $params 
	 */
	public function checkFields($attribute, $params) {
		if(empty($this->email))
			$this->addError($attribute, \Yii::t('nav', 'FORM_RECOVERY_PASSWORD_EMPTY_FIELDS'));

	}

	/**
	 * Восстановление пароля: создание кода восстановления и отправка письма
	 * @return boolean 
	 */
	public function recovery() {
		$sql = 'SELECT u.idusers, u.email, u.name
			FROM users u 
			LEFT JOIN services s ON u.idusers = s.idusers 
			WHERE s.idusers IS NULL AND (email = :email OR email LIKE :email1) 
			LIMIT 1';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindValue(':email', $this->email, \PDO::PARAM_STR);
		$command->bindValue(':email1', "%]$this->email;%", \PDO::PARAM_STR);
		$dataReader = $command->query();

		if(($data = $dataReader->read()) !== false) {
			$model = new \app\models\users\User;
			$find = $model->user(0, 'passRecovery');
			$field = $model->field(\app\models\users\User::NAME_PASS);

			$this->email = $data['email'];
			return $field->createCode($data['idusers'], $this->email, $data['name']);
		}

		\Yii::app()->appLog->user('PASSWORD_RECOVERY_TITLE', 'PASSWORD_RECOVERY', array('{email}' => $this->email), \app\components\AppLog::TYPE_ERROR);


		return false;
	}


}