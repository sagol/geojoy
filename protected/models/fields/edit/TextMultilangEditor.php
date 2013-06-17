<?php

namespace app\models\fields\edit;

/**
 * Поле мультиязычный текстовый редактор
 */
class TextMultilangEditor extends FieldMultiLang {


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::TEXT_MULTILANG_EDITOR;

		parent::init();
	}


}