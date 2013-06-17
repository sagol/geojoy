<?php

namespace app\models\object;

/**
 * Объявление
 */
class Object extends \app\components\ModelFields {


	/**
	 * Поле срок жизни объявления в базе
	 */
	const FIELD_LIFETIME = 'lifetime';

	/**
	 * Свойство спам - не спам
	 */
	const OBJECT_SPAM_NOT = -1;
	/**
	 * Свойство спам - не известно
	 */
	const OBJECT_SPAM_UNKNOWN = 0;
	/**
	 * Свойство спам - возможно
	 */
	const OBJECT_SPAM_POSSIBLY = 1;
	/**
	 * Свойство спам - спам
	 */
	const OBJECT_SPAM_EXACTLY = 2;

	/**
	 * Свойство модерация - не нужна
	 */
	const OBJECT_MODERATE_NOT_NEED = 0;
	/**
	 * Свойство модерация - нужна
	 */
	const OBJECT_MODERATE_NEED = 1;
	/**
	 * Свойство модерация - выполнена
	 */
	const OBJECT_MODERATE_OK = 2;


	// свойства хранимые в базе, таблица objects
	/**
	 * id объявления
	 * @var integer 
	 */
	public $idobjects;
	/**
	 * id типа объявления
	 * @var integer 
	 */
	public $idobj_type;
	/**
	 * id пользователя создавшего объявление
	 * @var integer 
	 */
	public $idusers;
	/**
	 * id мульти акка пользователя создавшего объявление
	 * @var integer 
	 */
	public $multiUser;
	/**
	 * Статус модерации объявления
	 * @var integer 
	 */
	public $moderate = self::OBJECT_MODERATE_NOT_NEED;
	/**
	 * Спам статус объявления
	 * @var integer 
	 */
	public $spam = self::OBJECT_SPAM_UNKNOWN;
	/**
	 * Дата создания обявления
	 * @var date 
	 */
	public $created;
	/**
	 * Дата изменения обявления
	 * @var date 
	 */
	public $modified;
	/**
	 * Дата поднятия обявления
	 * @var date 
	 */
	public $show;

	public $categoryModerate;
	public $categoryDisabled = false;
	/**
	 * Категория в которой создано, в формате сортировки 010501
	 * @var string 
	 */
	public $categoryTree;
	/**
	 * Поле в форме (галочка), позволяющее перенос в другую категорию
	 * @var boolean 
	 */
	public $moveCategory;
	/**
	 * Признак новая запись (объявление еще не создано)
	 * @var boolean 
	 */
	private $_isNewRecord = true;
	/**
	 * Имена полей формы
	 * @var array 
	 */
	private static $_names = array();
	/**
	 * Значение категории до изменения
	 * @var integer 
	 */
	private $_oldIdobj_category;
	/**
	 * id категории объявления
	 * @var integer 
	 */
	private $_idobj_category;

	private static $_controller;



	/**
	 * Правила валидации модели
	 * @return array 
	 */
	public function rules() {
		$safe[] = 'moveCategory';
		$rules[] = array('idobj_category', 'idobj_categoryCheck');
		if($this->_manager->fieldsCount()) $safe[] = 'object';
		if(!empty($safe)) $rules[] = array(implode(',', $safe), 'safe');


		return $rules;
	}


