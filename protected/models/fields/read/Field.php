<?php

namespace app\models\fields\read;

/**
 * Базавое поле полей для просмотра
 */
class Field  extends \app\fields\Field {


	/**
	 * Создание поля
	 * @param array $data
	 * @param \app\managers\Manager $manager 
	 */
	public function __construct($data, &$manager) {
		parent::__construct($data, $manager);
	}


	protected function init() {
		parent::init();
	}


	public function loadData() {
	}


}