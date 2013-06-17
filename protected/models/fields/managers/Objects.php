<?php

namespace app\managers;

class Objects extends Managers {


	/**
	 * Создает и возвращает единственный экземпляр класса
	 * @return  
	 */
	static function getInstanse() {
		if(self::$_instanse == null) self::$_instanse = new Objects;


		return self::$_instanse;
	}


	public function init() {
		$this->setSetName('idobj_type');
		$this->setMainTable('objects');
		$this->setMainTableAlias('o');
		$this->setFilterSelect('o.*');

		$this->setReplacedFields(array(
			'moderate' => 'o.moderate',
			'idobj_category' => 'o.idobj_category',
			'idobj_type' => 'o.idobj_type',
		));


		parent::init();
	}


	protected function loadManagersFilter($set) {
		if(!empty($this->_managersFilter[$set])) return true;

		$paramsObjectFields = \Yii::app()->params['cache']['objectFields'];
		// если кеширование разрешено, получаем всех менеджеров полей
		if($paramsObjectFields !== -1) {
			$this->loadManagersFilterAll();
			if(empty($this->_managersFilter[$set])) return false;
		}

		// а если кеширование запрещено, делаем выборку из таблицы для конкретного менеджера полей
		$this->_managersFilter[$set] = new Object;
		$manager = $this->_managersFilter[$set];
		// ot.filter = 0 - не использовать в фильтрации / 1 - по категории / 2 - по главной / 3 - по главной + категории
		$sql = 'SELECT DISTINCT oc.idobj_type, ot.idobj_ties, ot.filter, of.idobj_fields AS id, of.name, of.title, of.type, of.parent, ot.params, ot.orders 
			FROM obj_ties ot 
			LEFT JOIN obj_fields of USING(idobj_fields) 
			LEFT JOIN obj_category oc USING(idobj_type) 
			WHERE oc.disabled = 0 AND ot.disabled = 0 AND (ot.filter = 1 OR ot.filter = 3) AND ' . $this->_setName. ' = :setName 
			ORDER BY oc.idobj_type, ot.orders';
		$command = \Yii::app()->db->createCommand($sql);
		if(is_numeric($set)) $command->bindValue(':setName', $set, \PDO::PARAM_INT);
		else $command->bindValue(':setName', $set, \PDO::PARAM_STR);

		$dataReader = $command->query();
		while(($data = $dataReader->read()) !== false) {
			$data['initOptions']['shotNameInForm'] = true;
			$manager->create($data['type'], $data);
		}

		if(!$manager->fieldsCount()) return false;

		return true;
	}


	protected function loadManagersFilterAll() {
		// предотвращение многократной загрузки
		if($this->_managersFilterAllLoaded) return true;

		// получение из кеша
		$paramsObjectFields = \Yii::app()->params['cache']['objectFields'];
		if($paramsObjectFields !== -1) {
			$cache = \Yii::app()->cache;
			$this->_managersFilter = $cache->get('objectFields-managersFilter');
			if($this->_managersFilter !== false) {
				foreach($this->_managersFilter as &$managersFilter)
					$managersFilter->initFromCache();

				$this->_managersFilterAllLoaded = true;
				return true;
			}
			else $this->_managersFilter = array();
		}

		// определяем кол-во типов объявлений разрешенных к выводу
		// если поле будет создано не для всех типов объявлений, его нельзя показывать на главной
		$sql = 'SELECT tree, idobj_type 
			FROM obj_category 
			WHERE disabled = 0 AND NOT idobj_type = 0';
		$dataReader = \Yii::app()->db->createCommand($sql)->query();

		$types = array();
		while(($data = $dataReader->read()) !== false)
			$types[$data['idobj_type']] = $data['idobj_type'];

		$allTypes = count($types);
		// создаем менеджера для каждого типа объявления
		foreach($types as $type)
			$this->_managersFilter[$type] = new Object(\app\managers\Manager::ACCESS_TYPE_EDIT);
		unset($types);

		// и один тип для главной страницы
		$this->_managersFilter['main'] = new Object(\app\managers\Manager::ACCESS_TYPE_EDIT);

		// выбираем все поля, разрешенные для выводе в фильтре
		$sql = 'SELECT DISTINCT oc.idobj_type, ot.idobj_ties, ot.filter, of.idobj_fields AS id, of.name, of.title, of.type, of.parent, ot.params, ot.orders 
			FROM obj_ties ot 
			LEFT JOIN obj_fields of USING(idobj_fields) 
			LEFT JOIN obj_category oc USING(idobj_type) 
			WHERE oc.disabled = 0 AND ot.disabled = 0 AND NOT ot.filter = 0 
			ORDER BY oc.idobj_type, ot.orders';
		$dataReader = \Yii::app()->db->createCommand($sql)->query();

		while(($data = $dataReader->read()) !== false)
			$fields[$data['name']][$data['idobj_type']] = $data;

		if(empty($fields)) return false;

		// создание полей для вывода в фильтре
		foreach($fields as $name => $data)
			foreach($data as $type => $field) {
				$field['initOptions']['shotNameInForm'] = true;
				// filter = 0 - не использовать / 1 - по категории / 2 - по главной / 3 - по главной + категории
				if($field['filter'] == 1 || $field['filter'] == 3)
					$this->_managersFilter[$field['idobj_type']]->create($field['type'], $field);
				if(($field['filter'] == 2 || $field['filter'] == 3) && count($data) == $allTypes)
					$this->_managersFilter['main']->create($field['type'], $field);
			}

		$this->_managersFilterAllLoaded = true;

		if($paramsObjectFields !== -1) \Yii::app()->cache->set('objectFields-managersFilter', $this->_managersFilter, $paramsObjectFields);


		return true;
	}


}