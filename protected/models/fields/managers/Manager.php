<?php

namespace app\managers;

class Manager extends \CComponent {

	/**
	 * Уровни доступов 
	 */
	const ACCESS_OFF = 0;
	const ACCESS_ALL = 1;
	const ACCESS_REGISTERED = 2;
	const ACCESS_MULTIUSER = 3;
	const ACCESS_ONLY_ME = 4;
	const ACCESS_MODERATOR = 5;
	const ACCESS_ADMIN = 6;
	/**
	 * Типы доступов 
	 */
	const ACCESS_TYPE_READ = 'read';
	const ACCESS_TYPE_EDIT = 'edit';

	/**
	 * Свойства класса общие для любого объекта менеджера
	 */
	protected static $_controllerFields;
	protected static $_controllerGroups;

	protected  $_fieldsAccessType = self::ACCESS_TYPE_READ;


	protected $_mainTable;
	protected $_mainTableAlias;
	protected $_mainTablePrimaryKey;
	protected $_mainTableSequence;

	protected $_fieldsSelectSelect;
	protected $_fieldsSelectJoins = array();

	protected $_fieldsFilterWhere;
	protected $_fieldsFilterValues = array();

	/**
	 * Поля
	 * @var array 
	 */
	protected $_fields = array();
	/**
	 * Поля в порядке сортировки
	 * @var array 
	 */
	protected $_fieldsOrders = array();
	/**
	 * Поля в порядке группировки
	 * @var array 
	 */
	protected $_fieldsGroups = array();

	protected $_labelDictionary = 'fields';
	/**
	 * Дополнительные параметры создание полей
	 * допустимы:
	 * shotNameInForm true/false создавать полное или короткое имя для формы, для всех наследников app\fields\Field
	 * skipInitLists true/false пропустить инициализацию содержимого list, используется для ускорения ajax, для всех наследников app\models\fields\FieldList
	 * @var array 
	 */
	protected $initOptions = array();


	/**
	 * Свойства класса различные для любого объекта менеджера
	 */
	protected $_id;
	protected $_moderate;

	/**
	 * Признак новая запись
	 * @var boolean 
	 */
	protected $_isNewRecord = false;

	protected $_needNaturalOrdering = true;
	protected $_fieldsNaturalOrdering = array();

	protected $_fieldsOldValues = array();

	protected $_form;

	protected $_accessField;
	protected $_accessOwner;
	protected $_accessUser;


	/**
	 * Возращает значения поля для ajax запроса
	 * @param integer $field
	 * @param integer|string $parentValue
	 * @return array 
	 */
	public function ajax($fieldId, $parentValue, $needUse, $folder = 'default') {
		if($folder != 'default' && $folder != 'filter') throw new \CHttpException(400, \Yii::t('main', 'ERROR_INVALID_REQUEST'));


		$paramsFieldData = \Yii::app()->params['cache']['fieldData'];
		if($paramsFieldData !== -1) {
			// получение из кеша
			$cache = \Yii::app()->cache;
			$field = $cache->get('fieldData-' . \Yii::app()->getLanguage() . '-' . $fieldId . '-' . $parentValue);

			if($field !== false) {
				echo $field->getManager()->render($field->getName(), $folder, 'ajax', true);;
				\Yii::app()->end();
			}
		}

		$sql = 'SELECT of.idobj_fields AS id, of.name, of.type 
			FROM obj_fields of 
			WHERE of.idobj_fields = :id';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':id', $fieldId, \PDO::PARAM_INT);
		$dataReader = $command->query();

		if(($data = $dataReader->read()) !== false) {
			$data['initOptions'] = array('skipInitLists' => true);
			$field = &$this->create($data['type'], $data);
		}
		else throw new \CHttpException(400, \Yii::t('main', 'ERROR_INVALID_REQUEST'));

		$data['fieldId'] = $fieldId;
		$data['parentValue'] = $parentValue;
		$data['needUse'] = $needUse;
		$field->ajaxData($data);

		if($paramsFieldData !== -1)
			// сохранение в кеш
			$cache->set('fieldData-' . \Yii::app()->getLanguage() . '-' . $fieldId . '-' . $parentValue, $field, $paramsFieldData);

		$this->render($field->getName(), $folder, 'ajax');
		\Yii::app()->end();
	}


