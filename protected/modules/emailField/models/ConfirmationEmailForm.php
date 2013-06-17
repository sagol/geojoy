<?php

namespace app\modules\emailField\models;

/**
 * Форма подтверждения email`a
 */
class ConfirmationEmailForm extends \CFormModel {


	/**
	 * Код подтверждения
	 * @var string 
	 */
	public $code;
	/**
	 * Пользователь привязаный к коду подтверждения
	 * @var integer 
	 */
	private $_user;
	/**
	 * Объект с данными для сохранения подтвержденого email`a
	 * @var \app\models\fields\Email
	 */
	private $_manager;


	/**
	 * Правила валидации модели
	 * @return array 
	 */
	public function rules() {
		return array(
			// обязательное поле
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
			'code' => \Yii::t('app\modules\emailField\EmailFieldModule.fields', 'FORM_CONFIRMATION_EMAIL_FIELD_CODE'),
		);
	}


	/**
	 * Проверка кода подтверждения
	 * @param string $attribute
	 * @param array $params 
	 */
	public function checkCode($attribute, $params) {
		$data = \app\models\TableCode::select($this->code, null, \app\models\TableCode::CONFIRMATION_EMAIL);

		if($data !== false) {
			$this->_user = $data['idusers'];
			$this->_manager = $data['info'];
			$this->confirmation();
		}
		else $this->addError($attribute, \Yii::t('app\modules\emailField\EmailFieldModule.fields', 'FORM_CONFIRMATION_EMAIL_ERROR_CODE_NOT_EXIST'));
	}


	/**
	 * Подтверждение
	 * @return integer 
	 */
	public function confirmation () {
		$rowCount = $this->_manager->fieldsUpdate();
		\Yii::app()->message->add($this->_manager->fieldsInfo());

		if($rowCount) \app\models\TableCode::deleteUser($this->_user, \app\models\TableCode::CONFIRMATION_EMAIL);


		return $rowCount;
	}


}