	/**
	 * Проверка категории для перемещения в другую категорию
	 * @param string $attribute
	 * @param array $params
	 * @return boolean 
	 */
	public function idobj_categoryCheck($attribute, $params) {
		if($this->moveCategory) return true;
		if($this->_oldIdobj_category !== null && $this->_idobj_category != $this->_oldIdobj_category) {
			$category = \app\components\object\Category::getInstanse();
			$newType = $category->id($this->_idobj_category, 'type');

			if($this->idobj_type != $newType) {
				$sql = 'SELECT ot.idobj_type, ot.idobj_fields, of.name 
					FROM obj_ties ot 
					LEFT JOIN obj_category oc USING(idobj_type) 
					LEFT JOIN obj_fields of USING(idobj_fields) 
					WHERE oc.idobj_category = :new OR oc.idobj_category = :old';
				$command = \Yii::app()->db->createCommand($sql);
				$command->bindValue(':new', $this->idobj_category, \PDO::PARAM_INT);
				$command->bindValue(':old', $this->_oldIdobj_category, \PDO::PARAM_INT);
				$dataReader = $command->query();

				while(($data = $dataReader->read()) !== false) {
					if($data['idobj_type'] == $this->idobj_type) $old[$data['idobj_fields']] = $data['name'];
					else $new[$data['idobj_fields']] = $data['name'];
				}

				if(count($new) < count($old)) {
					foreach($new as $id => $data)
						unset($old[$id]);

					$this->addError($attribute, \Yii::t('nav', 'MOVE_OBJECT_DELETE_FIELDS', array('{category}' => \Yii::t('lists', $category->id($this->_idobj_category, 'name')), '{fields}' => implode(', ', $old))));
				}
				elseif(count($new) == count($old)) {
					foreach($new as $id => $data)
						if(!empty($old[$id])) unset($old[$id], $new[$id]);

					if(count($new) == 0) return true;

					$this->addError($attribute, \Yii::t('nav', 'MOVE_OBJECT_ADD_DELETE_FIELDS', array('{category}' => \Yii::t('lists', $category->id($this->_idobj_category, 'name')), '{fieldsDel}' => implode(', ', $old), '{fieldsAdd}' => implode(', ', $new))));
				}
			}
		}
	}


	/**
	 * Labels для полей формы (названия свойств модели для вывода пользователю в форме создания/изменения)
	 * @return array 
	 */
	public function attributeLabels() {
		return array_merge(
			array(
				'idobj_category' => \Yii::t('fields', 'IDOBJ_CATEGORY'),
				'moveCategory' => \Yii::t('fields', 'IDOBJ_MOVE_CATEGORY'),
		));
	}


	/**
	 * Получение значений
	 * @param string $name
	 * @return mixed 
	 */
	public function __get($name) {
		if(substr($name, 0, 7) == 'object[') {
			$values = $this->getObject();
			$f = strpos($name, ']', 7);
			$index = substr($name, 7, $f-7);
			if(strlen($name) == $f+1) return $values[$index];
			// для мультиязычных полей, на пример object[title][ru]
			else return $values[$index][substr($name, $f+2, -1)];
		}

		elseif($name == 'idobj_category') return $this->_idobj_category;


		else return parent::__get($name);
	}


	public function getCategoryList() {
		$data = \app\components\object\Category::getInstanse()->data();
		unset($data['main']);

		$list = array();
		foreach($data as $value)
			$list[$value['id']] = str_pad('', (strlen($value['tree'])-2)*2, '-') . \Yii::t('lists', $value['name']);


		return $list;
	}


	/**
	 * Задание значений
	 * @param string $name
	 * @param mixed $value
	 * @return mixed 
	 */
	public function __set($name, $value) {
		// сохранение значение категории до сохранения изменений
		if($name == 'idobj_category'){
			if($this->_oldIdobj_category === null) $this->_oldIdobj_category = $this->_idobj_category;
			$this->_idobj_category = $value;
			return true;
		}

		return parent::__set($name, $value);
	}


	/**
	 * Для работы yii с формой, со свойством object
	 * @return array 
	 */
	public function getObject() {
		$return = array();
		foreach($this->_manager->fields() as $field) {
			if($field->disabled) continue;

			if($field->type == \app\fields\Field::STRING_MULTILANG || $field->type == \app\fields\Field::TEXT_MULTILANG) {
				/* альтернатива мультиязычному */
				$id = \Yii::app()->params['lang'];
				$id = array_shift($id);
				$return[$field->name][$id] = $field->value;
				/* мультиязычный вариант
				$return[$field->name][\Yii::app()->getLanguage()] = $field->value;
				*/
			}
			elseif($field->type == \app\fields\Field::CHECKLIST) {
				if(!empty($field->value)) foreach($field->value as $id => $value)
					$return[$field->name][$id] = $id;

			}
			else $return[$field->name] = $field->value;
			
		}


		return $return;
	}