	public function createParams($idTies, $ties) {
		$params = '';
		$first = current($ties);
		if($first['idobj_ties'] != $idTies) throw new \CException(\Yii::t('main', 'ERROR_CREATED_TIES_NOT_LAST'));

		$field = $this->create($ties[$idTies]['type']);
		if($field->isFieldIndexDisabled()) return $params;

		unset($ties[$idTies]);
		if(empty($ties)) $params = $field->createParams(array('index' => 1));
		else {
			$fieldTable = $field->getTable();
			$fieldField = $field->getField();
			foreach($ties as $id => $tie) {
				$fieldTmp = $this->create($tie['type'], array('params' => $ties[$id]['params']));
				if($fieldTable == $fieldTmp->getTable() && $fieldField == $fieldTmp->getField()) {
					$fieldIndex = $fieldTmp->getFieldIndex();
					// проверка существования индекса у поля
					if(is_numeric($fieldIndex)) {
						$fieldIndex++;
						$params = $field->createParams(array('index' => $fieldIndex));
						break;
					}
				}
			}
		}
		if(empty($params)) $params = $field->createParams(array('index' => 1));

		return $params;
	}


	public function __construct($fieldsAccessType) {
		if($fieldsAccessType == self::ACCESS_TYPE_READ) $this->_fieldsAccessType = self::ACCESS_TYPE_READ;
		elseif($fieldsAccessType == self::ACCESS_TYPE_EDIT) $this->_fieldsAccessType = self::ACCESS_TYPE_EDIT;

		$this->init();
	}

	/**
	 * Инициализация менеждера
	 */
	public function init() {
		if(self::$_controllerFields === null) self::$_controllerFields = new \CController('fields');
		if(self::$_controllerGroups === null) self::$_controllerGroups = new \CController('groups');
	}

	/**
	 * Инициализация менеждера после кеша
	 */
	public function initFromCache() {
		if(self::$_controllerFields === null) self::$_controllerFields = new \CController('fields');
		if(self::$_controllerGroups === null) self::$_controllerGroups = new \CController('groups');

		$this->fieldsInitFromCache();
	}

	/**
	 * Получение менеждера из кеша
	 */
	static function fromCache($type, $fieldsAccessType) {
		$app = \Yii::app();
		$paramsFieldsType = $app->params['cache']['fieldsType'];
		if($paramsFieldsType !== -1) {
			$fieldsManager = $app->cache->get("fieldsType-$fieldsAccessType-$type");
			if($fieldsManager !== false) {
				$fieldsManager->initFromCache();
				return $fieldsManager;
			}
		}

		return false;
	}

	/**
	 * Сохранение менеждера в кеша
	 */
	public function toCache($type, $fieldsAccessType) {
		$app = \Yii::app();
		$paramsFieldsType = $app->params['cache']['fieldsType'];
		if($paramsFieldsType !== -1) $app->cache->set("fieldsType-$fieldsAccessType-$type", $this, $paramsFieldsType);
	}


	public function setConfig($config) {
		if(array_key_exists('id', $config) && !empty($config['id'])) $this->_id = $config['id'];
		if(array_key_exists('moderate', $config) && !empty($config['moderate'])) $this->_moderate = $config['moderate'];
	}


	public function &create($type, $data = null) {
		if(is_numeric($type)) {
			if(!empty(\app\fields\Field::$_typeText[$type])) $class = '\app\models\fields\\' . $this->_fieldsAccessType . '\\' . ucfirst(\app\fields\Field::$_typeText[$type]);
			else $class = '\app\models\fields\\' . ucfirst(\app\fields\Field::$_typeText[\app\fields\Field::NONE]);
		}
		else {
			if(in_array($type, \app\fields\Field::$_typeText)) $class = '\app\models\fields\\' . $this->_fieldsAccessType . '\\' . ucfirst($type);
			else {
				if(\Yii::import($type)) $class = $type;
				else $class = '\app\models\fields\\' . $this->_fieldsAccessType . '\\' . ucfirst(\app\fields\Field::$_typeText[\app\fields\Field::NONE]);
			}
		}

		$field = new $class($data, $this);
		$this->_fields[$field->getName()] = $field;
		if(!$this->_needNaturalOrdering) $this->_needNaturalOrdering = true;
		$this->_fieldsNaturalOrdering[$field->getName()] = $field->getFieldFullName();


		return $field;
	}


