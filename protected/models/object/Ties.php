<?php

namespace app\models\object;

/**
 * Связи
 */
class Ties extends \CActiveRecord {


	/**
	 * Поля
	 * @var array 
	 */
	private static $_objectFields = array();
	/**
	 * Типы полей
	 * @var array 
	 */
	private static $_objectType = array();
	/**
	 * Группы связей
	 * @var array 
	 */
	private static $_tiesGroups = array();
	/**
	 * Использование связи в фильтре
	 * @var array 
	 */
	private static $_filterList = array();
	/**
	 * Типы полей
	 * @var array 
	 */
	private static $_fieldsType = array();


	/**
	 * Создание AR модели
	 * @param string $className
	 * @return \app\models\object\Ties 
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}


	/**
	 * Таблица базы, с которой работает AR модель
	 * @return string 
	 */
	public function tableName() {
		return 'obj_ties';
	}


	/**
	 * Правила валидации модели
	 * @return array 
	 */
	public function rules() {
		return array(
			array('idobj_type, idobj_fields', 'required'),
			array('idobj_type', 'checkUnique'),
			array('idobj_type, idobj_fields, idobj_ties_groups, required, filter, disabled', 'numerical', 'integerOnly' => true),
			array('orders, params', 'safe'),
			array('idobj_ties, idobj_type, idobj_fields, idobj_ties_groups, required, filter, orders, disabled, params', 'safe', 'on' => 'search'),
		);
	}


	/**
	 * Проверка уникальности связи
	 * @param string $attribute
	 * @param array $params 
	 */
	public function checkUnique($attribute, $params) {
		if(!$this->hasErrors()) {
			$params['criteria'] = array(
				'condition' => 'idobj_fields = :id',
				'params' => array(':id' => $this->idobj_fields),
			);

			$validator = \CValidator::createValidator('unique', $this, $attribute, $params);
			$validator->validate($this, array($attribute));
		}
	}


	/**
	 * Labels для полей формы
	 * @return array 
	 */
	public function attributeLabels() {
		return array(
			'idobj_ties' => 'ID',
			'idobj_type' => \Yii::t('admin', 'TIES_FIELD_TYPE'),
			'idobj_ties_groups' => \Yii::t('admin', 'TIES_FIELD_TIES_GROUPS'),
			'idobj_fields' => \Yii::t('admin', 'TIES_FIELD_FIELDS'),
			'filter' => \Yii::t('admin', 'TIES_FIELD_FILTER'),
			'filter_val' => \Yii::t('admin', 'TIES_FIELD_FILTER'),
			'orders' => \Yii::t('admin', 'TIES_FIELD_ORDERS'),
			'disabled' => \Yii::t('admin', 'TIES_FIELD_DISABLED'),
			'required' => \Yii::t('admin', 'TIES_FIELD_REQUIRED'),
		);
	}


