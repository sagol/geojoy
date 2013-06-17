<?php

namespace app\models\users;

/**
 * Пользователь
 */
class User extends \app\components\ModelFields {


	const NAME_ID = 'idusers';
	const NAME_NAME = 'name';
	const NAME_EMAIL = 'email';
	const NAME_PASS = 'pass';
	const NAME_PROFILE = 'profile';
	const NAME_ROLE = 'role';
	const NAME_MAIN_LANGUAGE = 'mainLanguage';
	const NAME_KARMA = 'karma';
	const NAME_SOCIAL_INFO_VISIBLE = 'socialInfoVisible';

	const SUB_PRIVATE_MESSAGE = 1;
	const SUB_SITE_NEWS = 2;

	const SETTINGS_SHOW_PAGE = 1;
	const SETTINGS_SHOW_PAGE_COUNT = 2;
	const SETTINGS_SOCIAL_INFO_VISIBLE = 3;

	const PROFILE_USER = 1;
	const PROFILE_COMPANY = 2;

	const ACCOUNT_DEFAULT = 1;
	const ACCOUNT_SOCIAL = 2;
	/**
	 * Роль пользователя гость
	 */
	const ROLE_GUEST = 0;
	/**
	 * Роль пользователя пользователь
	 */
	const ROLE_USER = 1;
	/**
	 * Роль пользователя модератор
	 */
	const ROLE_MODER = 2;
	/**
	 * Роль пользователя администратор
	 */
	const ROLE_ADMIN = 3;
	const ROLE_COMPANY = 4;
	const ROLE_COMPANY_USER = 5;

	/**
	 * Тип профиля текущего пользователя
	 * @var integer 
	 */
	private $_profile;


	/**
	 * Именя ролей пользователей
	 * @var array 
	 */
	private static $_roleName = array(
		0 => 'guest',
		1 => 'user',
		2 => 'moder',
		3 => 'admin',
		4 => 'company',
		5 => 'companyUser',
	);