	public function createOrders($name) {
		if(!empty($this->_fields[$name])) {
			$this->_fieldsOrders[$name] = &$this->_fields[$name];
			return true;
		}

		return false;
	}


	public function &getOrders() {
		// удаление из списка полей, которые сами себя отключили
		foreach($this->_fieldsOrders as $id => $field)
			if($field->getDisabled()) unset($this->_fieldsOrders[$id]);

		return $this->_fieldsOrders;
	}


	public function createGroups($group, $name) {
		if(!empty($this->_fields[$name])) {
			$this->_fieldsGroups[$group][$name] = &$this->_fields[$name];
			return true;
		}

		return false;
	}


	public function &getGroups() {
		// удаление из списка полей, которые сами себя отключили
		foreach($this->_fieldsGroups as $id => $group)
			foreach($group as $idField => $field)
				if($field->getDisabled()) unset($this->_fieldsGroups[$id][$idField]);

		return $this->_fieldsGroups;
	}


	public function getId() {
		return $this->_id;
	}

	public function getModerate() {
		return $this->_moderate;
	}

	public function getIsNewRecord() {
		return $this->_isNewRecord;
	}

	public function setIsNewRecord($value) {
		$this->_isNewRecord = $value;
	}


	public function hasField($name) {
		return array_key_exists($name, $this->_fields);
	}


	public function fieldsCount() {
		return count($this->_fields);
	}


	public function fieldsInfo() {
		$info = array();
		foreach($this->_fields as $name => $field)
			if(!empty($field->_info)) $info[] = $field->_info;


		return $info;
	}


	protected function fieldsInitFromCache() {
		if(count($this->_fields) == 0) return;

		foreach($this->_fields as &$field)
			$field->initFromCache();
	}


	public function fieldsUnPackValue($data) {
		if(empty($data)) return;

		if($this->_needNaturalOrdering) {
			natcasesort($this->_fieldsNaturalOrdering);
			$this->_needNaturalOrdering = false;
		}

		foreach($this->_fieldsNaturalOrdering as $fieldName => $sort) {
			$field = & $this->_fields[$fieldName];
			// распаковка поля из таблицы из формата хранения данных в формат обработки данных
			$sql = $field->sqlSelect();
			foreach($sql as $params) {
				$name = $params['field'];
				if(!isset($unPack[$name])) {
					if($field->isSetSqlIndex($params)) $unPack[$name] = $field->unPackData($data[$name]);
					elseif($field->getMultiLang()) $unPack[$name] = self::toArray($data[$name], true);
					elseif(isset($data[$name])) $unPack[$name] = $data[$name];
					else $unPack[$name] = null;
				}
			}

			$field->unPackValue($unPack);

			if($this->_fieldsAccessType == self::ACCESS_TYPE_READ) {
				$field->loadData();
			}
		}
	}

	public function fieldsEventProcessing($controller, $action, $extData = array()) {
		foreach($this->_fields as $name => &$field)
			$field->eventProcessing($controller, $action, $extData);
	}


	public function fieldsAttributes($values, $saveOldValues = true) {
		if($saveOldValues) foreach($this->_fields as $name => $field)
			$this->_fieldsOldValues[$name] = $field->getValue();

		foreach($this->_fields as $name => &$field)
			if(isset($values[$name])) $field->setAttribute($name, $values);
	}


	public function fieldsValidate() {
		if(count($this->_fields) == 0) return false;
		$return = true;

		foreach($this->_fields as $name => &$field) {
			if(!$field->getDisabled()) $result = $field->validate();
			if($return && !$result) $return = false;
		}


		return $return;
	}


	public function fieldsGetErrors() {
		$return = array();
		if(count($this->_fields) == 0) return $return;

		foreach($this->_fields as $name => &$field) {
			if(!$field->getDisabled()) {
				$errors = $field->getErrors();
				if(!empty($errors)) foreach($errors as $id => $error) 
					$return[\CHtml::activeId($field, $id)] = $error;
			}
		}


		return $return;
	}


