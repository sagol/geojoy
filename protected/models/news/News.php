<?php

namespace app\models\news;

/**
 * Новости
 */
class News extends \CActiveRecord {


	/**
	 * Менеджер полей. Инициализация в функции createFields()
	 * @var \app\managers\Manager
	 */
	protected $_manager;


	/**
	 * Содержимое столбца для GridView
	 * @return string
	 */
	static function gridValue($data, $row, $column) {
		return self::value($data->attributes, $column->name);
	}


	/**
	 * Содержимое столбца для CDetailView
	 * @return string
	 */
	static function value($attributes, $name) {
		static $langs;
		static $curLang;

		if($langs == null) $langs = \Yii::app()->params['lang'];
		if($curLang == null) $curLang = \Yii::app()->getLanguage();

		foreach($langs as $lng)
			$lang[$lng] = '';

		$value = $attributes[$name];
		$value = \app\managers\Manager::toArray($value);

		foreach($langs as $lng) {
			$val = current($value);
			if($val) $lang[$lng] = $val;
			next($value);
		}


		return $lang[$curLang];
	}


	/**
	 * Менеджер полей. Инициализация в функции createFields()
	 * @return \app\managers\Manager
	 */
	public function &getManager() {
		return $this->_manager;
	}


	/**
	 * Инициализация полей
	 * При существовании данных, они распаковываются
	 * @param array $data атрибуты модели
	 */
	public function createFields($data = array()) {
		$this->_manager = new \app\managers\Manager(\app\managers\Manager::ACCESS_TYPE_EDIT);
		$this->_manager->create(\app\fields\Field::STRING_MULTILANG, array(
			'title' => \Yii::t('admin', 'NEWS_FIELD_TITLE'),
			'name' => 'title',
			'field' => 'title',
			'required' => true,
		));
		$this->_manager->create(\app\fields\Field::TEXT_MULTILANG, array(
			'title' => \Yii::t('admin', 'NEWS_FIELD_BRIEF'),
			'name' => 'brief',
			'field' => 'brief',
			'required' => true,
		));
		$this->_manager->create(\app\fields\Field::TEXT_MULTILANG_EDITOR, array(
			'title' => \Yii::t('admin', 'NEWS_FIELD_NEWS'),
			'name' => 'news',
			'field' => 'news',
			'required' => true,
		));

		if(!empty($data)) $this->_manager->fieldsUnPackValue($data);
	}


	/**
	 * Упаковка значений полей перед отдачей их модели News
	 * Перед упаковкой для полей выполняются beforeFieldsInsert/beforeFieldsUpdate
	 * @param array $data атрибуты модели из $_POST
	 */
	public function setFieldsAttributes($data) {
		$this->_manager->fieldsAttributes($data);
		if($this->isNewRecord) $this->_manager->beforeFieldsInsert();
		else $this->_manager->beforeFieldsUpdate();

		foreach($this->_manager->fields() as $name => $field)
			$this->setAttribute($name, $field->packValue());
	}


	/**
	 * Создание AR модели
	 * @param string $className
	 * @return \app\models\object\Ties 
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}


	/**
	 * Таблица базы, с которой работает AR модель
	 * @return string 
	 */
	public function tableName() {
		return 'news';
	}


	/**
	 * Правила валидации модели
	 * @return array 
	 */
	public function rules() {
		return array(
			/**
			 * проверка required выполняется самим полем
			 * array('title, brief, news', 'required'),
			 */
			array('title, brief, news', 'safe'),
			array('status, type', 'required'),
			array('status, type', 'numerical', 'integerOnly' => true),
			array('create, publish', 'unsetDate'),
			array('idnews, status, type, create, publish, news, title, brief', 'safe', 'on' => 'search'),
		);
	}


	/**
	 * Удаление не установленых атрибутов, используется для полей базы create, publish.
	 * При их отсутствии в AR, они не будут участвовать в sql запросе и будут заполнены значением по умолчанию из базы.
	 * @param string $attribute валидируемый атрибут
	 * @param array $params параметры валидации
	 * @return true 
	 */
	public function unsetDate($attribute, $params) {
		if($this->{$attribute} == '') unset($this->{$attribute});

		return true;
	}


	/**
	 * Labels для полей формы
	 * @return array 
	 */
	public function attributeLabels() {
		return array(
			'idnews' => 'ID',
			'title' => \Yii::t('admin', 'NEWS_FIELD_TITLE'),
			'brief' => \Yii::t('admin', 'NEWS_FIELD_BRIEF'),
			'news' => \Yii::t('admin', 'NEWS_FIELD_NEWS'),
			'status' => \Yii::t('admin', 'NEWS_FIELD_STATUS'),
			'type' => \Yii::t('admin', 'NEWS_FIELD_TYPE'),
			'create' => \Yii::t('admin', 'NEWS_FIELD_CREATE'),
			'publish' => \Yii::t('admin', 'NEWS_FIELD_PUBLISH'),
		);
	}


	/**
	 * Провайдер для AR модели
	 * @return \CActiveDataProvider 
	 */
	public function search() {
		$criteria = new \CDbCriteria;
		$criteria->compare('idnews', $this->idnews);
		$criteria->compare('title', $this->title);
		$criteria->compare('brief', $this->brief);
		$criteria->compare('news', $this->news);
		$criteria->compare('status', $this->status);
		$criteria->compare('type', $this->type);

		return new \CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array('defaultOrder' => 'publish DESC'),
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
		if($name == 'create') {
			$create = parent::__get($name);
			if($create) return \Yii::app()->dateFormatter->formatDateTime($create, 'medium', null);
			else return null;
		}
		elseif($name == 'publish') {
			$publish = parent::__get($name);
			if($publish) return \Yii::app()->dateFormatter->formatDateTime($publish, 'medium', null);
			else return null;
		}

		else return parent::__get($name);
	}


	/**
	 * Перед сохранением приведение дат к формату хранения в базе
	 * @return true 
	 */
	protected function beforeSave() {
		$attributes = $this->getAttributes();
		if($attributes['create']) $this->setAttribute('create', date('Y-m-d', strtotime($attributes['create'])));
		if($attributes['publish']) $this->setAttribute('publish', date('Y-m-d', strtotime($attributes['publish'])));


		return true;
	}


}