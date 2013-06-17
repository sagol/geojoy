<?php

namespace app\models\fields\edit;

/**
 * Поле язык сайта checkbox
 */
class SiteLanguagesChecklist extends Checklist {


	/**
	 * Инициализация поля 
	 */
	public function init() {
		parent::init();

		$this->_type = self::SITE_LANGUAGES_CHECKLIST;
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
	 * Возвращает массив текстовых значений
	 * @return array 
	 */
	public function getValueText() {
		$values = array();
		if(!empty($this->_value)) {
			$locale = \CLocale::getInstance(\Yii::app()->getLanguage());

			foreach($this->_value as $id => $value)
				$values[$id] = $locale->getLocaleDisplayName($value);
		}

		return $values;
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