	public function fieldsSetAccess($owner, $user, $accessField) {
		if(count($this->_fields) == 0) return false;

		if(!empty($this->_fields[$accessField]) && ($this->_fields[$accessField] instanceof \app\models\fields\edit\AccessFields || $this->_fields[$accessField] instanceof \app\models\fields\read\AccessFields)) {
			$this->_accessField = $this->_fields[$accessField];
			$access = $this->_accessField->getValue();

			$this->_accessField = $this->_fields[$accessField];
			foreach($this->_fields as $name => &$field) {
				if(!empty($access[$name])) {
					$field->setAccess($access[$name]);
					$this->_accessOwner['multiUser'] = $owner->multiUser;
					$this->_accessOwner['idusers'] = $owner->idusers;
					$this->_accessUser = $user;
				}
			}
		}
		else return false;

		return true;
	}


	public function checkAccessUser() {
		if($this->_accessUser == null || $this->_accessOwner == null) return 'ACCESS_ALL';

		if($this->_accessUser->checkAccess('admin')) return 'ACCESS_ADMIN';
		elseif($this->_accessUser->checkAccess('moder')) return 'ACCESS_MODERATOR';
		elseif($this->_accessOwner['idusers'] == $this->_accessUser->getId()) return 'ACCESS_ONLY_ME';
		elseif($this->_accessOwner['multiUser'] == $this->_accessUser->getMultiUser()) return 'ACCESS_MULTIUSER';
		elseif(!$this->_accessUser->getIsGuest()) return 'ACCESS_REGISTERED';
		else return 'ACCESS_ALL';
	}


	public function fieldsSelect($extFields = array()) {
		$tables["$this->_mainTable $this->_mainTableAlias"] = array();
		foreach($this->_fields as $name => $field) {
			$sqls = $field->sqlSelect();

			if(empty($sqls)) continue;
			foreach($sqls as $sql) {
				if(empty($sql)) continue;
				$t = $sql['table'] . ' ' . $sql['tableAlias'];
				$f = $sql['tableAlias'] . '.' . $sql['field'];
				$tables[$t][$f] = $f;
			}
		}

		if(empty($tables)) return false;
		foreach($tables as &$fields) 
			foreach($fields as &$field) {
				if(is_array($field)) ksort($field);
			}

		try {

			$sql = 'SELECT ';
			foreach($tables as $table => $data)
				$sql .= implode(', ', array_values($data)) . ',';

			$sql .= implode(', ', array_values($extFields)) . ',';

			$sql = substr($sql, 0, -1);
			$sql .= " FROM $this->_mainTable $this->_mainTableAlias" .
				(!empty($this->_fieldsSelectJoins) ? ' LEFT JOIN ' . implode(' LEFT JOIN ', $this->_fieldsSelectJoins) : '');

			unset($tables["$this->_mainTable $this->_mainTableAlias"]);

			// TODO зачем $key, почему не используется
			while(list($key, $table) = each($tables)) {
				$sql .= ' LEFT JOIN ' . $table . " USING($this->_mainTablePrimaryKey)";
			}

			$sql .= " WHERE $this->_mainTablePrimaryKey = :id";
			$command = \Yii::app()->db->createCommand($sql);
			$command->bindParam(':id', $this->_id, \PDO::PARAM_INT);
			$dataReader = $command->query();
			$data = $dataReader->read();
		}
		// в случае возникновения ошибки при выполнении одного из запросов выбрасывается исключение
		catch(\CDbException $e) {
			return false;
		}


		return $data;
	}


