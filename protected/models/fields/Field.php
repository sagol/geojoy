<?php

namespace app\fields;

class Field  extends \CModel {

	const NONE = 0;
	const HIDDEN = 1;
	const STRING_MULTILANG = 2;
	const PASS = 3;
	const TEXT_MULTILANG = 4;
	const PHOTO = 5;
	const RADIO = 6;
	const CHECK = 7;
	const DROPLIST = 8;
	const SELECT = 9;
	const CHECKLIST = 10;
	const RADIOLIST = 11;
	const PHOTOS = 12;
	const INT = 13;
	const KARMA = 14;
	const EMAIL = 15;
	const STRING = 16;
	const MAPS = 17;
	const ACCESS_FIELDS = 18;
	const LIFETIME = 19;
	const TEXT = 20;
	const CALENDAR_EMPLOYMENT = 21;
	const AVATAR = 22;
	const FRIENDS_COUNT = 23;
	const SITE_LANGUAGES_CHECKLIST = 24;
	const SITE_LANGUAGES_DROPLIST = 25;
	const TEXT_MULTILANG_EDITOR = 26;

	/**
	 * Индекс не установлен (инициализация)
	 */
	const FIELD_INDEX_NOT_SET = NULL;
	/**
	 * Индекса нету, поле поддерживает индекс
	 */
	const FIELD_INDEX_NONE = 'none';
	/**
	 * Индекса нету, поле не поддерживает индекс
	 */
	const FIELD_INDEX_DISABLED = 'disabled';

	public static $_typeText = array(
		self::NONE => 'none',
		self::HIDDEN => 'hidden',
		self::STRING_MULTILANG => 'stringMultilang',
		self::PASS => 'pass',
		self::TEXT_MULTILANG => 'textMultilang',
		self::PHOTO => 'photo',
		self::RADIO => 'radio',
		self::CHECK => 'check',
		self::DROPLIST => 'droplist',
		self::SELECT => 'select',
		self::CHECKLIST => 'checklist',
		self::RADIOLIST => 'radiolist',
		self::PHOTOS => 'photos',
		self::INT => 'int',
		self::KARMA => 'karma',
		self::EMAIL => 'email',
		self::STRING => 'string',
		self::MAPS => 'maps',
		self::ACCESS_FIELDS => 'accessFields',
		self::LIFETIME => 'lifetime',
		self::TEXT => 'text',
		self::CALENDAR_EMPLOYMENT => 'calendarEmployment',
		self::AVATAR => 'avatar',
		self::FRIENDS_COUNT => 'friendsCount',
		self::SITE_LANGUAGES_CHECKLIST => 'siteLanguagesChecklist',
		self::SITE_LANGUAGES_DROPLIST => 'siteLanguagesDroplist',
		self::TEXT_MULTILANG_EDITOR => 'textMultilangEditor',
	);

	protected $_manager;
	protected $_type;

	// поля в базе
	protected $_id;
	protected $_name;
	protected $_title;
	protected $_units;

	// структура в базе
	protected $_table = 'objects';
	protected $_tableAlias = 'o';
	protected $_field = 'object';
	protected $_fieldIndex = Field::FIELD_INDEX_NOT_SET;
	protected $_fieldFullName;

	protected $_orders;
	protected $_required = false;
	protected $_disabled = false;
	protected $_multiLang = false;

	protected $_value;
	protected $_info;

	protected $_access = \app\managers\Manager::ACCESS_OFF;

	protected $_labelDictionary = 'fields';


	/**
	 * Дополнительные параметры создание полей
	 * допустимы:
	 * shotNameInForm true/false создавать полное или короткое имя для формы, для всех наследников app\fields\Field
	 * skipInitLists true/false пропустить инициализацию содержимого list, используется для ускорения ajax, для всех наследников app\models\fields\FieldList
	 * @var array 
	 */
	protected $initOptions = array();



	public static function getTypes() {
		return self::$_typeText;
	}