	/**
	 * Поля профиля (должны быть в базе)
	 * названия полей для name и email указать в константах NAME_NAME и NAME_EMAIL
	 * @var array 
	 */
	private $_structure = array(
		0 => array(
			'meGroup' => array(
				self::NAME_NAME => array(
					'orders' => 25,
					'title' => 'FIELD_NAME',
					'required' => 1,
					// загрузить поле, но не выводить
					'load' => array('regSocial'),
					// выводить поле
					'show' => array(),
					'type' => \app\fields\Field::STRING,
					'table' => 'users',
					'tableAlias' => 'u',
					'field' => 'name',
				),
				self::NAME_EMAIL => array(
					'orders' => 30,
					'title' => 'FIELD_EMAIL',
					'required' => 1,
					'show' => array('regSocial'),
					// параметры поля при создании
					'params' => array(
						'regSocial' => array('skipUniqueEmail' => true),
					),
					'type' => '\app\models\fields\edit\FieldEmailUser', //\app\fields\Field::EMAIL,
					'table' => 'users',
					'tableAlias' => 'u',
					'field' => 'email',
				),
				self::NAME_PASS => array(
					'orders' => 40,
					'title' => 'FIELD_PASSWORD',
					'title2' => 'FIELD_PASSWORD2',
					'required' => 1,
					'show' => array('passRecovery', 'passReset'),
					'type' => \app\fields\Field::PASS,
					'table' => 'users',
					'tableAlias' => 'u',
					'field' => 'pass',
				),
			),
		),
		self::PROFILE_USER => array(
			'meGroup' => array(
				self::NAME_SOCIAL_INFO_VISIBLE => array(
					'orders' => 0,
					'load' => array('object', 'page'),
					'type' => \app\fields\Field::HIDDEN,
					'table' => 'users',
					'tableAlias' => 'u',
					'field' => 'settings',
					'fieldIndex' => self::SETTINGS_SOCIAL_INFO_VISIBLE,
				),
				self::NAME_ROLE => array(
					'orders' => 0,
					'load' => array('reg'),
					'type' => \app\fields\Field::HIDDEN,
					'table' => 'users',
					'tableAlias' => 'u',
					'field' => 'role',
				),
				self::NAME_PROFILE => array(
					'orders' => 0,
					'load' => array('reg'),
					'type' => \app\fields\Field::HIDDEN,
					'table' => 'users',
					'tableAlias' => 'u',
					'field' => 'profile',
				),
				'accessFields' => array(
					'orders' => 5,
					'title' => 'FIELD_ACCESS_FIELDS',
					'required' => 0,
					// загрузить поле, но не выводить
					'load' => array('form', 'page', 'object'),
					// выводить поле
					'show' => array(''),
					'type' => \app\fields\Field::ACCESS_FIELDS,
					'table' => 'users',
					'tableAlias' => 'u',
					'field' => 'access_fields',
				),
				// TODO: убрать зависимость
				// название поля прописано в /protected/models/fields/Email.php
				// НЕ МЕНЯТЬ!!!
				self::NAME_ID => array(
					'orders' => 10,
					'title' => 'FIELD_NAME',
					'show' => array('page', 'object'/*, 'reg'*/),
					'type' => \app\fields\Field::HIDDEN,
					'table' => 'users',
					'tableAlias' => 'u',
					'field' => 'idusers',
				),
				'avatar' => array(
					'orders' => 15,
					'title' => 'FIELD_AVATAR',
					'required' => 0,
					// выводить поле
					'show' => array('form', 'page', 'object'),
					'type' => \app\fields\Field::AVATAR,
					'table' => 'users',
					'tableAlias' => 'u',
					'field' => 'avatar',
				),
				// флаг 'required' => 1 обязательный, и наче при запросе восстановления пароля с пустым именем, вернет другого пользователя, который тоже с пустым именем
				self::NAME_NAME => array(
					'orders' => 20,
					'title' => 'FIELD_NAME',
					// 'required' => array('form', 'page', 'object'),
					'required' => 1,
					'show' => array('form', 'page', 'object', 'reg', 'sendEmail'),
					'type' => \app\fields\Field::STRING,
					'table' => 'users',
					'tableAlias' => 'u',
					'field' => 'name',
				),
				'friendsCount' => array(
					'orders' => 25,
					'title' => 'FIELD_FRIENDS_COUNT',
					'show' => array('form', 'page', 'object'),
					'type' => \app\fields\Field::FRIENDS_COUNT,
					'table' => 'services',
					'tableAlias' => 's',
					'field' => 'friends_count',
				),
				self::NAME_EMAIL => array(
					'orders' => 30,
					'title' => 'FIELD_EMAIL',
					'required' => 1,
					'load' => array('confirmationEmail'),
					'show' => array('form', 'page', 'reg', 'regSocialSetMail', 'sendEmail'),
					'type' => '\app\models\fields\edit\FieldEmailUser', //\app\fields\Field::EMAIL,
					// параметры поля при создании
					'params' => array(
						'regSocialSetMail' => array('skipUniqueEmail' => true),
					),
					'table' => 'users',
					'tableAlias' => 'u',
					'field' => 'email',
				),
				self::NAME_PASS => array(
					'orders' => 40,
					'titleOld' => 'FIELD_PASSWORD_OLD',
					'title' => 'FIELD_PASSWORD',
					'title2' => 'FIELD_PASSWORD2',
					'required' => 1,
					'show' => array('passEdit', 'reg', 'passReset'),
					'type' => \app\fields\Field::PASS,
					// параметры поля при создании
					'params' => array(
						'passEdit' => array('ckeckOldPass' => true),
					),
					'table' => 'users',
					'tableAlias' => 'u',
					'field' => 'pass',
				),
				self::NAME_KARMA => array(
					'orders' => 50,
					'title' => 'FIELD_KARMA',
					'show' => array('page', 'object', 'karmaForm'),
					'type' => \app\fields\Field::KARMA,
					'table' => 'users',
					'tableAlias' => 'u',
					'field' => 'karma',
				),
			),
			'infoGroup' => array(
				self::NAME_MAIN_LANGUAGE => array(
					'orders' => 55,
					'title' => 'FIELD_MAIN_LANGUAGE',
					'required' => 0,
					'show' => array('form', 'page', 'object', 'sendEmail'),
					'type' => \app\fields\Field::SITE_LANGUAGES_DROPLIST,
					'table' => 'users',
					'tableAlias' => 'u',
					'field' => 'main_language',
				),
				'tel' => array(
					'orders' => 60,
					'title' => 'FIELD_TEL',
					'required' => 0,
					'show' => array('form', 'page'),
					'type' => \app\fields\Field::STRING,
					'table' => 'users',
					'tableAlias' => 'u',
					'field' => 'tel',
				),
				/*'company' => array(
					'orders' => 70,
					'title' => 'FIELD_COMPANY',
					'required' => 0,
					'show' => array('form', 'page', 'object'),
					'type' => \app\fields\Field::STRING_MULTILANG,
				),*/
				/*'country' => array(
					'disabled' => 1,

					'orders' => 70,
					'title' => 'FIELD_COUNTRY',
					'required' => 0,
					'show' => array('form', 'page'),
					'type' => \app\fields\Field::STRING,
					'table' => 'users',
					'tableAlias' => 'u',
					'field' => 'country',
				),
				'city' => array(
					'disabled' => 1,

					'orders' => 80,
					'title' => 'FIELD_CITY',
					'required' => 0,
					'show' => array('form', 'page'),
					'type' => \app\fields\Field::STRING,
					'table' => 'users',
					'tableAlias' => 'u',
					'field' => 'city',
				),*/
				'me' => array(
					'orders' => 90,
					'title' => 'FIELD_ABOUT_ME',
					'required' => 0,
					'show' => array('form', 'page', 'object'),
					'type' => \app\fields\Field::TEXT,
					'table' => 'users',
					'tableAlias' => 'u',
					'field' => 'me',
				),
				'language' => array(
					'orders' => 100,
					'title' => 'FIELD_LANGUAGES',
					'required' => 0,
					'show' => array('form', 'page', 'object'),
					'type' => \app\fields\Field::SITE_LANGUAGES_CHECKLIST,
					'table' => 'users',
					'tableAlias' => 'u',
					'field' => 'language',
				),
			),
		),
		self::PROFILE_COMPANY => array(
			'meGroup' => array(
				self::NAME_SOCIAL_INFO_VISIBLE => array(
					'orders' => 0,
					'load' => array('object', 'page'),
					'type' => \app\fields\Field::HIDDEN,
					'table' => 'users',
					'tableAlias' => 'u',
					'field' => 'settings',
					'fieldIndex' => self::SETTINGS_SOCIAL_INFO_VISIBLE,
				),
				self::NAME_ROLE => array(
					'orders' => 0,
					'load' => array('reg'),
					'type' => \app\fields\Field::HIDDEN,
					'table' => 'users',
					'tableAlias' => 'u',
					'field' => 'role',
				),
				self::NAME_PROFILE => array(
					'orders' => 0,
					'load' => array('reg'),
					'type' => \app\fields\Field::HIDDEN,
					'table' => 'users',
					'tableAlias' => 'u',
					'field' => 'profile',
				),
				'accessFields' => array(
					'orders' => 5,
					'title' => 'FIELD_ACCESS_FIELDS',
					'required' => 0,
					// загрузить поле, но не выводить
					'load' => array('form', 'page', 'object'),
					// выводить поле
					'show' => array(''),
					'type' => \app\fields\Field::ACCESS_FIELDS,
					'table' => 'users',
					'tableAlias' => 'u',
					'field' => 'access_fields',
				),
				// TODO: убрать зависимость
				// название поля прописано в /protected/models/fields/Email.php
				// НЕ МЕНЯТЬ!!!
				self::NAME_ID => array(
					'orders' => 10,
					'title' => 'FIELD_NAME',
					'show' => array('page', 'object'/*, 'reg'*/),
					'type' => \app\fields\Field::HIDDEN,
					'table' => 'users',
					'tableAlias' => 'u',
					'field' => 'idusers',
				),
				'avatar' => array(
					'orders' => 15,
					'title' => 'FIELD_AVATAR',
					'required' => 0,
					// выводить поле
					'show' => array('form', 'page', 'object'),
					'type' => \app\fields\Field::AVATAR,
					// параметры поля при создании
					'table' => 'users',
					'tableAlias' => 'u',
					'field' => 'avatar',
				),
				// флаг 'required' => 1 обязательный, и наче при запросе восстановления пароля с пустым именем, вернет другого пользователя, который тоже с пустым именем
				'companyName' => array(
					'orders' => 20,
					'title' => 'FIELD_COMPANY_NAME',
					'required' => 1,
					'show' => array('form', 'page', 'object', 'reg'),
					'type' => \app\fields\Field::STRING,
					'table' => 'users',
					'tableAlias' => 'u',
					'field' => 'company_name',
				),
				self::NAME_NAME => array(
					'orders' => 25,
					'title' => 'FIELD_NAME',
					'required' => 1,
					'show' => array('form', 'page', 'object', 'reg', 'sendEmail'),
					'type' => \app\fields\Field::STRING,
					'table' => 'users',
					'tableAlias' => 'u',
					'field' => 'name',
				),
				'friendsCount' => array(
					'orders' => 27,
					'title' => 'FIELD_FRIENDS_COUNT',
					'show' => array('form', 'page', 'object'),
					'type' => \app\fields\Field::FRIENDS_COUNT,
					'table' => 'services',
					'tableAlias' => 's',
					'field' => 'friends_count',
				),
				self::NAME_EMAIL => array(
					'orders' => 30,
					'title' => 'FIELD_EMAIL',
					'required' => 1,
					'load' => array('confirmationEmail'),
					'show' => array('form', 'page', 'reg', 'regSocialSetMail', 'sendEmail'),
					'type' => '\app\models\fields\edit\FieldEmailUser', //\app\fields\Field::EMAIL,
					// параметры поля при создании
					'params' => array(
						'regSocialSetMail' => array('skipUniqueEmail' => true),
					),
					'table' => 'users',
					'tableAlias' => 'u',
					'field' => 'email',
				),
				self::NAME_PASS => array(
					'orders' => 40,
					'titleOld' => 'FIELD_PASSWORD_OLD',
					'title' => 'FIELD_PASSWORD',
					'title2' => 'FIELD_PASSWORD2',
					'required' => 1,
					'show' => array('passEdit', 'reg', 'passReset'),
					'type' => \app\fields\Field::PASS,
					// параметры поля при создании
					'params' => array(
						'passEdit' => array('ckeckOldPass' => true),
					),
					'table' => 'users',
					'tableAlias' => 'u',
					'field' => 'pass',
				),
				self::NAME_KARMA => array(
					'orders' => 50,
					'title' => 'FIELD_KARMA',
					'show' => array('page', 'object', 'karmaForm'),
					'type' => \app\fields\Field::KARMA,
					'table' => 'users',
					'tableAlias' => 'u',
					'field' => 'karma',
				),
			),
			'infoGroup' => array(
				self::NAME_MAIN_LANGUAGE => array(
					'orders' => 60,
					'title' => 'FIELD_MAIN_LANGUAGE',
					'required' => 0,
					'show' => array('form', 'page', 'object', 'sendEmail'),
					'type' => \app\fields\Field::SITE_LANGUAGES_DROPLIST,
					'table' => 'users',
					'tableAlias' => 'u',
					'field' => 'main_language',
				),
				'language' => array(
					'orders' => 100,
					'title' => 'FIELD_LANGUAGES',
					'required' => 0,
					'show' => array('form', 'page', 'object'),
					'type' => \app\fields\Field::SITE_LANGUAGES_CHECKLIST,
					'table' => 'users',
					'tableAlias' => 'u',
					'field' => 'language',
				),
			),
		),
	);