	/**
	 * Для работы yii с формой, со свойством object
	 * @param array $value 
	 */
	public function setObject($value) {
		foreach($value as $field => $val) {
			$objectField = &$this->_manager->field($field);

			if($objectField->getDisabled) continue;

			if($objectField->getType == \app\fields\Field::STRING_MULTILANG || $objectField->getType == \app\fields\Field::TEXT_MULTILANG) {
				foreach($val as $lang => $v)
					$objectField->lang[$lang] = $v;

				/* альтернатива мультиязычному */
				$id = \Yii::app()->params['lang'];
				$id = array_shift($id);
				$objectField->value = $objectField->lang[$id];
				if(empty($objectField->lang[$id])) $objectField->lang[$id] = $objectField->lang[\Yii::app()->getLanguage()];
				/* мультиязычный вариант
				$objectField->value = $objectField->lang[\Yii::app()->getLanguage()];
				*/
			}
			elseif($objectField->type == \app\fields\Field::PHOTO || $objectField->type == \app\fields\Field::PHOTOS) {
				$objectField->lists = array_merge_recursive($objectField->lists, (array)$val);
				$objectField->value = $objectField->lists['url'][0];
			}
			elseif($objectField->type == \app\fields\Field::CHECKLIST) {
				if(!empty($objectField->lists) && !empty($val)) {
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
	 * Свойство новая запись
	 * @return boolean 
	 */
	public function getIsNewRecord() {
		return $this->_isNewRecord;
	}


	/**
	 * Загрузка объявления
	 * @param integer $id
	 * @param array $data
	 * @return \app\models\object\Object
	 * @throws \CHttpException объявление не найдено
	 */
	public static function &load($id, $fieldsAccessType = 'read', $data = null) {
		$paramsObject = \Yii::app()->params['cache']['object'];
		if($paramsObject !== -1) {
			// получение из кеша
			$cache = \Yii::app()->cache;
			$object = $cache->get("object-$fieldsAccessType-$id");

			if($object !== false) {
				$object->getManager()->initFromCache();
				return $object;
			}
		}

		if($data === null) {
			$sql = 'SELECT idobj_type, idobj_category 
				FROM objects 
				WHERE idobjects = :id';
			$command = \Yii::app()->db->createCommand($sql);
			$command->bindParam(':id', $id, \PDO::PARAM_INT);
			$dataReader = $command->query();

			if(($data = $dataReader->read()) !== false) {
				$object = new Object;
				$object->_manager = &\app\managers\Object::manager($data['idobj_type'], $fieldsAccessType);
				$categoryData = \app\components\object\Category::getInstanse()->id($data['idobj_category']);
				$object->_manager->setConfig(array('id' => $id, 'moderate' => $categoryData['moderate']));
				$extFields = array('o.idobjects', 'o.idobj_category', 'o.idobj_type', 'o.idusers', 'o.multiuser', 'o.moderate', 'o.spam' => 'o.spam', 'o.created' => 'o.created', 'o.show' => 'o.show');
				$data = $object->_manager->fieldsSelect($extFields);
				if($data !== false) {
					$object->objectFromData($data);
					// сохранение в кеш
					if($paramsObject !== -1) $cache->set("object-$fieldsAccessType-$id", $object, $paramsObject);


					return $object;
				}
				else unset($object);
			}
		}
		else {
			$object = new Object;
			$object->_manager = &\app\managers\Object::manager($data['idobj_type'], $fieldsAccessType);
			$categoryData = \app\components\object\Category::getInstanse()->id($data['idobj_category']);
			$object->_manager->setConfig(array('id' => $data['idobjects'], 'moderate' => $categoryData['moderate']));
			$object->objectFromData($data);

			// сохранение в кеш
			if($paramsObject !== -1) $cache->set("object-$fieldsAccessType-$id", $object, $paramsObject);


			return $object;
		}

		throw new \CHttpException(404, \Yii::t('main', 'ERROR_NOT_FOUND_PAGE'));
	}


	/**
	 * Создание
	 * @param string $category
	 * @param string $adv 
	 */
	public function create(&$category, $adv) {
		$categoryType = $category['type'];
		$this->_manager = &\app\managers\Object::manager($categoryType, \app\managers\Manager::ACCESS_TYPE_EDIT);
		$this->_manager->setConfig(array('id' => $adv, 'moderate' => $category['moderate']));
		$this->idobj_category = $category['id'];
		$this->idobj_type = $categoryType;
		$this->idusers = \Yii::app()->user->id;
		$this->multiUser = \Yii::app()->user->multiUser;
		$this->idobjects = $adv;
		// $this->moderate = $category['moderate'];
		$this->categoryModerate = $category['moderate'];

		$this->categoryTree = $category['tree'];
		// задано при объявлении переменной $_isNewRecord
		// $this->_isNewRecord = true;
	}


	/**
	 * Перенос в другую категорию
	 */
	public function move() {
		$oldFieldsManager = null;
		if($this->_oldIdobj_category !== null && $this->_idobj_category != $this->_oldIdobj_category) {
			$newType = \app\models\object\Category::model()->findByPk($this->_idobj_category)->idobj_type;
			if($this->idobj_type != $newType) {
				$oldFieldsManager = $this->_manager;
				$this->_manager = &\app\managers\Object::manager($newType, \app\managers\Manager::ACCESS_TYPE_EDIT);
				$this->_manager->setConfig(array('id' => $this->idobjects, 'moderate' => $this->categoryModerate));

				foreach($oldFieldsManager->fields() as $fieldName => $field)
					if($this->_manager->hasField($fieldName))
						$this->_manager->field($fieldName)->setValue($field->getValue());

				$this->idobj_type = $newType;
			}
		}


		return $oldFieldsManager;
	}


	/**
	 * Сохранение
	 * @param integer $id
	 * @return boolean
	 * @throws \CHttpException 
	 */
	public function save() {
		if($this->_manager->fieldsValidate()) {
			if($this->isNewRecord) {
				$extFields['idobj_category'] = $this->idobj_category;
				$extFields['idobj_type'] = $this->idobj_type;
				$extFields['idusers'] = $this->idusers;
				$extFields['multiUser'] = $this->multiUser;
				$extFields['moderate'] = $this->categoryModerate;

				// сохраняем поля
				$rowCount = $this->_manager->fieldsInsert($extFields);

				if(!$rowCount) {
					\Yii::app()->appLog->object('OBJECT_CREATE_TITLE', 'OBJECT_CREATE_ERROR', array('{user}' => \CHtml::link($this->idusers, array('/site/user/profile', 'id' => $this->idusers))), \app\components\AppLog::TYPE_ERROR);
					throw new \CHttpException(500, \Yii::t('main', 'ERROR_NOT_CREATE_OBJECT'));
				}

				$this->idobjects = $this->_manager->getId();
			}
			else {
				$isModer = \Yii::app()->user->checkAccess('moder');
				if($isModer) $extFields['idobj_category'] = $this->idobj_category;

				// перенос объявления в другую категорию
				$moveFieldsManager = null;
				if($isModer && ($this->moveCategory || ($this->_idobj_category != $this->_oldIdobj_category))) {
					$moveFieldsManager = $this->move();
					$extFields['idobj_type'] = $this->idobj_type;
				}
				// после изменения объявления на модерацию
				$extFields['modified'] = 'NOW()';
				// убираем у объявления статус обработки по истечению срока жизни
				$extFields['disabled'] = $this->categoryDisabled ? '1' : '0';
				$extFields['moderate'] = $this->moderate = $this->categoryModerate;

				// сохраняем поля
				$rowCount = $this->_manager->fieldsUpdate($extFields, $moveFieldsManager);

				if($rowCount === false) {
					\Yii::app()->appLog->object('OBJECT_UPDATE_TITLE', 'OBJECT_UPDATE_ERROR', array('{user}' => \CHtml::link($this->idusers, array('/site/user/profile', 'id' => $this->idusers))), \app\components\AppLog::TYPE_ERROR);
					throw new \CHttpException(500, \Yii::t('main', 'ERROR_NOT_UPDATE_OBJECT'));
				}

				// удаление из кеша
				$cache = \Yii::app()->cache;
				$cache->delete("object-edit-$this->idobjects");
				$cache->delete("object-read-$this->idobjects");
			}
			
			return true;
		}
		else return false;
	}


	/**
	 * Операции после сохранения (логирование успешного сохранения)
	 */
	public function afterSave() {
		if($this->isNewRecord) \Yii::app()->appLog->object('OBJECT_CREATE_TITLE', 'OBJECT_CREATE', array('{object}' => \CHtml::link($this->idobjects, array('/site/objects/view', 'id' => $this->idobjects))));
		else \Yii::app()->appLog->object('OBJECT_UPDATE_TITLE', 'OBJECT_UPDATE', array('{object}' => \CHtml::link($this->idobjects, array('/site/objects/view', 'id' => $this->idobjects))));
	}


	/**
	 * Удаление
	 * @param integer $id
	 * @return boolean
	 * @throws \CHttpException если нету прав на удаление или ошибка при удалении
	 */
	public function delete($id) {
		// Дополнительная проверка. Выполняется при вызове из контроллера (защита от ошибки программирования)
		if(!\Yii::app()->user->checkAccess('editOrDeleteObject'))
			throw new \CHttpException(403, \Yii::t('main', 'ERROR_NOT_PERMISSION_FOR_DEL_OBJECT'));

		$rowCount = $this->_manager->fieldsDelete();

		if($rowCount) {
			\Yii::app()->appLog->object('OBJECT_DELETE_TITLE', 'OBJECT_DELETE', array('{object}' => \CHtml::link($this->idobjects, array('/site/objects/view', 'id' => $this->idobjects))));

			// удаление из кеша
			$cache = \Yii::app()->cache;
			$cache->delete("object-edit-$id");
			$cache->delete("object-read-$id");

			return true;
		}

		\Yii::app()->appLog->object('OBJECT_DELETE_TITLE', 'OBJECT_DELETE_ERROR', array('{object}' => \CHtml::link($this->idobjects, array('/site/objects/view', 'id' => $this->idobjects)), '{user}' => \CHtml::link($this->idusers, array('/site/user/profile', 'id' => $this->idusers))), \app\components\AppLog::TYPE_ERROR);
		throw new \CHttpException(500, \Yii::t('main', 'ERROR_NOT_DELETE_OBJECT'));

		return false;
	}


	/**
	 * Множественное удаление
	 * Допустимые параметры:
	 * user поле в базе idusers, type в базе idobj_type, category в базе idobj_category, id в базе idobjects
	 * на пример: $params = array('user' => 5, 'category' => array(3, 8, 45))
	 * 
	 * @param array $params
	 * @return boolean
	 * @throws \CHttpException нет прав на удаление или нет параметров для удаления
	 */
	public function deleteMany($params = array()) {
		if(!\Yii::app()->user->checkAccess('moder'))
			throw new \CHttpException(403, \Yii::t('main', 'ERROR_NOT_PERMISSION_FOR_DEL_OBJECT'));

		if(empty($params))
			throw new \CHttpException(500, \Yii::t('main', 'ERROR_NOT_ALLOW_DEL_All_OBJECTS'));


		$where = $whereArray = array();
		if(!empty($params['user'])) {
			if(is_array($params['user'])) $whereArray[] = 'o.idusers IN (' . implode(', ', $params['user']) . ')';
			else $where['user'] = 'o.idusers = :user';
		}

		if(!empty($params['type'])) {
			if(is_array($params['type'])) $whereArray[] = 'o.idobj_type IN (' . implode(', ', $params['type']) . ')';
			else $where['type'] = 'o.idobj_type = :type';
		}

		if(!empty($params['category'])) {
			if(is_array($params['category'])) $whereArray[] = 'o.idobj_category IN (' . implode(', ', $params['category']) . ')';
			else $where['category'] = 'o.idobj_category = :category';
		}

		if(!empty($params['id'])) {
			if(is_array($params['id'])) $whereArray[] = 'o.idobjects IN (' . implode(', ', $params['id']) . ')';
			else $where['id'] = 'o.idobjects = :id';
		}

		$sql = 'SELECT idobjects 
			FROM objects o 
			WHERE ' . implode(' AND ', array_merge($where, $whereArray));
		$command = \Yii::app()->db->createCommand($sql);
		foreach($where as $id => $value)
			$command->bindParam(':' . $id, $params[$id], \PDO::PARAM_INT);

		$dataReader = $command->query();
		while(($data = $dataReader->read()) !== false) {
			$model = \app\models\object\Object::load($data['idobjects'], \app\managers\Manager::ACCESS_TYPE_EDIT);
			$model->delete($data['idobjects']);
		}


		return true;
	}


	/**
	 * Поднятие вверх
	 * @return boolean 
	 */
	public function objectUp() {
		if($this->multiUser != \Yii::app()->user->multiUser) return false;

		$objectUpTime = (int)\Yii::app()->params['objectUpTime'];
		$sql = 'UPDATE objects SET show = NOW() 
			WHERE idobjects = :id AND show + :time::interval <= NOW()';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindValue(':id', $this->idobjects, \PDO::PARAM_INT);
		$command->bindValue(':time', $objectUpTime . ' seconds', \PDO::PARAM_STR);

		$rowCount = $command->execute();
		if($rowCount) {
			\Yii::app()->appLog->object('OBJECT_UP_TITLE', 'OBJECT_UP', array('{object}' => \CHtml::link($this->idobjects, array('/site/objects/view', 'id' => $this->idobjects))));
			// удаление из кеша
			$cache = \Yii::app()->cache;
			$cache->delete("object-edit-$id");
			$cache->delete("object-read-$id");
		}
		else \Yii::app()->appLog->object('OBJECT_UP_TITLE', 'OBJECT_UP_ERROR', array('{object}' => \CHtml::link($this->idobjects, array('/site/objects/view', 'id' => $this->idobjects))), \app\components\AppLog::TYPE_ERROR);


		return $rowCount;
	}


	/**
	 * Инициализация свойств
	 * @param array $data 
	 */
	public function objectFromData($data) {
		$categoryData = \app\components\object\Category::getInstanse()->id($data['idobj_category']);
		$this->idobjects = $data['idobjects'];
		$this->idobj_category = $data['idobj_category'];
		$this->idobj_type = $data['idobj_type'];
		$this->idusers = $data['idusers'];
		$this->multiUser = $data['multiuser'];
		$this->moderate = $data['moderate'];
		$this->spam = $data['spam'];
		$this->created = $data['created'];
		$this->show = $data['show'];

		$this->categoryModerate = $categoryData['moderate'];
		$this->categoryTree = $categoryData['tree'];
		if(empty($categoryData)) $this->categoryDisabled = true;

		$this->_isNewRecord = false;

		// распаковка объявления
		$this->_manager->fieldsUnPackValue($data);
	}


	/**
	 * Свойства app\models\object\Object, по аналогии с framework/web/CFormModel.php
	 * @return array 
	 */
	public function attributeNames() {
		$className = get_class($this);
		if(!isset(self::$_names[$className])) {
			$class = new \ReflectionClass(get_class($this));
			$names = array();
			foreach($class->getProperties() as $property) {
				$name = $property->getName();
				if($property->isPublic() && !$property->isStatic())
					$names[] = $name;
			}

			if($this->_manager->fieldsCount()) $names[] = 'object';

			return self::$_names[$className] = $names;
		}
		else return self::$_names[$className];
	}


	/**
	 * Установка статуса "модерация выполнена"
	 * @param integer $id
	 * @param integer $moderate
	 * @return integer 
	 */
	public static function moderateOk($id, $moderate) {
		$sql = 'UPDATE objects SET moderate = :moderate 
			WHERE idobjects = :id';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':id', $id, \PDO::PARAM_INT);
		$command->bindParam(':moderate', $moderate, \PDO::PARAM_INT);

		$rowCount = $command->execute();
		if($rowCount) {
			$object = \app\models\object\Object::load($id, \app\managers\Manager::ACCESS_TYPE_EDIT);
			$object->_manager->fieldsUpdateCount('+');

			\Yii::app()->appLog->object('OBJECT_MODERATE_TITLE', 'OBJECT_MODERATE', array('{object}' => \CHtml::link($id, array('/site/objects/view', 'id' => $id))));
			// удаление из кеша
			$cache = \Yii::app()->cache;
			$cache->delete("object-edit-$id");
			$cache->delete("object-read-$id");
		}
		else \Yii::app()->appLog->object('OBJECT_MODERATE_TITLE', 'OBJECT_MODERATE_ERROR', array('{object}' => \CHtml::link($id, array('/site/objects/view', 'id' => $id))), \app\components\AppLog::TYPE_ERROR);


		return $rowCount;
	}


	/**
	 * Пометка спамом
	 * @param integer $id
	 * @return boolean 
	 */
	public static function spam($id) {
		$spam = \Yii::app()->session->get('spam');

		if(empty($spam[$id])) {
			$spam[$id] = $id;
			\Yii::app()->session->add('spam', $spam);

			$sql = 'UPDATE objects SET spam = 1 
				WHERE idobjects = :id AND spam = 0';
			$command = \Yii::app()->db->createCommand($sql);
			$command->bindParam(':id', $id, \PDO::PARAM_INT);
			$command->execute();


			return true;
		}
		else return false;
	}


	/**
	 * Изменение свойства спам
	 * @param integer $id
	 * @param integer $type
	 * @return integer 
	 */
	public static function editSpam($id, $type) {
		$sql = 'UPDATE objects SET spam = :type 
			WHERE idobjects = :id';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':id', $id, \PDO::PARAM_INT);
		$command->bindParam(':type', $type, \PDO::PARAM_INT);
		$rowCount = $command->execute();

		// логирование изменения спам статуса объявления
		if($type == self::OBJECT_SPAM_EXACTLY) {
			$msg = 'OBJECT_SPAM_EXACTLY';
			$operation = '-';
		}
		elseif($type == self::OBJECT_SPAM_POSSIBLY) $msg = 'OBJECT_SPAM_POSSIBLY';
		elseif($type == self::OBJECT_SPAM_UNKNOWN) $msg = 'OBJECT_SPAM_RESET';
		elseif($type == self::OBJECT_SPAM_NOT) {
			$msg = 'OBJECT_SPAM_NOT';
			$operation = '+';
		}

		if($rowCount) {
			$object = \app\models\object\Object::load($id, \app\managers\Manager::ACCESS_TYPE_EDIT);
			$object->_manager->fieldsUpdateCount($operation);

			\Yii::app()->appLog->object('OBJECT_SPAM_TITLE', $msg, array('{object}' => \CHtml::link($id, array('/site/objects/view', 'id' => $id))));

			// удаление из кеша
			$cache = \Yii::app()->cache;
			$cache->delete("object-edit-$id");
			$cache->delete("object-read-$id");
		}
		else \Yii::app()->appLog->object('OBJECT_SPAM_TITLE', $msg . '_ERROR', array('{object}' => \CHtml::link($id, array('/site/objects/view', 'id' => $id))), \app\components\AppLog::TYPE_ERROR);


		return $rowCount;
	}


}