	protected function fieldsClear($extFields = array()) {
		foreach($this->_fields as $name => &$field)
			if(!$field->getDisabled())
				if(!$field->beforeFieldsClear()) return false;
		unset($field);

		$tables[$this->_mainTable] = array();

		foreach($this->_fieldsNaturalOrdering as $name => $sort) {
			$field = $this->_fields[$name];
			$sqls = $field->sqlClear();

			if(empty($sqls)) continue;
			foreach($sqls as $sql) {
				if(empty($sql)) continue;

				if($field->isSetSqlIndex($sql)) $tables[$sql['table']][$sql['field']][$sql['index']] = $sql['value'];
				elseif($sql['value'] === null) $tables[$sql['table']][$sql['field']] = 'NULL';
				else $tables[$sql['table']][$sql['field']] = $sql['value'];
			}
		}

		if(empty($tables)) return false;
		foreach($tables as &$fields) 
			foreach($fields as &$field) {
				if(is_array($field)) ksort($field);
			}

		$connection = \Yii::app()->db;
		foreach($tables as $table => $data) {
			$sql = 'UPDATE ' . $table . ' SET';

			foreach($data as $fld => $value)
				if(is_array($value)) $sql .= ' ' . $fld . ' = \'{' . implode(',', $value) . '}\',';
				// экранирование значения выполняется самим полем в 
				else $sql .= " $fld = $value,"; 

			if(!empty($extFields)) foreach($extFields as $fld => $value)
				$sql .= " $fld = '$value',";

			$sql = substr($sql, 0, -1) . ' WHERE ' . $this->_mainTablePrimaryKey . ' = ' . $this->_id;

			$return = \Yii::app()->db->createCommand($sql)->execute();

			if($table == $this->_mainTable) $extFields = array($this->_mainTablePrimaryKey => $this->_id);
		}

		foreach($this->_fields as $name => &$field)
			$field->afterFieldsClear();
		unset($field);

		if($this->_mainTable == 'objects')
			$this->fieldsUpdateCount('-');


		return true;
	}


	public function beforeFieldsInsert() {
		foreach($this->_fields as $name => &$field)
			if(!$field->beforeInsert()) return false;

		return true;
	}


	public function afterFieldsInsert() {
		foreach($this->_fields as $name => &$field)
			$field->afterInsert();
	}


	public function fieldsInsert($extFields = array()) {
		if(!$this->beforeFieldsInsert()) return false;

		if($this->_needNaturalOrdering) {
			natcasesort($this->_fieldsNaturalOrdering);
			$this->_needNaturalOrdering = false;
		}

		foreach($this->_fieldsNaturalOrdering as $name => $sort) {
			$field = $this->_fields[$name];
			$sqls = $field->sqlInsert();

			if(empty($sqls)) continue;

			foreach($sqls as $sql) {
				if(empty($sql)) continue;

				if($field->isSetSqlIndex($sql)) $tables[$sql['table']][$sql['field']][$sql['index']] = $sql['value'];
				else $tables[$sql['table']][$sql['field']] = $sql['value'];
			}
		}

		if(empty($tables)) return false;
		foreach($tables as &$fields) 
			foreach($fields as &$field) {
				if(is_array($field)) {
					ksort($field);
					end($field);
					$max = key($field);
					// добавление пропущеный индексов
					for($i = 1; $i <=$max; $i++)
						if(!isset($field[$i])) $field[$i] = '""';

					ksort($field);
				}
			}

		$connection = \Yii::app()->db;
		$transaction = $connection->beginTransaction();
		try {
			foreach($tables as $table => $data) {
				$sql = 'INSERT INTO ' . $table . ' (';
				$values = '';
				foreach($data as $fld => $value) {
					$sql .= "$fld,";
					if(is_array($value)) $values .= '\'{' . implode(',', $value) . '}\',';
					else $values .= "$value,";
				}

				if(!empty($extFields)) foreach($extFields as $fld => $value) {
					$sql .= " $fld,";
					$values .= "'$value',";
				}

				$sql = substr($sql, 0, -1) . ') VALUES (' . substr($values, 0, -1) . ')';
				$return = \Yii::app()->db->createCommand($sql)->execute();

				if(!empty($this->_mainTableSequence) && $table == $this->_mainTable && $return) {
					$this->_id = \Yii::app()->db->getLastInsertId($this->_mainTableSequence);
					// TODO почему если задан ключ?
					if(!empty($this->_mainTablePrimaryKey)) $extFields = array($this->_mainTablePrimaryKey => $this->_id);
				}
			}

			$this->afterFieldsInsert();

			// TODO почему только для таблицы objects
			if($this->_mainTable == 'objects' && $this->_moderate !== 1) $this->fieldsUpdateCount('+');

			$transaction->commit();
		}
		// в случае возникновения ошибки при выполнении одного из запросов выбрасывается исключение
		catch(\CDbException $e) {
			$transaction->rollBack();

			return false;
		}


		return true;
	}


	public function beforeFieldsUpdate() {
		foreach($this->_fields as $name => &$field)
			if(!$field->beforeUpdate()) return false;

		return true;
	}