	/**
	 * Допустимые поля
	 * @var string 
	 */
	private $_safe;
	/**
	 * Поле - пароль
	 * @var string 
	 */
	private $_pass;
	/**
	 * Выводимые названия полей
	 * @var array 
	 */
	private $_labels = array();
	/**
	 * Группы полей
	 * @var array 
	 */
	private $_group = array();
	/**
	 * Значения полей
	 * @var array 
	 */
	private $_values = array();

	/**  ????????????
	 * Поле для создания мульти акка
	 * @var string 
	 */

	/* public $code; не используется */

	/**
	 * id пользователя
	 * @var integer 
	 */
	public $idusers = null;
	/**
	 * id мульти акка пользователя
	 * @var integer 
	 */
	public $multiUser = null;


	static function getAccessFields() {
		return array(
			\app\managers\User::ACCESS_ALL => \Yii::t('user', 'FOR_ALL'),
			\app\managers\User::ACCESS_REGISTERED => \Yii::t('user', 'FOR_REGISTERED'),
			\app\managers\User::ACCESS_MULTIUSER => \Yii::t('user', 'FOR_MULTIUSER'),
			\app\managers\User::ACCESS_ONLY_ME => \Yii::t('user', 'FOR_ME'),
		);
	}


	static function getSettingsShowPageCountArray($param = null) {
		$pageCount =  array(0 => \Yii::t('nav', 'DEFAULT'), 12, 24, 48, 96);
		if($param == null) return $pageCount;
		elseif(array_key_exists($param, $pageCount)) return $pageCount[$param];

		return false;
	}


