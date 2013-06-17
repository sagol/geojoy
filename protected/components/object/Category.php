<?php

namespace app\components\object;

/**
 * Категории сайта и работа с ними
 */
class Category {


	/**
	 * Единственный экземпляр класса
	 * @var \app\components\object\Category
	 */
	private static $_instanse = null;
	/**
	 * Массив имени категорий виде 'путь категории' => 'код дерева'
	 * @var array 
	 */
	protected $_code = array();
	/**
	 * Массив имени категорий виде 'код дерева' => array(его свойства)
	 * @var array 
	 */
	protected $_data = array();
	/**
	 * Массив имени категорий виде 'id категории' => & array(его свойства)
	 * массив - ссылка на массив $_data
	 * @var array 
	 */
	protected $_id = array();
	/**
	 * Массив для меню категорий
	 * @var array 
	 */
	protected $_menu = array();
	/**
	 * Текущая категория в формате дерева
	 * @var string 
	 */
	protected $_curCategory;
	/**
	 * Хранит состояние вывода категорий на карту
	 * @var string 
	 */
	protected $_useMap = false;


	/**
	 * Создает и возвращает единственный экземпляр класса
	 * @return \app\components\object\Category 
	 */
	static function getInstanse() {
		if(self::$_instanse == null) {
			$paramsCategory = \Yii::app()->params['cache']['category'];
			if($paramsCategory !== -1) {
				// получение из кеша
				$cache = \Yii::app()->cache;
				$category = $cache->get('category');

				if($category !== false) {
					self::$_instanse = $category;
					return self::$_instanse;
				}
			}

			self::$_instanse = new Category;

			// сохранение в кеш
			if($paramsCategory !== -1) $cache->set('category', self::$_instanse, $paramsCategory);
		}


		return self::$_instanse;
	}


	/**
	 * Делает выборку категорий из базы и заполняем переменые 
	 */
	public function __construct() {
		// главная категория
		$this->_data['main'] = array(
			'tree' => 'main',
			'name' => 'NLS_MAIN_CATEGORY',
			'alias' => 'main',
			'disabled' => 0,
			'type' => 'main',
			'path' => 'main',
			'menu' => array(
				'label' => 'NLS_MAIN_CATEGORY',
				'url' => array('/site/objects/index'),
			)
		);
		$this->_code['main'] = 'main';

		$db = \Yii::app()->db;
		$sql = "SELECT * 
			FROM obj_category 
			WHERE disabled = 0 
			ORDER BY tree";
		$dataReader = $db->createCommand($sql)->query();
		$menu['items'] = array();

		while(($data = $dataReader->read()) !== false) {
			$parent = substr($data['tree'], 0, -2);

			if($parent && @$this->_data[$parent]['path']) {
				$path =  $this->_data[$parent]['path'] . '/' . $data['alias'];
				$breadcrumbs = $this->_data[$parent]['breadcrumbs'];
				$breadcrumbs[$data['name']] = array('/site/objects/category', 'params' => $data['tree']);
			}
			else {
				$path = $data['alias'];
				$breadcrumbs = array($data['name'] => array('/site/objects/category', 'params' => $data['tree']));
			}

			$this->_code[$path] = $data['tree'];
			$data['breadcrumbs'] = $breadcrumbs;
			$data['id'] = $data['idobj_category'];
			$data['type'] = $data['idobj_type'];
			$data['path'] = $path;

			$request = new \CHttpRequest;
			$baseUrl = rtrim(dirname($request->getScriptUrl()), '\\/');
			unset($request);
			$data['img'] = $baseUrl . \Yii::app()->params['uploadUrl'] . $data['img'];
			unset($data['idobj_type'], $data['idobj_category']);

			$menu[$data['tree']] = array('label' => $data['name'], 'url' => $breadcrumbs[$data['name']]);
			if($parent) $menu[$parent]['items'][$data['tree']] = &$menu[$data['tree']];
			else $menu['items'][$data['tree']] = &$menu[$data['tree']];
			$data['menu'] = $menu[$data['tree']];

			$this->_data[$data['tree']] = $data;
			$this->_id[$data['id']] = &$this->_data[$data['tree']];
		}

		$this->_menu = $menu['items'];
		unset($db, $dataReader, $breadcrumbs, $menu);
	}


	/**
	 * Возвращает код категории или всех категорий виде array('путь категории' => 'код дерева')
	 * @param string $path
	 * @return mixed 
	 */
	public function code($path = null) {
		if($path !== null) return @$this->_code[$path];

		return $this->_code;
	}


	/**
	 * Возвращает данные запрошеной категории или всех категорий
	 * @param string $id
	 * @param string $param
	 * @return mixed 
	 */
	public function id($id = null, $param = null) {
		if($id !== null) {
			if($param !== null) return @$this->_id[$id][$param];
			else {
				if(isset($this->_id[$id])) return $this->_id[$id];
				else return array();
			}
		}

		return $this->_id;
	}


	/**
	 * Возвращает данные запрошеной категории или всех категорий
	 * @param string $id
	 * @param string $param
	 * @return mixed 
	 */
	public function data($id = null, $param = null) {
		if($id !== null) {
			if($param !== null) return @$this->_data[$id][$param];
			else {
				if(isset($this->_data[$id])) return $this->_data[$id];
				else return array();
			}
		}

		return $this->_data;
	}


	/**
	 * Возвращает данные текущей категории категории или всех категорий
	 * @param string $id
	 * @param string $param
	 * @return mixed 
	 */
	public function curData($param = null) {
		if($param !== null) return @$this->_data[$this->_curCategory][$param];
		else return @$this->_data[$this->_curCategory];
	}

	/**
	 * Возвращает структуру меню с подменю дочерних категорий
	 * @param string $id
	 * @return mixed 
	 */
	public function menu($id = null) {
		if($id !== null) return @$this->_menu[$id];

		return $this->_menu;
	}


	/**
	 * Возвращает текущию категорию (main - главная категория, остальные в виде 010201)
	 * @return string 
	 */
	public function getCurCategory() {
		return $this->_curCategory;
	}


	/**
	 * Устанавливает текущию категорию (main - главная категория, остальные в виде 010201)
	 * @param string
	 */
	public function setCurCategory($category) {
		$this->_curCategory = $category;
	}


	/**
	 * Возвращает состояние вывода категорий на карту
	 * @return string 
	 */
	public function getUseMap() {
		return $this->_useMap;
	}


	/**
	 * Устанавливает состояние вывода категорий на карту
	 * @param string
	 */
	public function setUseMap($useMap) {
		$this->_useMap = $useMap;
	}


}