	public function afterFieldsUpdate() {
		foreach($this->_fields as $name => &$field)
			$field->afterUpdate();
	}


	public function fieldsUpdate($extFields = array(), $moveFieldsManager = null) {
		if(!$this->beforeFieldsUpdate()) return false;

		if($this->_needNaturalOrdering) {
			natcasesort($this->_fieldsNaturalOrdering);
			$this->_needNaturalOrdering = false;
		}

		$tables[$this->_mainTable] = array();

		foreach($this->_fieldsNaturalOrdering as $name => $sort) {
			$field = $this->_fields[$name];
			$sqls = $field->sqlUpdate();

			if(empty($sqls)) continue;
			foreach($sqls as $sql) {
				if(empty($sql)) continue;

				if($field->isSetSqlIndex($sql)) $tables[$sql['table']][$sql['field']][$sql['index']] = $sql['value'];
				elseif($sql['value'] === null) $tables[$sql['table']][$sql['field']] = 'NULL';
				else $tables[$sql['table']][$sql['field']] = $sql['value'];
			}
		}

		if(empty($tables)) return false;
		foreach($tables as &$fields) 
			foreach($fields as &$field) {
				if(is_array($field)) {
					ksort($field);
					end($field);
					$max = key($field);
					// добавление пропущеный индексов
					for($i = 1; $i <=$max; $i++)
						if(!isset($field[$i])) $field[$i] = '""';

					ksort($field);
				}
			}

		$connection = \Yii::app()->db;
		$transaction = $connection->beginTransaction();
		try {
			if(!empty($moveFieldsManager)) $moveFieldsManager->fieldsClear();

			foreach($tables as $table => $data) {
				$sql = 'UPDATE ' . $table . ' SET';

				foreach($data as $fld => $value)
					if(is_array($value)) $sql .= ' ' . $fld . ' = \'{' . implode(',', $value) . '}\',';
					// экранирование значения выполняется самим полем в 
					else $sql .= " $fld = $value,"; 

				if(!empty($extFields)) foreach($extFields as $fld => $value)
					$sql .= " $fld = '$value',";

				$sql = substr($sql, 0, -1) . ' WHERE ' . $this->_mainTablePrimaryKey . ' = ' . $this->_id;

				$return = \Yii::app()->db->createCommand($sql)->execute();

				if($table == $this->_mainTable) $extFields = array($this->_mainTablePrimaryKey => $this->_id);
			}

			$this->afterFieldsUpdate();

			if($this->_mainTable == 'objects') {
				if(!empty($moveFieldsManager)) {
					if($this->_moderate !== 1) $this->fieldsUpdateCount('+', true);
				}
				else {
					if($this->_moderate !== 1) $this->fieldsUpdateCount('+', true);
					else $this->fieldsUpdateCount('-');
				}
			}

			$transaction->commit();
		}
		// в случае возникновения ошибки при выполнении одного из запросов выбрасывается исключение
		catch(\CDbException $e) {
			$transaction->rollBack();

			return false;
		}


		return true;
	}

	public function beforeFieldsDelete() {
		foreach($this->_fields as $name => &$field)
			if(!$field->beforeDelete()) return false;

		return true;
	}


	public function afterFieldsDelete() {
		foreach($this->_fields as $name => &$field)
			$field->afterDelete();
	}

	public function fieldsDelete() {
		if(!$this->beforeFieldsDelete()) return false;

		$tables[$this->_mainTable] = array();
		foreach($this->_fields as $name => $field) {
			$sqls = $field->sqlDelete();

			if(empty($sqls)) continue;
			foreach($sqls as $sql) {
				if(empty($sql)) continue;

				$tables[$sql['table']] = $sql['table'];
			}
		}

		if(empty($tables)) return false;

		$connection = \Yii::app()->db;
		$transaction = $connection->beginTransaction();
		try {
			foreach($tables as $table) {
				$sql = 'DELETE FROM ' . $table .
				' WHERE ' . $this->_mainTablePrimaryKey . ' = ' . $this->_id;

				\Yii::app()->db->createCommand($sql)->execute();
			}

			$this->afterFieldsDelete();

			// TODO почему только для таблицы objects
			if($this->_mainTable == 'objects') $this->fieldsUpdateCount('-');

			$transaction->commit();
		}
		// в случае возникновения ошибки при выполнении одного из запросов выбрасывается исключение
		catch(\CDbException $e) {
			$transaction->rollBack();

			return false;
		}


		return true;
	}