	/**
	 * Получение значений
	 * @param string $name
	 * @return mixed 
	 */
	public function __get($name) {
		if($name == 'groups') {
			$groups = array();
			foreach($this->_group as $group => $fields)
				$groups[] = $group;


			return $groups;
		}
		elseif(array_key_exists ($name, $this->_group)) {
			foreach($this->_group[$name] as $field) $values[$field] = $this->_values[$field];


			return @$values;
		}
		elseif(array_key_exists($name, $this->_manager->fields())) return $this->_manager->field($name);
		else return parent::__get($name);
	}


	/**
	 * Задание значений
	 * @param string $name
	 * @param mixed $value
	 * @return mixed 
	 */
	public function __set($name, $value) {
		return parent::__set($name, $value);
	}


	/**
	 * Данные пользователя
	 * @param integer $id
	 * @param string $target
	 * @return boolean 
	 */
	public function user($id, $target = 'page', $profile = null) {
		if($id !== 0) {
			$sql = "SELECT *, service || ';' || friends_count AS friends_count
				FROM users 
				LEFT JOIN services USING(idusers) 
				WHERE idusers = :id";
			$command = \Yii::app()->db->createCommand($sql);
			$command->bindParam(':id', $id, \PDO::PARAM_INT);
			$dataReader = $command->query();
		}
		else {
			$data = array();
			if($profile === null) $profile = 0;
		}

		if($id === 0 || ($data = $dataReader->read()) !== false) {
			if($id !== 0) {
				$this->_profile = $data['profile'];
				$this->idusers = $data['idusers'];
				$this->multiUser = $data['multiuser'];
			}
			else {
				$this->_profile = $profile;
				$data['profile'] = $profile;
				if($profile == self::PROFILE_COMPANY) $data['role'] = self::ROLE_COMPANY;
				else $data['role'] = self::ROLE_USER;
			}

			$this->_manager = new \app\managers\User(\app\managers\Manager::ACCESS_TYPE_EDIT);
			$this->_manager->setConfig(array('id' => $id));
			foreach($this->_structure[$this->_profile] as $group => $fields) {
				foreach($fields as $fieldName => $params) {
					// пропуск шага:
					// - поле не выводся в этой форме
					if((empty($params['load']) && empty($params['show'])) ||
					(!empty($params['show']) && array_search($target, $params['show']) === false || empty($params['show'])) &&
					(!empty($params['load']) && array_search($target, $params['load']) === false || empty($params['load']))) continue;
					// - поле отключено
					if(isset($params['disabled']) && $params['disabled']  && isset($params['fieldIndex']) && $params['fieldIndex'] === false)  continue; 

					$params['name'] = $fieldName;
					$params['initOptions']['labelDictionary'] = 'user';
					if(!empty($params['params'][$target])) {
						foreach($params['params'][$target] as $parId => $par)
							$params[$parId] = $par;
					}
					unset($params['params']);

					if(isset($params['required']) && is_array($params['required'])) $params['required'] = in_array($target, $params['required']);

					$this->_manager->create($params['type'], $params);

					if((!empty($params['disabled']) && !$params['disabled']) || (empty($params['load']) || (!empty($params['load']) && array_search($target, $params['load']) === false))) {
						$orders[$params['name']] = $params['orders'];
						if($params['type'] != \app\fields\Field::HIDDEN)  $groups[$group][$params['name']] = $params['orders'];
					}
				}
			}

			if(!$this->_manager->fieldsCount()) return false;

			// сортировка полей
			if(!empty($orders)) {
				asort($orders);

				foreach($orders as $field => $order)
					$this->_manager->createOrders($field);

				foreach($groups as $id => $group)
					asort($groups[$id]);

				foreach($groups as $id => $group)
					foreach($group as $field => $order)
						$this->_manager->createGroups($id, $field);


				unset($orders, $groups);
			}

			// загрузка данных
			$this->_manager->fieldsUnPackValue($data);
			$this->_manager->fieldsSetAccess($this, \Yii::app()->getComponent('user'), 'accessFields');

			return true;
		}
		else return false;
	}


