<?php

namespace app\modules\passField\models;

/**
 * Форма сброса пароля пользователя
 */
class ResetCodeForm extends \CFormModel {


	/**
	 * Код сброса пароля
	 * @var string  
	 */
	public $code;
	/**
	 * Пользователь привязаный к коду активации
	 * @var integer 
	 */
	public $user;


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
			'code' => \Yii::t('app\modules\passField\PassFieldModule.fields', 'FORM_RESET_CODE_PASS_FIELD_CODE'),
		);
	}


	public function checkCode($attribute, $params) {
		$user = \app\models\TableCode::selectUser($this->code, \app\models\TableCode::RECOVERY_PASS);

		if($user) $this->user = $user;
		else $this->addError($attribute, \Yii::t('app\modules\passField\PassFieldModule.fields', 'FORM_RESET_CODE_PASS_ERROR_CODE_NOT_EXIST'));
	}


	public function reset($model) {
		if($model->getManager()->fieldsValidate()) {
			// сохраняем поля
			$model->getManager()->fieldsUpdate();
			\Yii::app()->message->add($model->getManager()->fieldsInfo());

			// удаляем код после использования
			\app\models\TableCode::deleteUser($this->user, \app\models\TableCode::RECOVERY_PASS);

			return true;
		}
		else return false;
	}


}