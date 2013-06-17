<?php

namespace app\components\widgets;

/**
 * Виджет последних объявлений
 */
class LastObjects extends \app\components\core\Widget {


	public $count = 8;


	/**
	 * Выполнение виджета
	 */
	public function run() {
		$queryParams = array(
			'skipFields' => true,
			'skipFilter' => true,
			'limit' => $this->count,
			// обязательные условия для показа допустимых объявлений
			'criteria' => array(
				'!=spam' => 2,
				'!=moderate' => 1,
				'disabled' => 0,
				'OR' => array('>=lifetime_date' => 'NOW()', '><lifetime_date'),
			),
		);

		$fieldsManager = \app\managers\Objects::getInstanse()->manager('main', 'filter');
		$objects = \app\managers\Objects::getInstanse()->filter('main', $queryParams);

		$obj = array();
		// создаем объекты объявлений
		if(!empty($objects)) foreach($objects as $object)
			$obj[$object['idobjects']] = \app\models\object\Object::load($object['idobjects'], 'read', $object);

		if(empty($obj)) return false;
		unset($criteriaFields, $fieldsManager, $objects, $object);


		$this->render('lastObjects', array('objects' => $obj));
	}


}