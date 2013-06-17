<?php

namespace app\components\object;

/**
 * Обрабатывает параметры переданые в урле для фильтрации объявлений
 */
class Filter {


	/**
	 * Единственный экземпляр класса
	 * @var Filter 
	 */
	private static $_instanse = null;
	/**
	 * Массив полей объявлений в формате массив[категория объявлений][имя поля] = ссылка на массив типов объявлений в 
	 * формате массив[имя поля][тип объявлений] = индекс поля в этом типе объявлений
	 * @var array 
	 */
	protected $_fieldsMap = array();
	/**
	 * Массив параметров переданых фильтру
	 * @var array 
	 */
	protected static $_params = array();
	/**
	 * sql строка параметров переданых фильтру
	 * @var string 
	 */
	protected static $_where = null;
	/**
	 * sql строка параметров переданых фильтру
	 * @var string 
	 */
	protected static $_values = null;

	/**
	 * Массив роутеров где допускается работа фильтра ('site/objects/index')
	 * @var array
	 */
	protected $_allowRoute = array(
		'site/objects/index',
		'site/objects/category',
		'site/objects/indexMap',
		'site/objects/categoryMap',
	);


	/**
	 * Создает и возвращает единственный экземпляр класса
	 * @return Filter 
	 */
	static function getInstanse() {
		if(self::$_instanse == null) {
			$paramsFilter = \Yii::app()->params['cache']['filter'];
			if($paramsFilter !== -1) {
				// получение из кеша
				$cache = \Yii::app()->cache;
				self::$_instanse = $cache->get('filter');

				if(self::$_instanse !== false) {
					self::$_instanse->_init();
					return self::$_instanse;
				}
			}

			self::$_instanse = new Filter;
			$init = self::$_instanse->_init();
			// сохранение в кеш
			if($init && $paramsFilter !== -1) $cache->set('filter', self::$_instanse, $paramsFilter);
		}


		return self::$_instanse;
	}


	/**
	 * Инициализация данных фильтра сайта
	 * @return boolean возвращает false, если нет параметров 
	 */
	protected function _init() {
		if(!in_array(\Yii::app()->getCurrentRoute(), $this->_allowRoute)) return false;


		$categoryTree = \app\components\object\Category::getInstanse()->getCurCategory();
		// выполняем есть категория (main или 01...)
		if($categoryTree) {
			$queryString = \Yii::app()->request->queryString;
			// разбираем строку запроса и получаем параметры фильтра
			parse_str($queryString, $params);
			if(isset($params['set'])) {
				$set = $params['set'];
				unset($params['set']);
			}
			else $set = false;
			$session = \Yii::app()->session;
			// получаем параметры фильтра из сессии пользователя
			$filterParams = $session->get('filterParams');	

			// параметр сохранения фильтра (из урла) в сессию пользователя
			if($set) {
				foreach($params as $name => $value) {
					if(empty($value)) unset($params[$name]);
					elseif(is_array($value)) {
						foreach($value as $subName => $subValue)
							if(empty($subValue)) unset($params[$name][$subName]);

						if(empty($params[$name])) unset($params[$name]);
					}
				}

				if(empty($params)) {
					unset($filterParams[$categoryTree]);
					$session->add('filterParams', $filterParams);
				}
				else {
					$filterParams[$categoryTree] = $params;
					$filterParams[$categoryTree][0] = \Yii::app()->getCurrentRoute();
					$session->add('filterParams', $filterParams);
				}
			
			}
			// если сохранение фильтра не задано и фильтр есть в сессии пользователя
			// редиректим на на урл с параметрами
			elseif(empty($params)) {
					$params = @$filterParams[$categoryTree];
					if(!empty($params) && count($params) > 1) {
						$route = $params[0];
						$params['params'] = $categoryTree == 'main' ? '' : $categoryTree;
						unset($params[0]);
						$url = \Yii::app()->createUrl($route, $params);
						\Yii::app()->getRequest()->redirect($url);
					}
					else return false;
			}
		}
		else return false;

		$categoryType = \app\components\object\Category::getInstanse()->curData('type');
		$managersObject = \app\managers\Objects::getInstanse();
		$fieldsManager = $managersObject->manager($categoryType, 'filter');
		foreach($params as $name => $value) {
			if(!$fieldsManager->hasField($name)) unset($params[$name]);
			if(is_array($value)) {
				foreach($value as $subParam => $subValue)
					if(empty($subValue)) unset($params[$name][$subParam]);
			}
			if(empty($params[$name])) unset($params[$name]);
		}

		if(empty($params)) return false;
		self::$_values = $params;

		$queryParams = array(
			'skipFilter' => true,
			'returnWhere' => true,
		);
		$category = \app\components\object\Category::getInstanse()->data($categoryTree);
		if($categoryTree != 'main') $queryParams['criteria']['idobj_category'] = $category['id'];
		foreach($params as $name => $value) {
			$fieldsManager->field($name)->setValue($value);
			$queryParams['criteria'][] = $name;
		}
		list(self::$_where, self::$_params) = $managersObject->filter($categoryType, $queryParams);


		return true;
	}


	/**
	 * Возвращает sql строку параметров переданых фильтру
	 * @param boolean $and
	 * @return string 
	 */
	public function where($and = false) {
		if(empty(self::$_where)) return false;

		return ($and ? ' AND (' . self::$_where : '(' .self::$_where) . ')';
	}


	/**
	 * Возвращает массив параметров переданых фильтру
	 * @return array 
	 */
	public function params() {
		return self::$_params;
	}


	/**
	 * Возвращает массив значений переданых фильтру
	 * @return array 
	 */
	public function values() {
		return self::$_values;
	}


}