	/**
	 * Сохранение
	 * @param integer $id
	 * @return integer 
	 */
	public function save($id = null) {
		if($this->_manager->fieldsValidate()) {
			$extFields = array();
			// сохраняем поля
			$this->_manager->fieldsUpdate($extFields);
			\Yii::app()->message->add($this->_manager->fieldsInfo());
			return true;
		}
		else return false;
	}


	/**
	 * Правила валидации модели
	 * @return array 
	 */
	public function rules() {
		$rules = array();
		if($this->_safe) $rules[] = array($this->_safe, 'safe');
		if($this->_pass) {
			$rules[] = array("$this->_pass,{$this->_pass}2", 'length', 'min' => 6, 'max' => 30);
			$rules[] = array($this->_pass . '2', 'compare', 'compareAttribute' => $this->_pass);
		}


		return $rules;
	}


	/**
	 * Labels для полей формы
	 * @return array 
	 */
	public function attributeLabels() {
		return $this->_labels;
	}


	/**
	 * Заглушка для CModel
	 */
	public function attributeNames() {
	}


	/**
	 * Языки пользователя
	 * @return array 
	 */
	public static function languageData() {
		return array(
			1 => 'ru',
			2 => 'en',
		);
	
	}


	/**
	 * Удаление
	 * @param integer $idUser
	 * @return integer
	 * @throws \CHttpException при отсутствии прав на удаление
	 */
	public function delete($idUser) {
		if(!\Yii::app()->user->checkAccess('moder'))
			throw new \CHttpException(403, \Yii::t('main', 'ERROR_NOT_PERMISSION_FOR_DEL_USER'));

		// удаление объявлений пользователя
		$modelObject = new \app\models\object\Object;
		$modelObject->deleteMany(array('user' => $idUser));

		// удаление кармы пользователя
		$sql = 'DELETE FROM obj_karma 
			WHERE idusers = :idusers';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':idusers', $idUser, \PDO::PARAM_INT);
		$rowCount = $command->execute();

		// удаление закладок пользователя и закладок других пользователей на этого пользователя
		$sql = 'DELETE FROM obj_bookmarks 
			WHERE idusers = :user OR owner = :user';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':user', $idUser, \PDO::PARAM_INT);
		$rowCount = $command->execute();

		// удаление акков соцсети пользователя
		$sql = 'DELETE FROM services 
			WHERE idusers = :user';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':user', $idUser, \PDO::PARAM_INT);
		$rowCount = $command->execute();

		// удаление пользователя
		$sql = 'DELETE FROM users 
			WHERE idusers = :user';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':user', $idUser, \PDO::PARAM_INT);
		$rowCount = $command->execute();

		if($rowCount)
			\Yii::app()->appLog->user('USER_DELETE_TITLE', 'USER_DELETE', array('{user}' => $idUser));
		else
			\Yii::app()->appLog->user('USER_DELETE_TITLE', 'USER_DELETE_ERROR',  array('{user}' => \CHtml::link($idUser, array('/site/user/profile', 'id' => $idUser))), \app\components\AppLog::TYPE_ERROR);


		return $rowCount;
	}


