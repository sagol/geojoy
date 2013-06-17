<?php

namespace app\models\users;

/**
 * Форма активации
 */
class ActivationForm extends \CFormModel {


	/**
	 * Код активации
	 * @var string 
	 */
	public $code;
	/**
	 * Пользователь привязаный к коду активации
	 * @var integer 
	 */
	private $_user;


	/**
	 * Правила валидации модели
	 * @return array 
	 */
	public function rules() {
		return array(
			// обязательные поля
			array('code', 'required'),

			// проверка кода
			array('code', 'checkCode'),
		);
	}


	/**
	 * Labels для полей формы
	 * @return array 
	 */
	public function attributeLabels() {
		return array(
			'code' => \Yii::t('nav', 'FORM_ACTIVATION_FIELD_CODE'),
		);
	}


	/**
	 * Проверка кода активации
	 * @param string $attribute
	 * @param array $params 
	 */
	public function checkCode($attribute, $params) {
		$user = \app\models\TableCode::selectUser($this->code, \app\models\TableCode::REGISTRATION);

		if($user) $this->_user = $user;
		else $this->addError($attribute, \Yii::t('nav', 'FORM_ACTIVATION_ERROR_CODE_NOT_EXIST'));
	}


	/**
	 * Активация
	 * @return integer 
	 */
	public function activation () {
		$sql = 'UPDATE users SET status = 1 
			WHERE idusers = :id';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':id', $this->_user, \PDO::PARAM_INT);
		$rowCount = $command->execute();

		if($rowCount) $rowCount = \app\models\TableCode::deleteUser($this->_user, \app\models\TableCode::REGISTRATION);

		return $rowCount;
	}


}