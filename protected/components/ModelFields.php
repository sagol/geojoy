<?php

namespace app\components;

/**
 * 
 */
class ModelFields extends \CModel {


	protected $_manager;


	public function attributeNames() {
		return array();
	}


	public function ajaxValidate() {
		$this->_manager->fieldsAttributes($_POST['app\models\fields'], false);
		$this->_manager->fieldsValidate();
		$errors = $this->_manager->fieldsGetErrors();


		return function_exists('json_encode') ? json_encode($errors) : CJSON::encode($errors);
	}


	public function getErrors($attribute = null) {
		return $this->_manager->fieldsGetErrors();
	}


	public function &getManager() {
		return $this->_manager;
	}



	/**
	 * Поле
	 * @param string $fieldName
	 * @return  
	 */
	public function &field($name) {
		return $this->_manager->field($name);
	}


	public function value($name) {
		if($this->_manager->hasField($name)) return $this->_manager->field($name)->getValueText();

		return null;
	}


	public function render($name, $folder = 'default', $type = 'html', $return = false) {
		return $this->_manager->render($name, $folder, $type, $return);
	}


	public function renderExt($name, $suff, $folder = 'default', $type = 'html', $return = false) {
		return $this->_manager->renderExt($name, $suff, $folder, $type, $return);
	}


	public function renderAccessField($name, $folder = 'default', $type = 'html', $return = false) {
		return $this->_manager->renderAccessField($name, $folder, $type, $return);
	}

}