	public static function editType($type) {
		switch($type) {
			case self::HIDDEN :
			case self::STRING :
			case self::TEXT :
				$types[self::HIDDEN] = self::$_typeText[self::HIDDEN];
				$types[self::STRING] = self::$_typeText[self::STRING];
				$types[self::TEXT] = self::$_typeText[self::TEXT];
				break;

			case self::STRING_MULTILANG :
			case self::TEXT_MULTILANG :
				$types[self::STRING_MULTILANG] = self::$_typeText[self::STRING_MULTILANG];
				$types[self::TEXT_MULTILANG] = self::$_typeText[self::TEXT_MULTILANG];
				break;

			case self::INT :
			case self::RADIO :
			case self::CHECK :
			case self::DROPLIST :
			case self::RADIOLIST :
				$types[self::INT] = self::$_typeText[self::INT];
				$types[self::RADIO] = self::$_typeText[self::RADIO];
				$types[self::CHECK] = self::$_typeText[self::CHECK];
				$types[self::DROPLIST] = self::$_typeText[self::DROPLIST];
				$types[self::RADIOLIST] = self::$_typeText[self::RADIOLIST];
				break;

			case self::SELECT :
			case self::CHECKLIST :
				$types[self::SELECT] = self::$_typeText[self::SELECT];
				$types[self::CHECKLIST] = self::$_typeText[self::CHECKLIST];
				break;

			case self::PHOTO :
				$types[self::PHOTO] = self::$_typeText[self::PHOTO];
				break;

			case self::PHOTOS :
				$types[self::PHOTOS] = self::$_typeText[self::PHOTOS];
				break;

			case self::CALENDAR_EMPLOYMENT :
				$types[self::CALENDAR_EMPLOYMENT] = self::$_typeText[self::CALENDAR_EMPLOYMENT];
				break;

			case self::AVATAR :
				$types[self::AVATAR] = self::$_typeText[self::AVATAR];
				break;

			case self::LIFETIME :
				$types[self::LIFETIME] = self::$_typeText[self::LIFETIME];
				break;

			case self::MAPS :
				$types[self::MAPS] = self::$_typeText[self::MAPS];
				break;

			case self::EMAIL :
				$types[self::EMAIL] = self::$_typeText[self::EMAIL];
				break;

			case self::KARMA :
				$types[self::KARMA] = self::$_typeText[self::KARMA];
				break;

			case self::PASS :
				$types[self::PASS] = self::$_typeText[self::PASS];
				break;

			case self::ACCESS_FIELDS :
				$types[self::ACCESS_FIELDS] = self::$_typeText[self::ACCESS_FIELDS];
				break;

			case self::FRIENDS_COUNT :
				$types[self::FRIENDS_COUNT] = self::$_typeText[self::FRIENDS_COUNT];
				break;

			case self::SITE_LANGUAGES_CHECKLIST :
				$types[self::SITE_LANGUAGES_CHECKLIST] = self::$_typeText[self::SITE_LANGUAGES_CHECKLIST];
				break;

			case self::SITE_LANGUAGES_DROPLIST :
				$types[self::SITE_LANGUAGES_DROPLIST] = self::$_typeText[self::SITE_LANGUAGES_DROPLIST];
				break;
		}


		return $types;
	}


	/**
	 * Создание поля
	 * @param array $data
	 * @param \app\managers\Manager $manager 
	 */
	public function __construct($data, &$manager) {
		$this->_manager = $manager;

		if(!empty($data['params'])) $this->_setParams($data['params']);
		unset($data['params']);
		if(empty($data)) return;

		if($data !== null) {
			if(!empty($data['id'])) $this->_id = $data['id'];
			if(!empty($data['name'])) $this->_name = $data['name'];
			if(!empty($data['title'])) $this->_title = $data['title'];
			if(!empty($data['units'])) $this->_units = $data['units'];
			if(isset($data['required'])) $this->_required = $data['required'];

			if(!empty($data['table'])) $this->_table = $data['table'];
			if(!empty($data['tableAlias'])) $this->_tableAlias = $data['tableAlias'];
			if(!empty($data['field'])) $this->_field = $data['field'];

			if(array_key_exists('fieldIndex', $data)) $this->_fieldIndex = $data['fieldIndex'];

			if(!empty($data['disabled'])) $this->_disabled = $data['disabled'];

			if(!empty($data['initOptions'])) {
				if(!empty($data['initOptions']['labelDictionary'])) {
					$this->_labelDictionary = $data['initOptions']['labelDictionary'];
					unset($data['initOptions']['labelDictionary']);
				}

				$this->initOptions = $data['initOptions'];
			}
		}

		$this->init();
	}


	/**
	 * Получение свойств
	 * @param string $name
	 * @return mix 
	 */
	public function __get($name) {
		if($name == $this->_name) return $this->getValue();


		return parent::__get($name);
	}