	public function fieldsUpdateCount($operation, $considerOldValues = false) {
		foreach($this->_fields as $name => $field)
			if(in_array($field->getType(), array(\app\fields\Field::DROPLIST, \app\fields\Field::SELECT, \app\fields\Field::CHECKLIST, \app\fields\Field::RADIOLIST))) {
				$value = $field->getValue();
				// поиск различий между старыми значениями и новыми
				// если режим выключен, то просто выбираются новые значения
				if($considerOldValues) {
					$oldValue = $this->_fieldsOldValues[$name];
					// сравниваем и удаляем, одинаковые значения
					if(is_array($value)) {
						foreach($value as $k => $val) {
							if(($key = array_search($val, $oldValue)) !== false)
								unset($value[$k], $oldValue[$key]);
						}
					}
					else {
						if($oldValue == $value) unset($value, $oldValue);
					}

					// сохраняем в массивы $ids и $oldId найденые различия
					if(!empty($oldValue) && is_array($oldValue)) $oldValue = implode(', ', $oldValue);
					if(!empty($oldValue)) $oldId[] = $oldValue;
				}

				if(!empty($value) && is_array($value)) $value = implode(', ', $value);
				if(!empty($value)) $ids[] = $value;
			}

		if(!empty($ids)) {
			$sql = 'UPDATE obj_fields_values 
				SET count = count ' . $operation . ' 1 
				WHERE idobj_fields_values IN (' . implode(', ', $ids) . ')';
			\Yii::app()->db->createCommand($sql)->execute();

			$cache = \Yii::app()->cache;
			foreach($ids as $id)
				$cache->delete("fieldData-$id");
		}

		if($considerOldValues && !empty($oldId)) {
			$oldId = implode(', ', $oldId);
			$sql = 'UPDATE obj_fields_values 
				SET count = count - 1 
				WHERE idobj_fields_values IN (' . $oldId . ')';
			\Yii::app()->db->createCommand($sql)->execute();
		}
	}


	public function checkAccess($field) {
		switch($field->getAccess()) {
			case self::ACCESS_OFF:
			case self::ACCESS_ALL:
				return true;
			case self::ACCESS_REGISTERED:
				return !$this->_accessUser->getIsGuest();
			case self::ACCESS_MULTIUSER:
				return $this->_accessOwner['multiUser'] == $this->_accessUser->getMultiUser() || $this->_accessUser->checkAccess('moder');
			case self::ACCESS_ONLY_ME:
				return $this->_accessOwner['idusers'] == $this->_accessUser->getId() || $this->_accessUser->checkAccess('moder');
			case self::ACCESS_MODERATOR:
				return $this->_accessUser->checkAccess('moder');
			case self::ACCESS_ADMIN:
				return $this->_accessUser->checkAccess('admin');
		}


		return false;
	}


	public function sqlFilter($values) {
		if(empty($values) && !$this->_isFiltered) return false;

		return array(
			$this->_table . ' ' . $this->_tableAlias => array(
				$this->_fieldFullName => $values,
			),
		);
	}


	public function getForm() {
		return $this->_form;
	}


	public function setForm($form) {
		$this->_form = $form;
	}


	public function render($name, $folder = 'default', $type = 'html', $return = false) {
		if(!array_key_exists($name, $this->_fields)) return null;
		$field = &$this->_fields[$name];
		if(!$this->checkAccess($field)) return false;

		if($folder == '') $folder = 'default';
		$data = array(
			'model' => $field,
			'value' => $field->getValueText(),
			'form' => $this->_form,
		);

		$output = self::$_controllerFields->renderPartial($folder . '/' . $type . '/' . \app\fields\Field::$_typeText[$field->getType()], $data, true);


		if($return) return $output;

		echo  $output;
	}


