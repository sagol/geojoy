<?php

namespace app\models\fields\read;

/**
 * Поле пароль
 */
class Pass  extends Field {

	protected $_name2;
	protected $_title2;
	protected $_value2;


	/**
	 * Создание поля
	 * @param array $data
	 * @param \app\managers\Manager $manager 
	 */
	public function __construct($data, &$manager) {
		if(!empty($data['name2'])) $this->_name2 = $data['name2'];
		else $this->_name2 = $data['name'] . '2';
		if(!empty($data['title2'])) $this->_title2 = $data['title2'];

		parent::__construct($data, $manager);
	}


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::PASS;

		parent::init();
	}


	/**
	 * Получение свойств
	 * @param string $name
	 * @return mix 
	 */
	public function __get($name) {
		if($name == $this->_name) return $this->getValue();
		if($name == $this->_name . '2') return $this->getValue2();
		if($name == $this->_name . '_html') {
			if(substr($this->getValue(), 0, 10) == '$6$rounds=') return '';
			else return $this->getValue();
		}


		return parent::__get($name);
	}


	public function getName2() {
		return $this->_name2;
	}


	public function getTitle2() {
		return $this->_title2;
	}


	public function getValue2() {
		return $this->_value2;
	}



}