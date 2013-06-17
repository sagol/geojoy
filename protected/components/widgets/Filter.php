<?php

namespace app\components\widgets;

/**
 * Виджет фильтра
 */
class Filter extends \app\components\core\Widget {


	/**
	 * Массив id контроллеров где будет выводиться виджет ('site/objects')
	 * @var array $controllerId
	 */
	public $controllerId = array();
	/**
	 * Массив id действий где будет выводиться виджет ('site/objects/index')
	 * @var array $controllerId
	 */
	public $actionId = array();
	/**
	 * Массив id действий где НЕ будет выводиться виджет ('site/objects/index')
	 * @var array $actionIdSkip
	 */
	public $actionIdSkip = array();
	/**
	 * Адрес на который будет отправлен запрос в формате роутера Yii ('ControllerID/ActionID')
	 * @var string $action the URL route.
	 */
	public $action;
	/**
	 * Текущая категория в формате дерева (010102)
	 * @var string $_categoryTree
	 */
	protected $_categoryTree;

	protected $_manager;


	/**
	 * Инициализация параметров
	 * @return boolean 
	 */
	public function init() {
		$owner = $this->getOwner();
		$controllerId = $owner->getId();
		$actionId = $owner->action->getId();
		if(!in_array($controllerId, $this->controllerId) &&
			!in_array($controllerId . '/' . $actionId, $this->actionId)) return false;
		if(in_array($controllerId . '/' . $actionId, $this->actionIdSkip)) return false;

		$this->_categoryTree = \app\components\object\Category::getInstanse()->getCurCategory();
		$categoryType = \app\components\object\Category::getInstanse()->curData('type');
		$this->_manager = \app\managers\Objects::getInstanse()->manager($categoryType, 'filter');
	}


	/**
	 * Выполнение виджета
	 * @return boolean 
	 */
	public function run() {
		if(empty($this->_manager)) return false;

		$owner = $this->getOwner();
		if(!in_array($owner->getId(), $this->controllerId) &&
			!in_array($owner->getId() . '/' . $owner->action->getId(), $this->actionId)) return false;

		$filterParams = \app\components\object\Filter::getInstanse()->values();

		$fields = &$this->_manager->fields();
		if(empty($fields)) return false;

		foreach($fields as $name => &$field)
			$field->setValue(@$filterParams[$name]);

		if(empty($this->action)) {
			$category = \app\components\object\Category::getInstanse()->data($this->_categoryTree);
			$this->action = !empty($category['menu']['url']) ? array_merge($category['menu']['url'], array('useMap' => \app\components\object\Category::getInstanse()->getUseMap())) : array('/site/objects/index', 'useMap' => \app\components\object\Category::getInstanse()->getUseMap());
		}

		$config['action'] = $this->action;
		$config['model'] = $this;

		$this->render('filter', $config);
	}


	/**
	 * Для работы yii с формой, со свойством Filter
	 * @return mixed 
	 */
	public function getFilter() {
		foreach($this->_manager->fields() as $field) {
			if($field->disabled) continue;

			if($field->type == \app\fields\Field::STRING_MULTILANG || $field->type == \app\fields\Field::TEXT_MULTILANG)
				$return[$field->name][\Yii::app()->getLanguage()] = $field->value;
			elseif($field->type == \app\fields\Field::CHECKLIST) {
				if(!empty($field->value)) foreach($field->value as $id => $value)
					$return[$field->name][$id] = $id;
			}
			else $return[$field->name] = $field->value;
		}


		return @$return;
	}


	/**
	 * Для работы yii с формой, со свойством object
	 * @param mixed $value 
	 */
	public function setFilter($value) {
		foreach($value as $field => $val) {
			$objectField = &$this->_manager->field($field);

			if($objectField->disabled) continue;

			if($objectField->type == \app\fields\Field::STRING_MULTILANG || $objectField->type == \app\fields\Field::TEXT_MULTILANG) {
				foreach($val as $lang => $v)
					$objectField->lang[$lang] = $v;

				$objectField->value = $objectField->lang[\Yii::app()->getLanguage()];
			}
			elseif($objectField->type == \app\fields\Field::PHOTO || $objectField->type == \app\fields\Field::PHOTOS) {
				$objectField->lists = array_merge_recursive($objectField->lists, (array)$val);
				$objectField->value = $objectField->lists['url'][0];
			}
			elseif($objectField->type == \app\fields\Field::CHECKLIST) {
				if(!empty($objectField->lists)) {
					foreach($val as $v) {
						if(array_key_exists($v, $objectField->lists))
							$objectFieldValue[$v] = $objectField->lists[$v];
					}

					$objectField->value = $objectFieldValue;
				}
			}
			else {
				if(!empty($objectField->lists) && array_key_exists($val, $objectField->lists))
					$objectField->value = $objectField->lists[$val];
				else $objectField->value = $val;
			}
		}
	}


	/**
	 * Получение значений
	 * @param string $name
	 * @return mixed 
	 */
	public function __get($name) {

		if(array_key_exists($name, $this->_manager->fields())) {
			$values = $this->getFilter();

			return $values[$name];

			$f = strpos($name, '[');
			$lang = substr($name, $f, $f-1);
			if($f === false) return $values[$name];
			// для мультиязычных полей, на пример object[title][ru]
			else return $values[$name][$lang];
		}

		else return parent::__get($name);
	}


	/**
	 * Заглушка для выводя полей в форме
	 * @return boolean 
	 */
	public function hasErrors() {
		return false; 
	}


	/**
	 * Заглушка для выводя полей в форме
	 * @param string $attribute
	 * @return boolean 
	 */
	public function isAttributeRequired($attribute) {
		return false; 
	}


	/**
	 * Заглушка для выводя полей в форме
	 * @param string $attribute
	 * @return boolean 
	 */
	public function getError($attribute) {
		return false; 
	}


	/**
	 * Заглушка для выводя полей в форме
	 * @param string $attribute
	 * @return array 
	 */
	public function getValidators($attribute) {
		return array(); 
	}


	/**
	 * Название поля
	 * @param string $attribute
	 * @return string 
	 */
	public function getAttributeLabel($attribute) {
		$f = strpos($attribute, '[');
		if($f !== false) $attribute = substr($attribute, 0, $f);


		return $this->_manager->field($attribute)->title;
	}


	public function &getManager() {
		return $this->_manager;
	}


}