	/**
	 * Установка свойств
	 * @param string $name
	 * @param mix $value
	 * @return mix 
	 */
	public function __set($name, $value) {
		if($name == $this->_name) return $this->setValue($value);


		return parent::__set($name, $value);
	}

	/**
	 * Вывод поля
	 * @return string 
	 */
	public function __toString() {
		return $this->getValue();
	}

	protected function init() {
		// вызываем исключение, если в классе поля не указан его тип
		if(empty($this->_type)) throw new \CException(\Yii::t('fields', 'NOT_SET_TYPE_FIELDS'));
		if($this->_multiLang && $this->_field == 'object') $this->_field == 'multilang';
		// индекс поля в массиве
		if($this->_fieldIndex == Field::FIELD_INDEX_NOT_SET)
			$this->_fieldIndex = Field::FIELD_INDEX_NONE;


		$this->_fieldFullName = $this->_tableAlias . '.' . $this->_field . ($this->isSetFieldIndex() ? '[' . $this->_fieldIndex . ']' : '');
	}


	public function initFromCache() {
	}


	public function sqlSelect() {
		return array(
			array(
				'table' => $this->_table,
				'tableAlias' => $this->_tableAlias,
				'field' => $this->_field,
				'index' => $this->getFieldIndex(),
			),
		);
	}


	public function getTable() {
		return $this->_table;
	}

	public function getTableAlias() {
		return $this->_tableAlias;
	}


	public function getField() {
		return $this->_field;
	}


	public function isSetFieldIndex() {
		return !in_array($this->_fieldIndex, array(
			'',
			Field::FIELD_INDEX_NOT_SET,
			Field::FIELD_INDEX_NONE,
			Field::FIELD_INDEX_DISABLED,
		));
	}


	public function isSetSqlIndex($sql) {
		if(!isset($sql['index'])) return false;

		return !in_array($sql['index'], array(
			'',
			Field::FIELD_INDEX_NOT_SET,
			Field::FIELD_INDEX_NONE,
			Field::FIELD_INDEX_DISABLED,
		));
	}


	public function getFieldIndex() {
		if($this->isSetFieldIndex()) return $this->_fieldIndex;

		return false;
	}


	public function isFieldIndexDisabled() {
		return $this->_fieldIndex == Field::FIELD_INDEX_DISABLED;
	}


	public function getFieldFullName() {
		if(!$this->_fieldFullName) $this->_fieldFullName = $this->_tableAlias . '.' . $this->_field . ($this->isSetFieldIndex() ? '[' . $this->_fieldIndex . ']' : '');


		return $this->_fieldFullName;
	}


	public function getType() {
		return $this->_type;
	}


	public function getId() {
		return $this->_id;
	}


	public function getName() {
		return $this->_name;
	}


	public function getTitle() {
		return $this->_title;
	}


	public function getUnits() {
		return $this->_units;
	}


	public function getDisabled() {
		return $this->_disabled;
	}


	/**
	 * Получения значения
	 * @return mix 
	 */
	public function getValue() {
		return $this->_value;
	}


	/**
	 * Возвращает текстовое значение
	 * @return string 
	 */
	public function getValueText() {
		return $this->getValue();
	}


	/**
	 * Установка значения
	 * @param mix $value 
	 */
	public function setValue($value) {
		$this->_value = $value;
	}


	public function setAccess($value) {
		$this->_access = $value;
	}


	public function getMultiLang() {
		return $this->_multiLang;
	}


	public function getAccess(){
		return $this->_access;
	}

	public function &getManager(){
		return $this->_manager;
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
			if(isset($value[$this->_fieldIndex])) $value = $value[$this->_fieldIndex];
			else return false;
		}

		$this->_value = $value;


		return true;
	}


	public function attributeNames() {
	}


	// временно
	/*public function attributeName() {
	}*/



	public function unPackData($data) {
		if($data === null) return null;

		return $this->_manager->toArray($data);
	}


	protected function _setParams($data) {
		if(empty($data)) return true;

		if(!is_array($data)) $params = explode(';', $data);
		else $params = $data;
		foreach($params as $param)
			if(strpos($param, 'index.') !== false) {
				$f = strpos($param, '=');
				$index = substr($param, $f+1);
				$param = substr($param, 0, $f);
				
				list($param, $table, $field) = explode('.', $param);
				$this->_table = $table;
				$this->_tableAlias = substr($table, 0, 1);
				$this->_field = $field;
				$this->_fieldIndex = $index;
			}


		return true;
	}


}