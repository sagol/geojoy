<?php

namespace app\managers;

abstract class Managers extends \CComponent {


	/**
	 * Единственный экземпляр класса
	 * @var Filter 
	 */
	protected static $_instanse = null;

	protected $_setName;

	protected $_mainTable;
	protected $_mainTableAlias;
	protected $_join = array();

	protected $_managersFilter = array();
	protected $_managersFilterAllLoaded = false;
	protected $_filterParams = array();
	protected $_filterWhere;

	protected $_filterSelect;
	protected $_filterJoins = array();
	protected $_replacedFields = array();


	abstract protected function loadManagersFilter($set);
	abstract protected function loadManagersFilterAll();

	public function __construct() {
		$this->init();
	}


	public function setSetName($value) {
		$this->_setName = $value;
	}


	public function getSetName() {
		return $this->_setName;
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


	public function setFilterSelect($value) {
		$this->_filterSelect = $value;
	}


	public function getFilterSelect() {
		return $this->_filterSelect;
	}


	public function setFilterJoins(array $value) {
		$this->_filterJoins = $value;
	}


	public function getFilterJoins() {
		return $this->_filterJoins;
	}


	public function setReplacedFields(array $value) {
		$this->_replacedFields = $value;
	}


	public function getReplacedFields() {
		return $this->_replacedFields;
	}


	public function init() {
		if(empty($this->_setName)) throw new \CException(Yii::t('main', 'ERROR_IS_NOT_SET_PARAMETER_SET_NAME'));
		if(empty($this->_mainTable)) throw new \CException(Yii::t('main', 'ERROR_IS_NOT_SET_PARAMETER_MAIN_TABLE'));
	}

	public function manager($set, $type = '') {
		//задан не правильный set
		if(empty($set)) return false;
		$manadger = &$this->{'_managers' . ucfirst($type)};
		if(empty($manadger[$set])) $this->loadManagersFilterAll();

		return $manadger[$set];
	}

	/**
	 * TODO:
	 * сделать кеширование критериев запроса и обновление значений
	 * 
	 * нет поддержки:
	 * работы с полями из 2-х таблиц
	 * работы с полями хранящими значения в 2-х массивах одной или двух таблиц
	 */
	public function filter($set, array $queryParams) {
		if(empty($this->_managersFilter[$set])) $loadManagersFilterAll = $this->loadManagersFilterAll();
		if($set == 'main') return $this->filterAll($queryParams);

		if(empty($queryParams['criteria'])) $criteria = array($this->_setName => $set);
		else $criteria = array(array($this->_setName => $set), array($queryParams['criteria']));

		if(isset($queryParams['needCount'])) $needCount = $queryParams['needCount'];
		else $needCount = false;
		if(isset($queryParams['skipFilter'])) $skipFilter = $queryParams['skipFilter'];
		else $skipFilter = false;
		if(isset($queryParams['returnWhere'])) $returnWhere = $queryParams['returnWhere'];
		else $returnWhere = false;
		if(isset($queryParams['skipFields'])) $skipFields = $queryParams['skipFields'];
		else $skipFields = false;

		// поля не загружены, если не задан пропуск полей, то они нужны для запроса
		if(isset($loadManagersFilterAll) && !$loadManagersFilterAll && !$skipFields) return false;
		// менеджер полей загружен, а полей нету, если не задан пропуск полей, то они нужны для запроса
		elseif($this->_managersFilter[$set]->fieldsCount() == 0 && !$skipFields) return false;

		if(empty($queryParams['keepSql']) || !$queryParams['keepSql']) {
			$params = $names = array();
			$this->_filterNames($criteria, $names);

			// перебираем названия полей с массивами их условий. условия передаются по ссылке
			if(!empty($names))
				foreach($names as $name => &$paramsNames) {
					foreach($paramsNames as $id => &$param) {
						if(!$skipFields) {
							if($this->_managersFilter[$set]->hasField($name))
								$this->_managersFilter[$set]->field($name)->createCondition($param, $names, $params);
						}

						if(!isset($param['condition'])) $this->_filterCondition($param, $names, $params);
					}
				}

			if(!empty($criteria)) $where = $this->_filterConditionsSql($criteria);
			if(!$skipFilter) {
				$filterWhere = \app\components\object\Filter::getInstanse()->where();
				if(!empty($filterWhere)) {
					if(empty($where)) $where = $filterWhere;
					else $where .= ' AND ' . $filterWhere;
					$params = array_merge($params, \app\components\object\Filter::getInstanse()->params());
				}
			}

			if($returnWhere) return array($where, $params);
			$this->_filterWhere = $where;
			$this->_filterParams = $params;
		}
		// включен режим хранить запрос и отдать sql where и параметры запроса
		elseif($returnWhere) return array($this->_filterWhere, $this->_filterParams);
		// включен режим хранить запрос
		else {
			$where = $this->_filterWhere;
			$params = $this->_filterParams;
		}

		if(!$needCount && isset($queryParams['limit'])) {
			$limit = ' LIMIT :limit';
			if(isset($queryParams['page']) && !isset($queryParams['offset'])) $queryParams['offset'] = $queryParams['limit']*($queryParams['page']-1);
			if(isset($queryParams['offset'])) $limit .= ' OFFSET :offset';
		}
		else $limit = '';

		if(isset($queryParams['orderBy'])) {
			$orderBy = ' ORDER BY ' . $queryParams['orderBy'];
		}
		else $orderBy = '';

		if($needCount) 
			$sql = "SELECT COUNT(*) 
				FROM $this->_mainTable $this->_mainTableAlias " . 
				(!empty($this->_filterJoins) ? ' LEFT JOIN ' . implode(' LEFT JOIN ', $this->_filterJoins) : '') .
				(!empty($where) ?  ' WHERE ' . $where : '') ;
		else $sql = "SELECT $this->_filterSelect 
				FROM $this->_mainTable $this->_mainTableAlias " . 
				(!empty($this->_filterJoins) ? ' LEFT JOIN ' . implode(' LEFT JOIN ', $this->_filterJoins) : '') .
				(!empty($where) ?  ' WHERE ' . $where : '') . 
				$orderBy . 
				$limit;
		$command = \Yii::app()->db->createCommand($sql);

		if(!$needCount && isset($queryParams['limit'])) {
			$command->bindParam(':limit', $queryParams['limit'], \PDO::PARAM_INT);
			if(isset($queryParams['page']) && !isset($queryParams['offset'])) $queryParams['offset'] = $limit*($page-1);
			if(isset($queryParams['offset'])) $command->bindParam(':offset', $queryParams['offset'], \PDO::PARAM_INT);
		}

		foreach($params as $param => $value) {
			if(is_numeric($value) && !is_string($value)) {
				$command->bindValue($param, $value, \PDO::PARAM_INT);
			}
			else {
				$command->bindValue($param, $value, \PDO::PARAM_STR);
			}

			/* для жесткой отладки передаваемого типа значения и создаваемого запроса
			if(is_numeric($value) && !is_string($value)) echo "INT $param $value\n";
			else echo "STR $param $value\n";
			if(is_numeric($value) && !is_string($value)) $sql = str_replace($param, $value, $sql);
			else $sql = str_replace($param, "'$value'", $sql);*/
		}
		/*echo "$sql\n";*/

		$dataReader = $command->query();
		if($needCount) {
			if(($data = $dataReader->read()) !== false) {
				$return = $data['count'];
			}
			else $return = 0;
		}
		else {
			$return = array();
			while(($data = $dataReader->read()) !== false)
				$return[] = $data;
		}


		return $return;
	}


	public function filterAll(array $queryParams) {
		$loadManagersFilterAll = $this->loadManagersFilterAll();

		if(empty($queryParams['criteria'])) $criteria = array();
		else $criteria = $queryParams['criteria'];

		if(isset($queryParams['needCount'])) $needCount = $queryParams['needCount'];
		else $needCount = false;
		if(isset($queryParams['skipFilter'])) $skipFilter = $queryParams['skipFilter'];
		else $skipFilter = false;
		if(isset($queryParams['returnWhere'])) $returnWhere = $queryParams['returnWhere'];
		else $returnWhere = false;
		if(isset($queryParams['skipFields'])) $skipFields = $queryParams['skipFields'];
		else $skipFields = false;

		if(!$loadManagersFilterAll && !$skipFields) return false;

		if(empty($queryParams['keepSql']) || !$queryParams['keepSql']) {
			$params = $names = array();
			$this->_filterNames($criteria, $names);

			// перебираем названия полей с массивами их условий. условия передаются по ссылке
			if(!empty($names))
				foreach($names as $name => &$paramsNames) {
					foreach($paramsNames as $id => &$param) {
						if(!$skipFields) {
							$or = array();
							foreach($this->_managersFilter as $type => $fieldsManager) {
								if($type == 'main') continue;
								if($fieldsManager->hasField($name)) {
									if($this->_managersFilter['main']->hasField($name))
									$fieldsManager->field($name)->setValue($this->_managersFilter['main']->field($name)->getValue());
									$fieldsManager->field($name)->createCondition($param, $names, $params);
									
									if(!empty($param['condition'])) $or[] = 'o.idobj_type = ' . $type . ' AND ' . $param['condition'];
									unset($param['condition']);
								}
							}
							if(!empty($or)) $param['condition'] = '(' . implode(' OR ', $or) . ')';
						}

						if(!isset($param['condition'])) $this->_filterCondition($param, $names, $params);
					}
				}

			if(!empty($criteria)) $where = $this->_filterConditionsSql($criteria);
			if(!$skipFilter) {
				$filterWhere = \app\components\object\Filter::getInstanse()->where();
				if(!empty($filterWhere)) {
					if(empty($where)) $where = $filterWhere;
					else $where .= ' AND ' . $filterWhere;
					$params = array_merge($params, \app\components\object\Filter::getInstanse()->params());
				}
			}

			if($returnWhere) return array($where, $params);
			$this->_filterWhere = $where;
			$this->_filterParams = $params;
		}
		// включен режим хранить запрос и отдать sql where и параметры запроса
		elseif($returnWhere) return array($this->_filterWhere, $this->_filterParams);
		// включен режим хранить запрос
		else {
			$where = $this->_filterWhere;
			$params = $this->_filterParams;
		}

		if(!$needCount && isset($queryParams['limit'])) {
			$limit = ' LIMIT :limit';
			if(isset($queryParams['page']) && !isset($queryParams['offset'])) $queryParams['offset'] = $queryParams['limit']*($queryParams['page']-1);
			if(isset($queryParams['offset'])) $limit .= ' OFFSET :offset';
		}
		else $limit = '';

		if(isset($queryParams['orderBy'])) {
			$orderBy = ' ORDER BY ' . $queryParams['orderBy'];
		}
		else $orderBy = '';

		if($needCount) 
			$sql = "SELECT COUNT(*) 
				FROM $this->_mainTable $this->_mainTableAlias " . 
				(!empty($this->_filterJoins) ? ' LEFT JOIN ' . implode(' LEFT JOIN ', $this->_filterJoins) : '') .
				(!empty($where) ?  ' WHERE ' . $where : '') ;
		else $sql = "SELECT $this->_filterSelect 
				FROM $this->_mainTable $this->_mainTableAlias " . 
				(!empty($this->_filterJoins) ? ' LEFT JOIN ' . implode(' LEFT JOIN ', $this->_filterJoins) : '') .
				(!empty($where) ?  ' WHERE ' . $where : '') . 
				$orderBy . 
				$limit;
		$command = \Yii::app()->db->createCommand($sql);

		if(!$needCount && isset($queryParams['limit'])) {
			$command->bindParam(':limit', $queryParams['limit'], \PDO::PARAM_INT);
			if(isset($queryParams['page']) && !isset($queryParams['offset'])) $queryParams['offset'] = $limit*($page-1);
			if(isset($queryParams['offset'])) $command->bindParam(':offset', $queryParams['offset'], \PDO::PARAM_INT);
		}

		foreach($params as $param => $value) {
			if(is_numeric($value) && !is_string($value)) {
				$command->bindValue($param, $value, \PDO::PARAM_INT);
			}
			else {
				$command->bindValue($param, $value, \PDO::PARAM_STR);
			}

			/* для жесткой отладки передаваемого типа значения и создаваемого запроса
			if(is_numeric($value) && !is_string($value)) echo "INT $param $value\n";
			else echo "STR $param $value\n";
			if(is_numeric($value) && !is_string($value)) $sql = str_replace($param, $value, $sql);
			else $sql = str_replace($param, "'$value'", $sql);*/
		}
		/*echo "$sql\n";*/

		$dataReader = $command->query();
		if($needCount) {
			if(($data = $dataReader->read()) !== false) {
				$return = $data['count'];
			}
			else $return = 0;
		}
		else {
			$return = array();
			while(($data = $dataReader->read()) !== false)
				$return[] = $data;
		}


		return $return;
	}


	protected function _filterNames(&$criteria, &$names) {
		static $signs = array(
			'!()', // NOT IN ()
			'()', // IN ()

			'!><', // NOT IS NULL

			'>=', '<=',
			'!=', '<>',
			'|~', // LIKE text%
			'~|', // LIKE %text
			'><', // IS NULL

			'>', '<',
			'=',
			'~', // LIKE %text%
		);

		if(is_array($criteria)) {
			$count = count($criteria);
			$i = 0;
			foreach($criteria as $id => $value) {
				if($i == $count) break;
				$i++;
				if(is_array($value) && empty($value['param'])) $this->_filterNames($criteria[$id], $names);
				else {
					if(is_numeric($id)) {
						$name = $value;
						$criteria[$id] = array();
					}
					else {
						$name = $id;
						$criteria[] = array();
						end($criteria);
						$id = key($criteria);

						$isArray = is_array($value);
						if($isArray && isset($value['param'])) unset($value['param']);
						if($isArray && isset($value['function'])) {
							$criteria[$id]['function'] = $value['function'];
							unset($value['function']);
						}
						if($isArray && isset($value['value'])) $criteria[$id]['value'] = $value['value'];
						else $criteria[$id]['value'] = $value;
						unset($criteria[$name]);
					}

					$test = array(
						substr($name, 0, 3),
						substr($name, 0, 2),
						substr($name, 0, 1),
					);
					$sign = array_intersect($test, $signs);
					if(empty($sign)) $sign = '=';
					else {
						$sign = current($sign);
						$name = substr($name, strlen($sign));
					}

					$criteria[$id]['name'] = $name;
					$criteria[$id]['sign'] = $sign;
					$names[$name][] = &$criteria[$id];
				}
			}
		}
	}


	protected function _filterCondition(&$param, $names, &$values) {
		static $signs = array(
			'!()' => ':in__', // NOT IN ()
			'()' => ':not_in__', // IN ()

			'!><' => '', // NOT IS NULL
			'><' => '', // IS NULL

			'>=' => ':more_equal__',
			'<=' => ':less_equal__',

			'!=' => ':not_equally__',
			'<>' => ':not_equally__',

			'|~' => ':match_first__', // LIKE text%
			'~|' => ':match_last__', // LIKE %text

			'>' => ':more__',
			'<' => ':less__',

			'=' => ':equally__',
			'~' => ':match_overlap__', // LIKE %text%
		);

		$name = $param['name'];
		$sign = $param['sign'];

		$setParam = $signs[$sign] . $name . '_' . (empty($names[$name]) ? 0 : count($names[$name]));
		if(array_key_exists($name, $this->_replacedFields)) $name = $this->_replacedFields[$name];
		if(isset($param['function'])) $function = $param['function'];
		else $function = $name;

		switch($sign) {
			case '!><':
				$param['condition'] = 'NOT ' . $function . ' IS NULL';
				break;
			case '><':
				$param['condition'] = $function . ' IS NULL';
				break;
			case '!()':
				if(isset($param['value'])) {
					$i = 0;
					foreach($param['value'] as $value) {
						$condition[] = $setParam . '_' . $i;
						$values[$setParam . '_' . $i] = $value;
						$i++;
					}
					$param['condition'] = $function . ' NOT IN (' . implode(', ', $condition) . ')';
					unset($condition, $i);
				}
				break;
			case '()':
				if(isset($param['value'])) {
					$i = 0;
					foreach($param['value'] as $value) {
						$values[$setParam . '_' . $i] = $value;
						$i++;
					}
					$param['condition'] = $function . ' IN (' . implode(', ', (array)$param['value']) . ')';
				}
				break;
			case '|~':
				if(isset($param['value'])) {
					$param['condition'] = $function . ' LIKE ' . $setParam;
					$values[$setParam] = $param['value'] . '%';
				}
				break;
			case '~|':
				if(isset($param['value'])) {
					$param['condition'] = $function . ' LIKE ' . $setParam;
					$values[$setParam] = '%' . $param['value'];
				}
				break;
			case '~':
				if(isset($param['value'])) {
					$param['condition'] = $function . ' LIKE ' . $setParam;
					$values[$setParam] = '%' . $param['value'] . '%';
				}
				break;
			case '!=':
				if(isset($param['value'])) {
					$param['condition'] = 'NOT ' . $function . ' = ' . $setParam;
					$values[$setParam] = $param['value'];
				}
				break;
			default:
				if(isset($param['value'])) {
					$param['condition'] = $function . ' ' . $sign . ' ' . $setParam;
					$values[$setParam] = $param['value'];
				}
				else $param['condition'] = false;
		}
	}


	protected function _filterConditionsSql($conditions, $operand = 'AND') {
		if(!empty($conditions['OR'])) {
			$return[] = $this->_filterConditionsSql($conditions['OR'], 'OR');
			unset($conditions['OR']);
		}
		if(!empty($conditions['AND'])) {
			$return[] = $this->_filterConditionsSql($conditions['AND']);
			unset($conditions['AND']);
		}

		foreach($conditions as $id => $condition) {
			// не задан операнд по умолчанию (AND)
			if(empty($condition)) continue;
			elseif(is_array($condition) && !isset($condition['condition'])) {
				$return[] = $this->_filterConditionsSql($condition);
				unset($conditions[$id]);
				continue;
			}
			elseif(isset($condition['condition']) && $condition['condition']) $return[] = $condition['condition'];
		}
		if(empty($return)) $return = '';
		else $return = '(' . implode(' ' . $operand . ' ', $return) . ')';


		return $return;
	}


}