	/**
	 * Провайдер для AR модели
	 * @return \CActiveDataProvider 
	 */
	public function search() {
		if(!is_numeric($this->idobj_type)) $this->idobj_type = array_search($this->idobj_type, $this->idobj_type_arr);
		if(!is_numeric($this->idobj_ties_groups)) $this->idobj_ties_groups = array_search($this->idobj_ties_groups, $this->idobj_ties_groups_arr);
		if(!is_numeric($this->idobj_fields)) $this->idobj_fields = array_search($this->idobj_fields, $this->idobj_fields_arr);

		// обработка значений числовых полей (для фильтра)
		if(!is_numeric($this->idobj_ties)) $this->idobj_ties = '';
		if(!is_numeric($this->idobj_type)) $this->idobj_type = '';
		if(!is_numeric($this->idobj_ties_groups)) $this->idobj_ties_groups = '';
		if(!is_numeric($this->idobj_fields)) $this->idobj_fields = '';
		if(!is_numeric($this->filter)) $this->filter = '';
		if(!is_numeric($this->orders)) $this->orders = '';
		if(!is_numeric($this->disabled)) $this->disabled = '';
		if(!is_numeric($this->required)) $this->required = '';

		$criteria = new \CDbCriteria;
		$criteria->compare('idobj_type', $this->idobj_type);
		$criteria->compare('idobj_ties_groups', $this->idobj_ties_groups);
		$criteria->compare('idobj_fields', $this->idobj_fields);
		$criteria->compare('orders', $this->orders);
		$criteria->compare('disabled', $this->disabled);
		$criteria->compare('required', $this->required);

		return new \CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'pagination' => array(
				'pageSize' => 50,
			),
		));
	}


	/**
	 * Получение значений
	 * @param string $name
	 * @return mixed 
	 */
	public function __get($name) {
		if($name == 'idobj_type_val') return $this->idObjectType(false, parent::__get('idobj_type'), false);
		elseif($name == 'idobj_type_arr') return $this->idObjectType();
		elseif($name == 'idobj_type_arr1') return $this->idObjectType(true, null, false);

		elseif($name == 'idobj_fields_val') return $this->idObjectFields(false, parent::__get('idobj_fields'), false);
		elseif($name == 'idobj_fields_arr') return $this->idObjectFields();
		elseif($name == 'idobj_fields_arr1') return $this->idObjectFields(true, null, false);

		elseif($name == 'idobj_ties_groups_val') return $this->idTiesGroups(false, parent::__get('idobj_ties_groups'), false);
		elseif($name == 'idobj_ties_groups_arr') return $this->idTiesGroups();
		elseif($name == 'idobj_ties_groups_arr1') return $this->idTiesGroups(true, null, false);

		elseif($name == 'filter_val') return $this->filterList(false, parent::__get('filter'), false);
		elseif($name == 'filter_arr') return $this->filterList();
		elseif($name == 'filter_arr1') return $this->filterList(true, null, false);
		
		else return parent::__get($name);
	}


	/**
	 * Операции перед сохранением
	 * @return boolean 
	 */
	public function beforeSave() {
		if(!$this->orders) unset($this->orders);


		return true;
	}


	/**
	 * Операции после сохранения
	 */
	public function afterSave() {
		if($this->getIsNewRecord()) {
			$sql = 'SELECT ot.idobj_ties, of.type, ot.params 
				FROM obj_ties ot 
				LEFT JOIN obj_fields of ON ot.idobj_fields = of.idobj_fields 
				WHERE ot.idobj_type = :idobj_type 
				ORDER BY ot.idobj_ties DESC';
			$command = \Yii::app()->db->createCommand($sql);
			$command->bindValue(':idobj_type', $this->idobj_type, \PDO::PARAM_INT);
			$dataReader = $command->query();

			while(($data = $dataReader->read()) !== false)
				$ties[$data['idobj_ties']] = $data;

			$fieldsManager = new \app\managers\Object(\app\managers\Object::ACCESS_TYPE_EDIT);
			$this->params = $fieldsManager->createParams($this->idobj_ties, $ties);

			$sql = 'UPDATE obj_ties 
				SET params = :params 
				WHERE idobj_ties = :idobj_ties';
			$command = \Yii::app()->db->createCommand($sql);
			$command->bindValue(':idobj_ties', $this->idobj_ties, \PDO::PARAM_INT);
			$command->bindValue(':params', $this->params, \PDO::PARAM_STR);
			$command->execute();
		}

		// удаление из кеша
		$cache = \Yii::app()->cache;
		$cache->delete("fieldsType-read-$this->idobj_type");
		$cache->delete("fieldsType-edit-$this->idobj_type");

		// менеджеров полей
		$cache->delete('objectFields-managersFilter');

		// удаление из кеша виджета фильтра
		// не используется на данный момент
		// $cache->delete('widgetFilter');
		// удаление из кеша фильтра
		$cache->delete('filter');
	}


	/**
	 * Операции перед удалением
	 * @return boolean 
	 */
	public function beforeDelete() {
		$sql = 'SELECT type 
			FROM obj_fields 
			WHERE idobj_fields = :field';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindValue(':field', $this->idobj_fields, \PDO::PARAM_INT);
		$type = $command->query()->read();

		$sql = "SELECT ot.idobj_ties, ot.idobj_fields, of.type 
			FROM obj_ties ot 
			LEFT JOIN obj_fields of USING(idobj_fields) 
			WHERE idobj_type = :type 
			ORDER BY idobj_ties";
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindValue(':type', $this->idobj_type, \PDO::PARAM_INT);
		$dataReader = $command->query();

		$multi = $type['type'] == \app\fields\Field::STRING_MULTILANG || $type['type'] == \app\fields\Field::TEXT_MULTILANG;

		$i = 0;
		while(($data = $dataReader->read()) !== false) {
			$typeMulti = $data['type'] == \app\fields\Field::STRING_MULTILANG || $data['type'] == \app\fields\Field::TEXT_MULTILANG; 
			if($multi === $typeMulti) $i++;
			if($data['idobj_fields'] == $this->idobj_fields) $position = $i;
		}

		if($multi) $field = 'multilang';
		else $field = 'object';

		if($i == 1) $value = "{}";
		elseif($position == 1) $value = $field . '[2:' . $i . ']';
		elseif($position == $i) $sql = $field . '[1:' . ($i-1) . ']';
		else $value = $field . '[1:' . ($i-1) . '] || ' . $field . '[' . ($i-1) . ':' . $position . ']' ;

		$sql = "UPDATE objects SET $field = :value 
			WHERE idobj_type = :type";
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':value', $value, \PDO::PARAM_STR);
		$command->bindValue(':type', $this->idobj_type, \PDO::PARAM_INT);
		$rowCount = $command->execute();


		return true;
	}


	/**
	 * Операции после удаления
	 */
	public function afterDelete() {
		// удаление из кеша
		$cache = \Yii::app()->cache;
		$cache->delete("fieldsType-read-$this->idobj_type");
		$cache->delete("fieldsType-edit-$this->idobj_type");

		// менеджеров полей
		$cache->delete('objectFields-managersFilter');

		// удаление из кеша виджета фильтра
		// не используется на данный момент
		// $cache->delete('widgetFilter');
		// удаление из кеша фильтра
		$cache->delete('filter');
	}


	/**
	 * Поля
	 * @param boolean $arr
	 * @param integer $id
	 * @param boolean $all
	 * @return array|string 
	 */
	public function idObjectFields($arr = true, $id = null, $all = true) {
		if(empty(self::$_objectFields)) {
			self::$_objectFields[''] = \Yii::t('nav', 'ALL');

			$sql = "SELECT idobj_fields, name 
				FROM obj_fields 
				ORDER BY name";
			$dataReader = \Yii::app()->db->createCommand($sql)->query();

			while(($data = $dataReader->read()) !== false)
				self::$_objectFields[$data['idobj_fields']] = $data['name'];
		}

		if($all) {
			if($arr) return self::$_objectFields;

			return @self::$_objectFields[$id];
		}
		else {
			$_objectFields = self::$_objectFields;
			unset($_objectFields['']);

			if($arr) return $_objectFields;

			return @$_objectFields[$id];
		}
	}


	/**
	 * Типы полей
	 * @param boolean $arr
	 * @param integer $id
	 * @param boolean $all
	 * @return array|string 
	 */
	public function idObjectType($arr = true, $id = null, $all = true) {
		if(empty(self::$_objectType)) {
			self::$_objectType[''] = \Yii::t('nav', 'ALL');

			$sql = "SELECT idobj_type, name 
				FROM obj_type 
				ORDER BY name";
			$dataReader = \Yii::app()->db->createCommand($sql)->query();

			while(($data = $dataReader->read()) !== false)
				self::$_objectType[$data['idobj_type']] = $data['name'];
		}

		if($all) {
			if($arr) return self::$_objectType;

			return @self::$_objectType[$id];
		}
		else {
			$_objectType = self::$_objectType;
			unset($_objectType['']);

			if($arr) return $_objectType;

			return @$_objectType[$id];
		}
	}


	/**
	 * Группы связей
	 * @param boolean $arr
	 * @param integer $id
	 * @param boolean $all
	 * @return array|string 
	 */
	public function idTiesGroups($arr = true, $id = null, $all = true) {
		if(empty(self::$_tiesGroups)) {
			self::$_tiesGroups[''] = \Yii::t('nav', 'ALL');
			self::$_tiesGroups[0] = \Yii::t('nav', 'NO');

			$sql = "SELECT idobj_ties_groups, name 
				FROM obj_ties_groups 
				ORDER BY name";
			$dataReader = \Yii::app()->db->createCommand($sql)->query();

			while(($data = $dataReader->read()) !== false)
				self::$_tiesGroups[$data['idobj_ties_groups']] = $data['name'];
		}

		if($all) {
			if($arr) return self::$_tiesGroups;

			return @self::$_tiesGroups[$id];
		}
		else {
			$_tiesGroups = self::$_tiesGroups;
			unset($_tiesGroups['']);

			if($arr) return $_tiesGroups;

			return @$_tiesGroups[$id];
		}
	}


	/**
	 * Использование связи в фильтре
	 * @param boolean $arr
	 * @param integer $id
	 * @param boolean $all
	 * @return array|string 
	 */
	public function filterList($arr = true, $id = null, $all = true) {
		self::$_filterList = array(
			'' => \Yii::t('admin', 'TIES_FILTER_ALL'),
			0 => \Yii::t('admin', 'TIES_FILTER_NONE'),
			1 => \Yii::t('admin', 'TIES_FILTER_CATEGORY'),
			2 => \Yii::t('admin', 'TIES_FILTER_MAIN'),
			3 => \Yii::t('admin', 'TIES_FILTER_MAIN_AND_CATEGORY'),
		);

		if($all) {
			if($arr) return self::$_filterList;

			return @self::$_filterList[$id];
		}
		else {
			$_filterList = self::$_filterList;
			unset($_filterList['']);

			if($arr) return $_filterList;

			return @$_filterList[$id];
		}
	}


	/**
	 * Типы полей
	 * @param boolean $arr
	 * @param integer $id
	 * @param boolean $all
	 * @return array|string 
	 */
	function fieldsTypeList($arr = true, $id = null, $all = true) {
		if(empty(self::$_fieldsType)) {
			self::$_fieldsType[''] = \Yii::t('nav', 'ALL');

			$sql = "SELECT idobj_fields, type 
				FROM obj_fields 
				ORDER BY name";
			$dataReader = \Yii::app()->db->createCommand($sql)->query();

			while(($data = $dataReader->read()) !== false)
				self::$_fieldsType[$data['idobj_fields']] = $data['type'];
		}

		if($all) {
			if($arr) return self::$_fieldsType;

			return @self::$_fieldsType[$id];
		}
		else {
			$_fieldsType = self::$_fieldsType;
			unset($_fieldsType['']);

			if($arr) return $_fieldsType;

			return @$_fieldsType[$id];
		}	
	}


	/**
	 * Поддержка фильтрации полем
	 * @return boolean
	 */
	function filtredField() {
		$type = $this->fieldsTypeList(false, $this->idobj_fields, false);
		$manager = new \app\managers\Manager(\app\managers\Manager::ACCESS_TYPE_EDIT);
		$field = $manager->create($type);


		return $field->getIsFiltered();
	}


	/**
	 * Типы объявлений, в которых нету запрашиваемого поля
	 * @param integer $field
	 * @return string 
	 */
	function getTypeFieldData($field) {
		$values = array();

		$sql = 'SELECT ot.idobj_type, ot.name 
			FROM obj_type ot
			LEFT JOIN obj_ties t ON t.idobj_type = ot.idobj_type
			WHERE ot.idobj_type NOT IN (
				SELECT idobj_type 
				FROM obj_ties 
				WHERE idobj_fields = :id
			)
			GROUP BY ot.idobj_type, ot.name';

		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':id', $field, \PDO::PARAM_INT);
		$dataReader = $command->query();

		while(($data = $dataReader->read()) !== false)
			$values[$data['idobj_type']] = $data['name'];

		if(empty($values)) $values = array(0 => \Yii::t('nav', 'NO'));


		return $values;
	}


	/**
	 * Поля объявлений которых нет в запрашиваемом типе
	 * @param integer $type
	 * @return string 
	 */
	function getFieldsFieldData($type) {
		$values = array();

		$sql = 'SELECT af.idobj_fields, af.name 
			FROM obj_fields af
			LEFT JOIN obj_ties t ON t.idobj_fields = af.idobj_fields
			WHERE af.idobj_fields NOT IN (
				SELECT idobj_fields 
				FROM obj_ties 
				WHERE idobj_type = :id
			)';

		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':id', $type, \PDO::PARAM_INT);
		$dataReader = $command->query();

		while(($data = $dataReader->read()) !== false)
			$values[$data['idobj_fields']] = $data['name'];

		if(empty($values)) $values = array(0 => \Yii::t('nav', 'NO'));


		return $values;
	}


	/**
	 * Множественное удаление
	 * @param array $params
	 * @return boolean
	 * @throws \CHttpException нет прав на удаление или нет параметров для удаления
	 */
	public static  function deleteMany($params = array()) {
		if(!\Yii::app()->user->checkAccess('moder'))
			throw new \CHttpException(403, \Yii::t('main', 'ERROR_NOT_PERMISSION_FOR_DEL_TIES'));

		if(empty($params))
			throw new \CHttpException(500, \Yii::t('main', 'ERROR_NOT_ALLOW_DEL_All_TIES'));


		$where = $whereArray = array();

		if(!empty($params['type'])) {
			if(is_array($params['type'])) $whereArray[] = 't.idobj_type IN (' . implode(', ', $params['type']) . ')';
			else $where['type'] = 't.idobj_type = :type';
		}

		if(!empty($params['fields'])) {
			if(is_array($params['fields'])) $whereArray[] = 't.idobj_fields IN (' . implode(', ', $params['fields']) . ')';
			else $where['fields'] = 't.idobj_fields = :fields';
		}

		// удаление из кеша
		$sql = 'SELECT idobj_type 
			FROM obj_ties t 
			WHERE ' . implode(' AND ', array_merge($where, $whereArray)) . '
			GROUP BY idobj_type';
		$command = \Yii::app()->db->createCommand($sql);
		foreach($where as $id => $value)
			$command->bindParam(':' . $id, $params[$id], \PDO::PARAM_INT);

		$dataReader = $command->query();
		$cache = \Yii::app()->cache;
		while(($data = $dataReader->read()) !== false) {
			$cache->delete("fieldsType-read-{$data['idobj_type']}");
			$cache->delete("fieldsType-edit-{$data['idobj_type']}");
		}

		// удаление из кеша виджета фильтра
		// не используется на данный момент
		// $cache->delete('widgetFilter');
		// удаление из кеша фильтра
		$cache->delete('filter');

		$sql = 'DELETE FROM obj_ties t 
			WHERE ' . implode(' AND ', array_merge($where, $whereArray));
		$command = \Yii::app()->db->createCommand($sql);
		foreach($where as $id => $value)
			$command->bindParam(':' . $id, $params[$id], \PDO::PARAM_INT);

		if($command->execute()) return true;
		else return false;
	}


}