<?php

namespace app\models\fields\edit;

/**
 * Поле язык сайта select
 */
class SiteLanguagesDroplist extends Droplist {


	/**
	 * Инициализация поля 
	 */
	public function init() {
		parent::init();

		$this->_type = self::SITE_LANGUAGES_DROPLIST;
		$this->_isFiltered = false;
	}


	public function data($needUse = false) {
		$data = array();

		$locale = \CLocale::getInstance(\Yii::app()->getLanguage());
		foreach(\Yii::app()->params['lang'] as $lang)
			$data[$lang] = $locale->getLocaleDisplayName($lang);


		return $data;
	}


	/**
	 * Возвращает текстовое значение
	 * @return string 
	 */
	public function getValueText() {
		if(empty($this->_value)) return null;

		$locale = \CLocale::getInstance(\Yii::app()->getLanguage());


		return $locale->getLocaleDisplayName($this->_value);
	}


	/**
	 * Установка значения
	 * @param mix $value 
	 */
	public function setValue($value) {
		if(empty($value)) $this->_value = null;
		else $this->_value = $value;
	}


}