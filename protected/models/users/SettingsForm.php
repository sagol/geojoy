<?php

namespace app\models\users;

/**
 * 
 */
class SettingsForm extends \CFormModel {


	/**
	 * 
	 * @var string 
	 */
	public $showPage = 0;
	public $showPageCount = 0;
	public $socialInfoVisible = 1;

	public $subscription;


	/**
	 * Правила валидации модели
	 * @return array 
	 */
	public function rules() {
		return array(
			array('showPage, showPageCount, subscription, socialInfoVisible', 'safe'),
		);
	}


	/**
	 * Labels для полей формы
	 * @return array 
	 */
	public function attributeLabels() {
		return array(
			'subscription' => \Yii::t('nav', 'FORM_SETTINGS_FIELD_SUBSCRIPTION'),
			'showPage' => \Yii::t('nav', 'FORM_SETTINGS_FIELD_SHOW_PAGE'),
			'showPageCount' => \Yii::t('nav', 'FORM_SETTINGS_FIELD_SHOW_PAGE_COUNT'),
			'socialInfoVisible' => \Yii::t('nav', 'FORM_SETTINGS_FIELD_SOCIAL_INFO_VISIBLE'),
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

	public function load() {
		$settings = \Yii::app()->user->getSetting();
		if(isset($settings[\app\models\users\User::SETTINGS_SHOW_PAGE]))
			$this->showPage = $settings[\app\models\users\User::SETTINGS_SHOW_PAGE];

		if(isset($settings[\app\models\users\User::SETTINGS_SHOW_PAGE_COUNT]))
			$this->showPageCount = $settings[\app\models\users\User::SETTINGS_SHOW_PAGE_COUNT];

		if(isset($settings[\app\models\users\User::SETTINGS_SOCIAL_INFO_VISIBLE]))
			$this->socialInfoVisible = $settings[\app\models\users\User::SETTINGS_SOCIAL_INFO_VISIBLE];

		$this->subscription = \app\models\users\User::subscription(\Yii::app()->user->id);
		$this->subscription = $this->subscription['sub'];


		return true;
	}

	public function save() {
		$subscription = \app\managers\User::fromArray($this->subscription);
		$settingsArray[\app\models\users\User::SETTINGS_SHOW_PAGE] = $this->showPage;
		$settingsArray[\app\models\users\User::SETTINGS_SHOW_PAGE_COUNT] = $this->showPageCount;
		$settingsArray[\app\models\users\User::SETTINGS_SOCIAL_INFO_VISIBLE] = $this->socialInfoVisible;
		$settings = \app\managers\User::fromArray($settingsArray);

		$sql = 'UPDATE users SET subscription = :subscription, settings = :settings 
			WHERE idusers = :id';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindValue(':id', \Yii::app()->user->id, \PDO::PARAM_INT);
		$command->bindParam(':subscription', $subscription, \PDO::PARAM_STR);
		$command->bindParam(':settings', $settings, \PDO::PARAM_STR);
		$rowCount = $command->execute();

		if($rowCount) \Yii::app()->user->setSetting($settingsArray);


		return $rowCount;
	}


}