	/**
	 * Операции после выхода пользователя
	 * @return boolean 
	 */
	public function afterLogout() {
		\Yii::app()->appLog->user('EXIT_USER_TITLE', 'EXIT_USER_OK', array('{user}' => \CHtml::link("$this->idusers($this->{self::NAME_NAME})", array('/site/user/profile', 'id' => $this->idusers))));

		return true;
	}

	/**
	 * Профили
	 * @return array 
	 */
	public static function profiles() {
		return array(
			0 => \Yii::t('admin', 'USER_PROFILES_NONE'),
			1 => \Yii::t('admin', 'USER_PROFILES_USER'),
			2 => \Yii::t('admin', 'USER_PROFILES_COMPANY'),
		);
	}


	/**
	 * Статусы
	 * @return array 
	 */
	public static function status() {
		return array(
			0 => \Yii::t('admin', 'USER_STATUS_NOT_ACTIVATED'),
			1 => \Yii::t('admin', 'USER_STATUS_ACTIVATED'),
			2 => \Yii::t('admin', 'USER_STATUS_NOT_BANED'),
		);
	}


	/**
	 * Роли
	 * @return array 
	 */
	public static function role() {
		return 	array(
			self::ROLE_GUEST => \Yii::t('admin', 'USER_ROLE_GUEST'),
			self::ROLE_USER => \Yii::t('admin', 'USER_ROLE_USER'),
			self::ROLE_MODER => \Yii::t('admin', 'USER_ROLE_MODER'),
			self::ROLE_ADMIN => \Yii::t('admin', 'USER_ROLE_ADMIN'),
			self::ROLE_COMPANY => \Yii::t('admin', 'USER_ROLE_COMPANY'),
			self::ROLE_COMPANY_USER => \Yii::t('admin', 'USER_ROLE_COMPANY_USER'),
		);
	}


