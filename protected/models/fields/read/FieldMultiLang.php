<?php

namespace app\models\fields\read;

/**
 * Базавое поле мультиязычных полей
 */
class FieldMultiLang  extends Field {


	protected $_langs;
	protected $_curLang;

	protected $_field = 'multilang';

	protected $_lang;


	/**
	 * Создание поля
	 * @param array $data
	 * @param \app\managers\Manager $manager 
	 */
	public function __construct($data, &$manager) {
		$this->_langs = \Yii::app()->params['lang'];
		$this->_curLang = \Yii::app()->getLanguage();
		foreach($this->_langs as $lng)
			$this->_lang[$lng] = '';

		parent::__construct($data, $manager);
	}


	/**
	 * Получение свойств
	 * @param string $name
	 * @return mix 
	 */
	public function __get($name) {
		$f = strrpos($name, '_');
		if($f == strlen($name)-3) {
			$lang = substr($name, $f+1);
			if(in_array($lang, \Yii::app()->params['lang']) && substr($name, 0, $f) == $this->_name) return $this->_lang[$lang];
			elseif($name == $this->_name) return $this->getValue();
		}
		elseif($name == $this->_name) return $this->getValue();


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
	 * Инициализация поля 
	 */
	public function init() {
		$this->_multiLang = true;


		parent::init();
	}


	public function initFromCache() {
		$this->_curLang = \Yii::app()->getLanguage();
	}


	/**
	 * Получения значения
	 * @return mix 
	 */
	public function getValue() {
		return $this->_lang[$this->_curLang];
	}


	/**
	 * Установка значения
	 * @param mix $value 
	 */
	public function setValue($value) {
		if(is_array($value)) {
			foreach($this->_langs as $lang)
				if(isset($value[$lang]))$this->_lang[$lang] = $value[$lang];
		}
		else  $this->_lang[$this->_curLang] = $value;

	}


	/**
	 * Возвращает текстовое значение
	 * @return string 
	 */
	public function getValueText() {
		return $this->_lang[$this->_curLang];
	}


	public function getMultiLang() {
		return $this->_multiLang;
	}

	/**
	 * Упаковка значения поля
	 * @return string 
	 */
	public function packValue() {
		return \app\managers\Manager::fromArray((array)$this->_lang);
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

		$value = (array)$value;

		foreach($this->_langs as $lang) {
			$val = current($value);
			if($val) $this->_lang[$lang] = $val;
			next($value);
		}


		return true;
	}


	public function unPackData($data) {
		$data = $this->unPackFromArray($data);
		// для нумерации массива с 1
		$return[] = '';
		foreach($data as $id => $value)
			$return[] = $this->_manager->toArray($value, false);

		// для нумерации массива с 1
		unset($return[0]);


		return $return;
	}


	protected function unPackFromArray($data) {
		$data = substr($data, 2, -2);


		return explode('},{', $data);
	}


}