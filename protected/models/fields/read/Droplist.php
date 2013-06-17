<?php

namespace app\models\fields\read;

/**
 * Поле select
 */
class Droplist  extends FieldList {


	
	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::DROPLIST;

		parent::init();
	}


}