	/**
	 * Имена ролей
	 * @return array 
	 */
	public static function roleName() {
		return self::$_roleName;
	}


	/**
	 * Labels полей
	 * @return array 
	 */
	public function getLabels() {
		return $this->_labels;
	}


	/**
	 * Регистрация пользователя через соцсеть
	 * @param \app\components\ServiceUserIdentity $identity
	 * @return boolean 
	 */
	public function registrationSocial(& $identity) {
		if($this->_manager->fieldsValidate()) {
			// задаем значения для поля name
			$this->_manager->field(self::NAME_NAME)->setValue($identity->getState('__name'));

			$extFields = array('status' => 0);
			// сохраняем поля
			$this->_manager->fieldsInsert($extFields);
			$user = $this->_manager->getId();
			\Yii::app()->message->add($this->_manager->fieldsInfo());

			if($user) {
				$friendsCount = $identity->getState('__friendsCount');
				$urlSocial = $identity->getState('__urlSocial');
				$socialInfo = $identity->getState('__socialInfo');

				if($friendsCount === false) $friendsCount = -1;
				$sql = 'INSERT INTO services (idusers, id, service, friends_count, url_social, social_info) 
					VALUES (:idusers, :id, :service, :friends_count, :url, :info)';
				$command = \Yii::app()->db->createCommand($sql);
				$command->bindParam(':idusers', $user, \PDO::PARAM_INT);
				$command->bindValue(':id', $identity->getState('__id'), \PDO::PARAM_STR);
				$command->bindValue(':service', $identity->getState('__service'), \PDO::PARAM_STR);
				$command->bindValue(':friends_count', $friendsCount, \PDO::PARAM_INT);
				$command->bindValue(':url', $urlSocial, \PDO::PARAM_STR);
				$command->bindValue(':info', serialize($info), \PDO::PARAM_LOB);
				$rowCount = $command->execute();

				/* убрано, т.к. после соц регистрации нужно подтвердить мыло
				if($rowCount) {
					return \app\models\users\LoginForm::loginSocial($identity);
				}*/
				// добавлено для подтверждения мыла
				return $this->_createActivation($user);
			}

			return true;
		}
		else return false;
	}


	public function registrationSocialSetNewMail($user) {
		if($this->_manager->fieldsValidate()) {
			// сохраняем поля
			$rowCount = $this->_manager->fieldsUpdate();
			\Yii::app()->message->add($this->_manager->fieldsInfo());

			if($rowCount) return $this->_createActivation($user);

			return $rowCount;
		}
		else return false;
	}


	/**
	 * Регистрация
	 * @return boolean 
	 */
	public function registration() {
		if($this->_manager->fieldsValidate()) {
			// задаем значения для поля name
			if(!$this->_manager->field(self::NAME_NAME)->getValue()) {
				$name = $this->_manager->field(self::NAME_EMAIL)->getValue();
				$name = substr($name, 0, strpos($name, '@'));
				$this->_manager->field(self::NAME_NAME)->setValue($name);
			}

			// сохраняем поля
			$this->_manager->fieldsInsert();
			$user = $this->_manager->getId();
			\Yii::app()->message->add($this->_manager->fieldsInfo());

			if($user) \Yii::app()->appLog->user('USER_REGISTRATION_TITLE', 'USER_REGISTRATION', array('{user}' => \CHtml::link($user, array('/site/user/profile', 'id' => $user))));
			else \Yii::app()->appLog->user('USER_REGISTRATION_TITLE', 'USER_REGISTRATION_ERROR', array('{email}' => $this->_manager->field(self::NAME_EMAIL)->getValue()), \app\components\AppLog::TYPE_ERROR);

			if($user) return $this->_createActivation($user);
			else return false;
		}
		else return false;
	}


