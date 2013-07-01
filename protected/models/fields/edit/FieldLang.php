<?php

namespace app\models\fields\edit;

/**
 * Базовый класс текстовых полей
 */
class FieldLang  extends Field {


	protected $purifierOptions = array('HTML.Allowed' => '');


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->setPurifierOptions(__CLASS__);


		parent::init();
	}


	protected function setPurifierOptions($class = __CLASS__) {
		$class = lcfirst(substr($class, strrpos($class, '\\') + 1));
		if(isset(\Yii::app()->params['fields'][$class]['purifierOptions']))
			$this->purifierOptions = \Yii::app()->params['fields'][$class]['purifierOptions'];
	}


	public function setValue($value) {
		if($this->purifierOptions !== false) {
			$htmlPurifier = new \CHtmlPurifier();
			$htmlPurifier->options = $this->purifierOptions;
			$value = $htmlPurifier->purify($value);
		}
		
		$this->_value = $value;
	}


}
