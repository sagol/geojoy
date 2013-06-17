<?php

namespace app\models\users;

/**
 * Пользователи
 */
class Users extends \CActiveRecord {


	/**
	 * Статусы
	 * @var array 
	 */
	private static $_status = array();
	/**
	 * Профили
	 * @var array 
	 */
	private static $_profile = array();
	/**
	 * Роли
	 * @var array 
	 */
	private static $_role = array();

	/**
	 * Роль до изменения
	 * @var integer 
	 */
	private $_oldRole = null;
	/**
	 * Статус до изменения
	 * @var integer 
	 */
	private $_oldStatus = null;


	/**
	 * Создание AR модели
	 * @param string $className
	 * @return \app\models\users\Users 
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}


	/**
	 * Таблица базы, с которой работает AR модель
	 * @return string 
	 */
	public function tableName() {
		return 'users';
	}


	/**
	 * Правила валидации модели
	 * @return array 
	 */
	public function rules() {
		return array(
			// анулировано login обязательное поле, и наче при запросе восстановления пароля с пустым именем, вернет другого пользователя, который тоже с пустым именем
			array('profile, email, status, role', 'required'),
			array('profile, status, role, karma', 'numerical', 'integerOnly' => true),
			array('role', 'checkRole'),
			array('email', 'unique'),

			// почта проверяется на соответствие типу
			array('email', 'email'),
			// почта должна быть в пределах от 6 до 50 символов
			array('email', 'length', 'min' => 6, 'max' => 50),
			// почта должна быть написана в нижнем регистре
			array('email', 'filter', 'filter' => 'mb_strtolower'),

			array('name', 'safe'),
			array('idusers, profile, status, date, email, name', 'safe', 'on' => 'search'),
		);
	}


	/**
	 * Проверка роли (запрет задания пользователю роли больше роли авторизированного пользователя)
	 * @param string $attribute
	 * @param array $params 
	 */
	public function checkRole($attribute, $params) {
		if(\Yii::app()->user->role < $this->role) {
			$this->addError($attribute, \Yii::t('main', 'ERROR_NOT_PERMISSION_SET_ROLE'));
		}
	}


	/**
	 * Labels для полей формы
	 * @return array 
	 */
	public function attributeLabels() {
		return array(
			'idusers' => 'ID',
			'profile' => \Yii::t('admin', 'USERS_FIELD_PROFILE'),
			'status' => \Yii::t('admin', 'USERS_FIELD_STATUS'),
			'date' => \Yii::t('admin', 'USERS_FIELD_DATE'),
			'email' => \Yii::t('admin', 'USERS_FIELD_EMAIL'),
			'name' => \Yii::t('admin', 'USERS_FIELD_NAME'),
			'role' => \Yii::t('admin', 'USERS_FIELD_ROLE'),
			'karma' => \Yii::t('admin', 'USERS_FIELD_KARMA'),
		);
	}