	public function renderExt($name, $suff, $folder = 'default', $type = 'html', $return = false) {
		if(!array_key_exists($name, $this->_fields)) return null;
		$field = &$this->_fields[$name];
		if(!$this->checkAccess($field)) return false;

		if($folder == '') $folder = 'default';
		$data = array(
			'model' => $field,
			'value' => $field->getValueText(),
			'form' => $this->_form,
		);

		$output = self::$_controllerFields->renderPartial($folder . '/' . $type . '/' . \app\fields\Field::$_typeText[$field->getType()] . ucwords($suff), $data, true);


		if($return) return $output;

		echo  $output;
	}


	public function renderAccessField($name, $folder = 'default', $type = 'html', $return = false) {
		if(!array_key_exists($name, $this->_fields) || !isset($this->_accessField)) return null;
		$field = &$this->_fields[$name];
		if($folder == '') $folder = 'default';
		$data = array(
			'model' => $this->_accessField,
			'value' => $this->_accessField->getValue(),
			'form' => $this->_form,
			'fieldName' => $field->getName(),
		);

		$output = self::$_controllerFields->renderPartial($folder . '/' . $type . '/' . \app\fields\Field::$_typeText[$this->_accessField->getType()], $data, true);


		if($return) return $output;

		echo  $output;
	}


	public function renderGroup($group, $view = 'default', $return = false) {
		if(empty($this->_fieldsGroups[$group])) return false;

		$fields = $this->_fieldsGroups[$group];

		foreach($fields as $name => $field)
			if(!$this->checkAccess($field)) unset($fields[$name]);

		$data = array(
			'fields' => $fields,
		);

		$output = self::$_controllerGroups->renderPartial($view, $data, true);


		if($return) return $output;

		echo  $output;
	}


	public function renderGroups($view = 'default', $return = false) {
		$output = '';

		foreach(array_keys($this->_fieldsGroups) as $group)
			$output .= $this->renderGroup($group, $view, $return);


		if($return) return $output;
	}


	// конвертация в массив
	static function toArray($value, $trim = true) {
		if($value == '') return array();

		// для нумерации массива с 1
		$return[] = '';
		$start = 0;
		$end = 0;
		$find = false;
		if($trim) $value = substr($value, 1, -1);
		$value .= ',';
		while($start <= strlen($value)){
			if(!$find) {
				if(($start = strpos($value, ',', $end)) === false) break;
				$startFd = strpos($value, '"', $end);
				if($startFd < $start && $startFd !== false) {
					$find = true;
					$end = $startFd;
				}
				else {
					$return[] = htmlspecialchars_decode(substr($value, $end, $start-$end), ENT_QUOTES);
					$end = $start+1;
				}
			}
			else {
				if(($end = strpos($value, '",', $end+1)) === false) break;
				if($value[$end-1] != '\\') {
					$return[] = htmlspecialchars_decode(substr($value, $startFd+1, $end-$startFd-1), ENT_QUOTES);
					$find = false;
					$end += 2;
				}
			}
		}

		// для нумерации массива с 1
		unset($return[0]);


		return $return;
	}


	// конвертация из массива
	static function fromArray(array $array) {
		foreach($array as $field)
			if(is_array($field)) $return[] = self::fromArray($field);
			else $return[] = '"' . htmlspecialchars($field, ENT_QUOTES) . '"';


		return '{' . @implode(',', $return) . '}';
	}


	public function &field($name) {
		if(array_key_exists($name, $this->_fields)) return $this->_fields[$name];

		throw new \CException(\Yii::t('main', 'ERROR_FIELD_NOT_EXISTS'));
	}

	public function &fields() {
		return $this->_fields;
	}


	public function setMainTable($value) {
		$this->_mainTable = $value;
	}


	public function getMainTable() {
		return $this->_mainTable;
	}


	public function setMainTableAlias($value) {
		$this->_mainTableAlias = $value;
	}


	public function getMainTableAlias() {
		return $this->_mainTableAlias;
	}

	public function setMainTablePrimaryKey($value) {
		$this->_mainTablePrimaryKey = $value;
	}


	public function getMainTablePrimaryKey() {
		return $this->_mainTablePrimaryKey;
	}

	public function setMainTableSequence($value) {
		$this->_mainTableSequence = $value;
	}


	public function getMainTableSequence() {
		return $this->_mainTableSequence;
	}


	public function setFieldsSelectJoins(array $value) {
		$this->_fieldsSelectJoins = $value;
	}


	public function getFieldsSelectJoins() {
		return $this->_fieldsSelectJoins;
	}
}
