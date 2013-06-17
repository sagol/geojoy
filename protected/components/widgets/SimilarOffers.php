<?php

namespace app\components\widgets;

/**
 * Виджет похожих предложений
 */
class SimilarOffers extends \app\components\core\Widget {


	/**
	 * Критерии выбора из базы объектов
	 * Ограничение. Выводит только объявления такого же типа, как и категория, в каторой выводится виджет. Переменная $categoryType
	 * tree убрано для производительности, теперь нельзя делать выборку объявлений со всех подкатегорий.
	 * 
	 * Допустимые значения:
	 * 	idobjects, idobj_category, idobj_type, idusers, multiuser, moderate, spam, created, modified, show, lifetime_date
	 * Запрещенные значения:
	 * 	idobj_type
	 * Допускается символы:
	 *	'!><', // NOT IS NULL
	 *	'>=', '<=',
	 *	'!=', '<>',
	 *	'|~', // LIKE text%
	 *	'~|', // LIKE %text
	 *	'><', // IS NULL
	 *	'>', '<',
	 *	'=',
	 *	'~', // LIKE %text%
	 * Использования AND, OR. Если индекс массива не указан, используется операторо AND.
	 * array(
	 * 	'OR' => array('show' => 1, 'spam' => 1),
	 * 	'!=moderate' => 5,
	 * )
	 * (show = 1 OR spam = 1) AND NOT moderate = 5 AND tree LIKE '0101%'
	 * @var array 
	 */
	public $criteriaData = array('idobj_category');
	/**
	 * Критерии выбора из базы объектов по значениям полей.
	 * Допустимые значения: любые названия полей
	 * @var array 
	 */
	public $criteriaFields = array();
	/**
	 * Текущее объявление страницы, где выводится виджет
	 */
	public $object;


	/**
	 * Выполнение виджета
	 */
	public function run() {
		// добавляем обязательные условия для показа допустимых объявлений
		if(!empty($this->criteriaFields['OR'])) {
			$or = $this->criteriaFields['OR'];
			unset($this->criteriaFields['OR']);
			$this->criteriaFields['AND'][] = array(
				'OR' => $or,
				array(
					'!=spam' => 2,
					'!=moderate' => 1,
					'disabled' => 0,
					'OR' => array('>=lifetime_date' => 'NOW()', '><lifetime_date'),
				),
			);
		}
		else {
			$this->criteriaFields['AND'][] = array(
				'!=spam' => 2,
				'!=moderate' => 1,
				'disabled' => 0,
				'OR' => array('>=lifetime_date' => 'NOW()', '><lifetime_date'),
			);
		}

		$categoryType = \app\components\object\Category::getInstanse()->curData('type');
		$fieldsManager = \app\managers\Objects::getInstanse()->manager($categoryType, 'filter');


		if($this->object instanceof \app\models\object\Object) {
			$fields = $fieldsManager->fields();
			foreach($fields as $name => &$field)
				if($this->object->getManager()->hasField($name))
					$field->setValue($this->object->getManager()->field($name)->getValue());

			// запрещаем вывод текущего объявления
			$this->criteriaFields['AND'][] = array('!=idobjects' => $this->object->idobjects);
			// заполняем текущими значенииями объявления служебные поля таблицы
			if(!empty($this->criteriaData))
				foreach($this->criteriaData as $id => $criteriaData)
					if(is_numeric($id)) $this->criteriaFields[][$criteriaData] = $this->object->{$criteriaData};
					else $this->criteriaFields[][$id] = $criteriaData;
		}

		$obj = array();
		// получаем объявления по заданным критериям
		$objects = \app\managers\Objects::getInstanse()->filter($categoryType, array('criteria' => $this->criteriaFields));
		if(empty($objects)) return false;

		// создаем объекты объявлений
		foreach($objects as $object)
			$obj[$object['idobjects']] = \app\models\object\Object::load($object['idobjects'], 'read', $object);

		if(empty($obj)) return false;
		unset($this->object, $this->criteriaData, $this->criteriaFields, $fieldsManager, $objects, $object);


		$this->render('similarOffers', array('objects' => $obj));
	}


}