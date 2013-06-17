<?php

namespace app\managers;

class Object extends Manager {


	static function &manager($type, $fieldsAccessType) {
		// получение из кеша
		if(($fieldsManager = parent::fromCache($type, $fieldsAccessType)) !== false) return $fieldsManager;

		$fieldsManager = new Object($fieldsAccessType);
		// инициализация полей объявления
		$fieldsManager->_initFields($type);

		// сохранение в кеш
		$fieldsManager->toCache($type, $fieldsAccessType);


		return $fieldsManager;
	}


	public function init() {
		parent::init();

		$this->setMainTable('objects');
		$this->setMainTableAlias('o');
		$this->setMainTablePrimaryKey('idobjects');
		$this->setMainTableSequence('objects_idobjects_seq');

		$this->setFieldsSelectJoins(array(
			'obj_category oc USING(idobj_category)',
		));
	}


	/**
	 * Инициализация структуры полей
	 * @param integer $type
	 * @return boolean 
	 */
	protected function _initFields($type) {
		// получение полей объявления определенного типа
		if(!$this->fieldsCount()) {
			$type = (int)$type;

			$tiesGroups[0] = 'MAIN_GROUP';
			$sql = 'SELECT * 
				FROM obj_ties_groups
				ORDER BY orders';
			$dataReader = \Yii::app()->db->createCommand($sql)->query();
			while(($data = $dataReader->read()) !== false)
				$tiesGroups[$data['idobj_ties_groups']] = $data['name'];

			// обязательно наличие отключеных связей, для правильной распаковки данных
			// т.е. WHERE ot.disabled = 0 НЕЛЬЗЯ добавлять в этот запрос
			$sql = 'SELECT *, of.idobj_fields AS id 
				FROM obj_fields of 
				LEFT JOIN obj_ties ot USING(idobj_fields) 
				WHERE ot.idobj_type = :type 
				ORDER BY ot.idobj_ties';
			$command = \Yii::app()->db->createCommand($sql);
			$command->bindParam(':type', $type, \PDO::PARAM_INT);
			$dataReader = $command->query();
			
			while(($data = $dataReader->read()) !== false) {
				$this->create($data['type'], $data);
				// сортировка полей
				if(!$data['disabled']) {
					$orders[$data['name']] = $data['orders'];
					$groups[$tiesGroups[$data['idobj_ties_groups']]][$data['name']] = $data['orders'];
				}
			}

			// сортировка полей
			if(!empty($orders)) {
				asort($orders);

				foreach($orders as $field => $order)
					$this->createOrders($field);

				foreach($groups as $id => $group)
					asort($groups[$id]);

				foreach($groups as $id => $group)
					foreach($group as $field => $order)
						$this->createGroups($id, $field);


				unset($orders, $groups);
			}
		}


		return true;
	}


}