	static function sendMailActivation($user) {
		$model = new \app\models\users\User;
		$find = $model->user($user['id'], 'reg');
		if($find) return $model->_createActivation($user['id']);


		return false;
	}


	/**
	 * Создание активации
	 * @param integer $user
	 * @return integer 
	 */
	private function _createActivation($user) {
		$code = \app\models\TableCode::insert($user, \app\models\TableCode::REGISTRATION);

		if($code) {
			\Yii::app()->appLog->user('USER_ACTIVATION_TITLE', 'USER_ACTIVATION', array('{user}' => \CHtml::link($user, array('/site/user/profile', 'id' => $user))));
			$emailField = $this->_manager->field(self::NAME_EMAIL);
			$nameField = $this->_manager->field(self::NAME_NAME);
			$mailer = \Yii::app()->mailer;
			$mailer->IsHTML(true);
			$mailer->AddAddress($emailField->getValue());
			$mailer->Subject = \Yii::t('mail', 'MAIL_ACTIVATION_SUBJECT');
			$mailer->getView('activation', array('code' => $code, 'username' => $nameField));
			if($mailer->Send()) \Yii::app()->appLog->mail('MAIL_ACTIVATION_TITLE', 'MAIL_ACTIVATION', array('{user}' => \CHtml::link($user, array('/site/user/profile', 'id' => $user)), '{email}' => $emailField->getValue()));
			else {
				\Yii::app()->appLog->mail('MAIL_ACTIVATION_TITLE', 'MAIL_ACTIVATION_ERROR', array('{user}' => \CHtml::link($user, array('/site/user/profile', 'id' => $user)), '{email}' => $emailField->getValue(), '{error}' => $mailer->ErrorInfo), \app\components\AppLog::TYPE_ERROR);
				$return = false;
				\Yii::app()->message->add(\Yii::t('user', 'ERROR_POSIBLE_NOT_CORRECT_EMAIL', array('{email}' => \Yii::t('fields', $emailField->getTitle()))));
			}
		}
		else \Yii::app()->appLog->user('USER_ACTIVATION_TITLE', 'USER_ACTIVATION_ERROR', array('{user}' => \CHtml::link($user, array('/site/user/profile', 'id' => $user))), \app\components\AppLog::TYPE_ERROR);


		return $return;
	}


	public function getProfile() {
		return $this->_profile;
	}


	public function getRole() {
		return $this->_role;
	}


	public static function name($user) {
		$paramsUsers = \Yii::app()->params['cache']['users'];
		if($paramsUsers !== -1) {
			// получение из кеша
			$cache = \Yii::app()->cache;
			$name = $cache->get('users-name' . $user);

			if($name !== false) return $name;
		}

		$sql = 'SELECT ' . self::NAME_NAME . ' 
			FROM users
			WHERE idusers = :id';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':id', $user, \PDO::PARAM_INT);
		$dataReader = $command->query();

		if(($data = $dataReader->read()) !== false) {
			// сохранение в кеш
			if($paramsUsers !== -1) $cache->set('users-name' . $user, $data['name'], $paramsUsers);


			return $data['name'];
		}
	}


	public static function subscription($user, $type = null) {
		$paramsUsers = \Yii::app()->params['cache']['users'];
		if($paramsUsers !== -1) {
			// получение из кеша
			$cache = \Yii::app()->cache;
			$subscription = $cache->get('users-sub' . $user);

			if($subscription !== false) {
				if($type === null) return $subscription;
				else {
					$subscription['sub'] = in_array($type, $subscription['sub']);
					return $subscription;
				}
			}
		}

		$sql = 'SELECT subscription, ' . self::NAME_EMAIL .' 
			FROM users
			WHERE idusers = :id';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':id', $user, \PDO::PARAM_INT);
		$dataReader = $command->query();

		if(($data = $dataReader->read()) !== false) {
			$subscription['sub'] = \app\managers\User::toArray($data['subscription']);
			$subscription['email'] = $data[self::NAME_EMAIL];
			// сохранение в кеш
			if($paramsUsers !== -1) $cache->set('users-sub' . $user, $subscription, $paramsUsers);

			if($type === null) return $subscription;
			else {
				$subscription['sub'] = in_array($type, $subscription['sub']);
				return $subscription;
			}
		}
	}


}