	/**
	 * Провайдер для AR модели
	 * @return \CActiveDataProvider 
	 */
	public function search() {
		// обработка значений числовых полей (для фильтра)
		if(!is_numeric($this->idusers)) $this->idusers = '';
		if(!is_numeric($this->profile)) $this->profile = '';
		if(!is_numeric($this->status)) $this->status = '';
		if(!is_numeric($this->role)) $this->role = '';

		$criteria = new \CDbCriteria;

		$criteria->compare('idusers', $this->idusers);
		$criteria->compare('profile', $this->profile);
		$criteria->compare('status', $this->status);
		$criteria->compare('date', $this->date, true);
		$criteria->compare('email', $this->email, true);
		$criteria->compare('name', $this->name, true);

		return new \CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'pagination' => array(
				'pageSize' => 50,
			),
		));
	}


	/**
	 * Получение значений
	 * @param string $name
	 * @return mixed 
	 */
	public function __get($name) {
		if($name == 'status_val') return $this->status(false, parent::__get('status'), false);
		elseif($name == 'status_arr') return $this->status();
		elseif($name == 'status_arr1') return $this->status(true, null, false);

		elseif($name == 'profile_val') return $this->profile(false, parent::__get('profile'), false);
		elseif($name == 'profile_arr') return $this->profile();
		elseif($name == 'profile_arr1') return $this->profile(true, null, false);

		elseif($name == 'role_val') return $this->role(false, parent::__get('role'), false);
		elseif($name == 'role_arr') return $this->role();
		elseif($name == 'role_arr1') return $this->role(true, null, false);

		else return parent::__get($name);
	}


	/**
	 * Задание значений
	 * @param string $name
	 * @param mixed $value
	 * @return mixed 
	 */
	public function __set($name, $value) {
		// сохранение свойств до изменения
		if($name == 'role' && $this->_oldRole === null) $this->_oldRole = $this->role;
		elseif($name == 'status' && $this->_oldStatus === null) $this->_oldStatus = $this->status;


		return parent::__set($name, $value);
	}


	/**
	 * Статусы (получение массива статусов пользователей)
	 * @param boolean $arr
	 * @param integer $id
	 * @param boolean $all
	 * @return mixed 
	 */
	public function status($arr = true, $id = null, $all = true) {
		if(empty(self::$_status))
			self::$_status = \app\models\users\User::status();

		if($all) {
			if($arr) return array_merge(array('' => \Yii::t('nav', 'ALL')), self::$_status);

			$status = array_merge(array('' => \Yii::t('nav', 'ALL')), self::$_status);
			return @$status[$id];
		}
		else {
			if($arr) return self::$_status;
			return @self::$_status[$id];
		}
	}


	/**
	 * Профили (получение массива профилей пользователей)
	 * @param boolean $arr
	 * @param integer $id
	 * @param boolean $all
	 * @return mixed 
	 */
	public function profile($arr = true, $id = null, $all = true) {
		if(empty(self::$_profile))
			self::$_profile = \app\models\users\User::profiles();

		if($all) {
			if($arr) return array_merge(array('' => \Yii::t('nav', 'ALL')), self::$_profile);

			$profile = array_merge(array('' => \Yii::t('nav', 'ALL')), self::$_profile);
			return @$profile[$id];
		}
		else {
			if($arr) return self::$_profile;
			return @self::$_profile[$id];
		}
	}


	/**
	 * Роли (получение массива ролей пользователей)
	 * @param boolean $arr
	 * @param integer $id
	 * @param boolean $all
	 * @return mixed 
	 */
	public function role($arr = true, $id = null, $all = true) {
		if(empty(self::$_role))
			self::$_role = \app\models\users\User::role();

		if($all) {
			if($arr) return array_merge(array('' => \Yii::t('nav', 'ALL')), self::$_role);

			$role = array_merge(array('' => \Yii::t('nav', 'ALL')), self::$_role);
			return @$role[$id];
		}
		else {
			if($arr) return self::$_role;
			return @self::$_role[$id];
		}
	}


	/**
	 * Операции перед сохранением (запрет изменения пользователей с ролью больше роли авторизированного пользователя)
	 * @return boolean
	 * @throws \CHttpException 
	 */
	public function beforeSave() {
		if(\Yii::app()->user->role < $this->_oldRole)
			throw new \CHttpException(403, \Yii::t('main', 'ERROR_NOT_PERMISSION_FOR_EDIT_USER'));


		return true;
	}


	/**
	 * Операции после сохранения
	 * @return boolean 
	 */
	public function afterSave() {
		if($this->isNewRecord)
			\Yii::app()->appLog->user('USER_CREATE_TITLE', 'USER_CREATE', array('{user}' => \CHtml::link($this->idusers, array('/site/user/profile', 'id' => $this->idusers))));

		if($this->_oldStatus != $this->status) {
			$oldStatus = $this->status(false, $this->_oldStatus, false);
			$curStatus = $this->status(false, $this->status, false);
			\Yii::app()->appLog->user('USER_STATUS_EDIT_TITLE', 'USER_STATUS_EDIT', array('{user}' => \CHtml::link("$this->idusers($this->email)", array('/site/user/profile', 'id' => $this->idusers)), '{oldStatus}' => $oldStatus, '{status}' => $curStatus));
		}


		return true;
	}


	/**
	 * Операции перед удалением (запрет удаления пользователей с ролью больше роли авторизированного пользователя)
	 * @return boolean
	 * @throws \CHttpException 
	 */
	public function beforeDelete() {
		if(\Yii::app()->user->role < $this->role)
			throw new \CHttpException(403, \Yii::t('main', 'ERROR_NOT_PERMISSION_FOR_EDIT_USER'));

		$user = new \app\models\users\User;
		$user->delete($this->idusers);

		// false т.к. удаление пользователя выполняется в модели \app\models\users\User
		return false;
	}


}
